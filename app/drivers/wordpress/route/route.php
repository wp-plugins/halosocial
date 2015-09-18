<?php
/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */

/**
 * Global Asset manager
 */
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Generator\UrlGenerator;
 
class HALOURL extends Illuminate\Routing\UrlGenerator
{	
	/**
	 * Implement of route translation on Wordpress
	 * 
	 */
	public function to($url, $parameters = array(), $secure = null)
	{
		if($url == '/'){
			return get_page_link(halo_getPageId());
		}
		//if $url is not in halo url format then just apply the parent mapping
		if(!is_array($url) && strpos($url,'?') !== 0){		
			return parent::to($url,$parameters,$secure);
		}
		
		$noSEO = false;
		//check for seo params
		if(isset($parameters['noSEO'])) {
			$noSEO = true;
			unset($parameters['noSEO']);
		}
		
		$scheme = $this->getScheme($secure);
		
		$routes = HALORouter::getRoutes();

		$root = $this->getRootUrl($scheme);
		
		$context = new RequestContext('');
		$generator = new UrlGenerator($routes, $context);
		$par = array();
		if(strpos($url,'?') === 0){
			parse_str(substr($url,1),$par);
		} else if(is_array($url)){
			$par = $url;
		}
		if(isset($par['app']) && $par['app'] == 'admin') {
			//route to admin url, no need to apply seo
			unset($par['app']);
			$par = array('page' => 'halo_dashboard') + $par;
			$url = $generator->generate('route_admin_url', $par);
			return trim($root.trim($url, '/'), '/');
		} else {
			$halo_pageId = halo_getPageId();
			if(HALOURL::isSEOEnabled() && !$noSEO){
				//find the route rule that matches with parameters list
				$rule = HALORouter::matchRouteRule($par);
				if($rule) {
					$url = $generator->generate($rule['name'], $par);
				} else {
					$url = $generator->generate('route_site_url', $par);
				}
				$root = trim(HALORouter::getRootLink(), '/');
			} else {
				//insert wordpress page id
				$par = array('page_id' => $halo_pageId) + $par;
				$url = $generator->generate('route_site_url', $par);
				$root = get_site_url();
			}

			//append parameters
            $pString = '';
            if (!empty($parameters)) {
				$pVal = http_build_query($parameters);
				if($pVal) {
					if(strpos($url,'?') === false) {
						$pString = '?' . $pVal;
					} else {
						$pString = '&' . $pVal;
					}
				}
            }
			
			return trim($root.'/'.trim($url, '/'), '/') . $pString;
		}
	}
	
	/*
		function to check if SEO setting is enabled
	*/
	public static function isSEOEnabled() {
		if ( get_option('permalink_structure') ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate a URL to an application asset.
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	public function asset($path, $secure = null)
	{
		if ($this->isValidUrl($path)) return $path;

		// Once we get the root URL, we will check to see if it contains an index.php
		// file in the paths. If it does, we will remove it since it is not needed
		// for asset paths, but only for routes to endpoints in the application.
		$root = $this->getRootUrl($this->getScheme($secure));

		return $this->removeIndex($root).'/'.trim($path, '/');
	}
	
	/**
	 * turn on ajax flag
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	public function setAjax(){
		$this->isAjax = true;
	}
	
	public function isAjax(){
		return $this->isAjax;
	}

	public function cleanParams($parameters) {
		return HALOFilter::transformP2S($parameters);
	}
}

