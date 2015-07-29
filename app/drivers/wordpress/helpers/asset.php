<?php
use Illuminate\Filesystem\Filesystem;
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

class HALOAssetHelper
{
	static $paths = null;
	static $loadedCss = array();
	static $loadedJs = array();
	static $loadedJsUrl = array();
	/**
	 * Return a list of assets paths include template override
	 * @return	array	paths list
	 * @param	optional dir relative dir
	 */
	public static function getPaths()
	{
		
		if(is_null(self::$paths)){
			$paths = array();
			//1. get theme path
			$themePath = get_template_directory() . '/halosocial/views';
			if(is_dir($themePath)){
				$paths[] = $themePath;
			}
			
			//get template setting
			try {
				$defaultTemp = HALOConfig::get('template.name','default');
			} catch (Exception $e) {
				$defaultTemp = 'default';
			}
			$defaultTemp = 'default';
			
			//2. get configured template path
			$templatePath = HALO_PLUGIN_PATH . '/app/views/' . $defaultTemp;
			if(is_dir($templatePath)){
				$paths[] = $templatePath;
			}
			//3. get default template path
			if($defaultTemp != 'default'){
				$templateDefaultPath = HALO_PLUGIN_PATH . '/app/views/' . $defaultTemp;
				$paths[] = $templateDefaultPath;
			
			}
			//4. plugin paths
			if(halo_getPageId()){
				$plugins = HALOPluginModel::getActivePlugins();
				foreach ($plugins as $plugin) {
					$pluginPath = HALO_PLUGIN_PATH . '/app/plugins/' . $plugin->folder . '/' . $plugin->element . '/src/views';
					if(is_dir($pluginPath) && file_exists($pluginPath)) {
						$paths[] = $pluginPath;
					}
				}
			}
			self::$paths = $paths;
		}
		
		return self::$paths;
	}
	
	/**
	 * Return storage path of an assets file if found
	 * @return	string	the storage path of the file found
	 * @param	string file relative file name
	 */
	public static function find($file){
		foreach ((array) self::getPaths() as $path)
		{
			if (file_exists($viewPath = $path.'/'.$file))
			{
				return $viewPath;
			}
		}
		return null;
	}
	
	/**
	 * Return asset url of a file
	 * @return	string	the asset url the file found
	 * @param	string file relative file name
	 */
	public static function to($file){
		$path = self::find($file);
		if(is_null($path)) return null;
		//remove the server path to get file url
		return File::toUrl($path);
	}
	
	public static function addCss($file, $flush = false){
		$hash = md5($file);
		if(!isset(self::$loadedCss[$hash])){
			$path = self::find($file);
			if(!is_null($path)){
				self::$loadedCss[$hash] = $path;
				if(HALOConfig::isDev()){
					wp_enqueue_style( $hash, File::toUrl($path) );
				}
				//echo '<link rel="stylesheet" type="text/css" href="'. File::toUrl($path) .'"/>';
			}
		}
		
	}
	
	public static function addScript($file, $ver = false, $in_footer = null){
		$hash = md5($file);
		if(is_null($in_footer)) {
			$in_footer =  HALOConfig::get('global.footerScript', true);
		}
		$in_footer = (boolean) $in_footer;
		if(!isset(self::$loadedJs[$hash])){
			$path = self::find($file);
			if(!is_null($path)){
				$url = File::toUrl($path);
				self::$loadedJs[$hash] = $path;
				self::$loadedJsUrl[$hash] = $url;
				if(HALOConfig::isDev()){
					wp_enqueue_script( $hash, $url, array( 'jquery' ), $ver, $in_footer );
				}
				//echo '<script type="text/javascript" src="'.File::toUrl($path).'"></script>';
			}
		}
	}

	/*
		load default css
	*/
	public static function loadDefaultCss($layout = 'default'){
		$minify = HALOConfig::get('global.cssMinify')?'.min':'';
		//bootstrap css
		self::addCss('assets/css/bootstrap'.$minify.'.css');
		//fontawesome css
		self::addCss('assets/css/fontawesome'.$minify.'.css');
		//halo css
		self::addCss('assets/css/halo'.$minify.'.css');
		//magnific_popup css
		self::addCss('assets/css/magnific_popup'.$minify.'.css');
		
		Event::fire('system.loadDefaultCss',array($layout));
		
		self::enqueueStyle();
	}
	
	/*
		start enqueue css files
	*/
	public static function enqueueStyle(){
		//bundle css files
		if(!HALOConfig::isDev()){
			$bundle = self::bundleAssets('assets/css/', self::$loadedCss, 'css');
			$path = $bundle[1];
			if($path) {
				wp_enqueue_style( $bundle[0], File::toUrl($path) );
			} else {
				//fallback option
				foreach(self::$loadedCss as $hash => $file) {
					wp_enqueue_style( $hashKey, File::toUrl($file) );
				}
			}
		}	
	}
	/*
		load default script
	*/
	public static function loadDefaultScript($layout = 'default'){
		$uglify = HALOConfig::get('global.jsUglify')?'.min':'';
		// Translation json
		HALOAssetHelper::addScript('assets/js/locales/haloDomain.' . App::getLocale() . '.js');

		//bootstrap css
		HALOAssetHelper::addScript('assets/js/jquery-2.1.1.min.js');
		// HALOAssetHelper::addScript('assets/js/modernizr.js', false, false);
		HALOAssetHelper::addScript('assets/js/bootstrap.js');
		
		HALOAssetHelper::addScript('assets/js/bootstrap-markdown.js');
		
		HALOAssetHelper::addScript('assets/js/jquery.nicescroll.min.js');
		HALOAssetHelper::addScript('assets/js/plupload/plupload.full.min.js');
		HALOAssetHelper::addScript('assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
		HALOAssetHelper::addScript('assets/js/pkg1'.$uglify.'.js');
		
		//gb core js
		//HALOAssetHelper::addScript('assets/js/gabo'.$uglify.'.js');
		HALOAssetHelper::addScript('assets/js/features/f1-core'.$uglify.'.js');
		switch($layout){
			case 'admin':
				HALOAssetHelper::addScript('assets/js/features/f3-admin'.$uglify.'.js');				
				break;
			case 'default':
			default:
				HALOAssetHelper::addScript('assets/js/features/f2-site'.$uglify.'.js');				
				break;
		}
		HALOAssetHelper::addScript('assets/js/features/f4-plugin'.$uglify.'.js');
		HALOAssetHelper::addScript('assets/js/features/f5-extras'.$uglify.'.js');
		
		Event::fire('system.loadDefaultScript',array($layout));
		//ready script
		HALOAssetHelper::addScript('assets/js/features/f6-ready.js');
		
		self::enqueueJs();
	}

	/*
		start enqueue css files
	*/
	public static function enqueueJs(){
		//bundle js files
		if(!HALOConfig::isDev()){
			$bundle = self::bundleAssets('assets/js/bundle_/', self::$loadedJs, 'js');
			$path = $bundle[1];
			$in_footer =  HALOConfig::get('global.footerScript', true);
			if($path) {
				wp_enqueue_script( $bundle[0], File::toUrl($path), array( 'jquery' ), false, $in_footer );
			} else {
				//fallback option
				foreach(self::$loadedJs as $hash => $file) {
					wp_enqueue_script( $hashKey, File::toUrl($file), array( 'jquery' ), false, $in_footer );
				}
			}
		}		
	}
	
	/*
		return ajax url
	*/
	public static function getAjaxUrl(){
		return admin_url( 'admin-ajax.php' ) . '?action=halo_ajax';
	}
	
	/*
		return asset url of the active tempalte
	*/
	public static function getAssetUrl($file){
		$path = self::find($file);
		if(!is_null($path)){
			return File::toUrl($path);
		} else {
			$pathPrefix = plugins_url('halosocial/app/views/default/');
			return $pathPrefix . $file;
		}
	}
	
	/*
		return upload dir
	*/
	public static function getUploadTmpDir(){
		$uploadDir = wp_upload_dir();
		$targetDir = $uploadDir['basedir'] . DIRECTORY_SEPARATOR . "halo_tmp";
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
		return $targetDir;
	}
	
	
	/*
		print out the inline script if it is not printed
	*/
	public static function printInlineScript() {
		static $printed = null;
		if(!$printed) {
			$loadJs = implode(',', self::$loadedJsUrl);
			$loadJs = base64_encode($loadJs);
			echo "<script type='text/javascript' src='" . HALOAssetHelper::getAssetUrl('assets/js/modernizr.js') . "'></script>";
			echo "<script> var halo_jax_targetUrl = '" . admin_url( 'admin-ajax.php' ) . "?action=halo_ajax'; 
					var halo_assets_url = '" . plugins_url('halosocial/app/views/default/assets') . "';</script>";
			echo "<script>
					var __haloReady = function(__init) {
						if(!window.halo) { document.addEventListener('DOMContentLoaded', function() { if(!window.halo) {___haloLoadAllScripts(__init);} else {__init();} });
						} else { __init();}		
					};
					var __haloLoadScript = function(src, callback) {
						var s, r, t; r = false; s = document.createElement('script'); s.type = 'text/javascript'; s.src = src;
						s.onload = s.onreadystatechange = function() { if ( !r && (!this.readyState || this.readyState == 'complete') ){ r = true; callback(src); } };
						t = document.getElementsByTagName('script')[0]; t.parentNode.insertBefore(s, t);					
					};
					var ___haloLoadingScripts = false;
					var ___haloLoadAllScripts = function(cb) {
						document.addEventListener('__haloDoneLoading', cb);
						if(!___haloLoadingScripts) {
							___haloLoadingScripts = true; var s = atob('" . $loadJs . "'); var a = s.split(',');
							var n = function (cb) {	if(a.length) { var t = a.shift(); __haloLoadScript(t, function(src) { n(cb);});}}; n(cb);
						}
					}
			</script>";
			$printed = true;
		}
	}
	
	/*
	
	*/
	public static function getRenewalUrl($key, $downloadId) {
		return add_query_arg(array('edd_license_key' => $key,'download_id'=> $downloadId), EDD_HALOSOCIAL_CHECKOUT_URL);
	}
	
	/*
		return page title suffix
	*/
	public static function getPageTitle() {
		return get_the_title();
	}
	
	/*
		check if the current request go to wp-admin 
	*/
	public static function is_admin() {
		return is_admin();
	}
	
	/*
		return language setting string
	*/
	public static function getLanguageInfo() {
		$lang = get_bloginfo('language');
		$lang = str_replace("-", "_", $lang);
		return $lang;
	}

	/*
		bundle assets
	*/
	public static function bundleAssets($bundlePath, array $files, $extension){
		$hashKey = implode(',', $files);
		$hashKey = md5($hashKey);
		$path = self::find($bundlePath . $hashKey . '.' . $extension);
		
		//check filetime
		if($path) {
			$pathTime = filemtime($path);
			$loadedCssTime = array();
			$loadedCssTime = array_map(function($file){
									return filemtime($file);
								}, $files);
			if($pathTime < max($loadedCssTime)) {
				$path = null;
			}
		}
		if(!$path) {
			//concat files and generate the bundle
			$output = "";
			foreach($files as $hash => $file) {
				$output .= @file_get_contents($file);
			}
			if($output) {
				$path = HALO_PLUGIN_PATH . '/app/views/default/' . $bundlePath . 'bundle_' . $hashKey . '.' . $extension;
				try {
					if(!file_exists( dirname($path)) ) {
						File::makeDirectory(dirname($path));
					}
					file_put_contents($path, $output);
				} catch (\Exception $e) {
					$path = null;
				}
			}
		}
		return array($hashKey, $path);
		
	}
}
