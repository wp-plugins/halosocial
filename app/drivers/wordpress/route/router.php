<?php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
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

class HALORouter {

	protected static $routes = null;
	
	const REX_SLUG = '([^/?]+)';
	const REX_NUMBER = '([0-9]{1,})';
	
	private static $routeRules = null;
	
	private static $__unsortRouteRules = array();
	/*
		function to add a route rule
	*/
	public static function addRouteRule($ruleName, array $arrParams, $ruleFormat, $rewriteRex, $rewriteVal) {
		$paramCount = count($arrParams);
		if(!isset(self::$__unsortRouteRules[$paramCount])) {
			self::$__unsortRouteRules[$paramCount] = array();
		}
		$paramKeys = array_flip($arrParams);
		self::$__unsortRouteRules[$paramCount][] = array('name' => $ruleName, 'params' => $arrParams, 'paramKeys' => $paramKeys
														, 'format' => $ruleFormat, 'rewriteRex' => $rewriteRex, 'rewriteVal' => $rewriteVal);
	}
	
	/*
		function to get a list of sorted route rules
	*/
	public static function getRouteRules() {
		$routeRules = array();
		if(is_null(self::$routeRules)){
			foreach(self::$__unsortRouteRules as $rules) {
				foreach($rules as $rule) {
					$routeRules[] = $rule;
				}
			}
			self::$routeRules = array_reverse($routeRules);
		}
		return self::$routeRules;
	}
	
	/*
		function to match a given param list with a route rule
	*/
	public static function matchRouteRule($params) {
		$routeRules = self::getRouteRules();
		foreach($routeRules as $rule) {
			$matchedParams = array_intersect_key($params, $rule['paramKeys']);
			if(count($matchedParams) == count($rule['paramKeys'])) {
				return $rule;
			}
		}
		//no matched route rule found, return null.
		return null;
	}
	
	/*
		utility function to get wordpress rewrite rule for a given route
	*/
	public static function getRewriteRule($route) {
		$halo_pageId = halo_getPageId();
		
		$rewriteRex = trim(HALORouter::__halo_getPathPrefix(), '/') . '/' . $route['rewriteRex'] ;
		$rewriteVal = 'index.php?page_id=' . $halo_pageId . '&' . $route['rewriteVal'];
		return array($rewriteRex => $rewriteVal);
	}
	
	/*
	* generate route rule for the plugin
	*/
	public static function getRoutes(){
		if(is_null(self::$routes)){
			$routes = new RouteCollection();
			$routes->add('route_admin_url', new Route('/'));
			$routes->add('route_site_url', new Route('/'));
			//generate other route rule here

			//init route rules
			self::addRouteRule('route_site_url_1', array('view'), '/{view}'
					, HALORouter::REX_SLUG, 'view=$matches[1]');
			self::addRouteRule('route_site_url_2', array('view', 'task'), '/{view}/{task}'
					, HALORouter::REX_SLUG . '/' . HALORouter::REX_SLUG, 'view=$matches[1]&task=$matches[2]');
			self::addRouteRule('route_site_url_3_c', array('view', 'uid', 'slug'), '/{view}/{uid}-{slug}'
					, HALORouter::REX_SLUG . '/' . HALORouter::REX_NUMBER . '-' . HALORouter::REX_SLUG, 'view=$matches[1]&uid=$matches[2]&slug=$matches[3]');
			self::addRouteRule('route_site_url_3', array('view', 'task', 'uid'), '/{view}/{task}/{uid}'
					, HALORouter::REX_SLUG . '/' . HALORouter::REX_SLUG . '/' . HALORouter::REX_NUMBER, 'view=$matches[1]&task=$matches[2]&uid=$matches[3]');
			self::addRouteRule('route_site_url_4', array('view', 'uid', 'task', 'slug'), '/{view}/{task}/{uid}-{slug}'
					, HALORouter::REX_SLUG . '/' . HALORouter::REX_SLUG . '/' . HALORouter::REX_NUMBER . '-' . HALORouter::REX_SLUG, 'view=$matches[1]&task=$matches[2]&uid=$matches[3]&slug=$matches[4]');

			//re-order the route rules to make sure the rules by number of params in desc ordering
			foreach(self::getRouteRules() as $rule) {
				$routes->add($rule['name'], new Route($rule['format']));
			}
		
			self::$routes = $routes;
		}
		return self::$routes;
		
	}
		
	public static function getRewriteRuleVersionString() {
		return '__halo_rewrite_rule_version_' . HALO_PLUGIN_VER . HALORouter::__halo_getPathPrefix();
	}
	
	protected static function __halo_getPathPrefix() {
		static $__haloPathPrefix = null;
		if(is_null($__haloPathPrefix)){
			$siteUrl = get_site_url();
			$pageUrl = HALORouter::getRootLink();
			$pos = strpos($pageUrl, $siteUrl);
			if($pos === 0) {
				$__haloPathPrefix = substr($pageUrl, strlen($siteUrl));
			} else {
				$__haloPathPrefix = parse_url($pageUrl, PHP_URL_PATH);
			}
		}
		return $__haloPathPrefix;
	}
	
	public static function getRootLink(){
		static $rootLink = null;
		if(is_null($rootLink)) {
			$halo_pageId = halo_getPageId();
			$rootLink = get_permalink($halo_pageId);
			//if halosocial page is set as static frontpage, there is no pagename prefix in the rootLink, we need to add it manually
			if($halo_pageId && 'page' == get_option('show_on_front') && $halo_pageId == get_option('page_on_front')) {
				$rootLink = $rootLink . 'halosocial/';
			}
		}
		return $rootLink;
	}

	// flush_rules() if our rules are not yet included
	public static function __halo_flush_rules(){
		$rules = get_option( 'rewrite_rules' );
		//var_dump($rules);
		if ( ! isset( $rules[HALORouter::getRewriteRuleVersionString()] ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}
	
	// Adding a new rule
	public static function __halo_insert_rewrite_rules( $rules )
	{
		//init routes
		HALORouter::getRoutes();
		
		$routes = HALORouter::getRouteRules();
		//reverse routes ordering
		$routes = array_reverse($routes);
		foreach($routes as $route) {
			$newRule = HALORouter::getRewriteRule($route);
			$rules = $newRule + $rules;
		}
		
		//a markable rule to indicate that halo seo rules are inserted
		$rules = $rules + array( HALORouter::getRewriteRuleVersionString() => 'index.php');
		return $rules;
	}

	// Adding the id var so that WP recognizes it
	public static function __halo_insert_query_vars( $vars )
	{
		array_push($vars, 'view', 'task', 'uid', 'slug', 'page_id');
		return $vars;
	}		

	public static function insertRewriteRules(){
		add_filter( 'rewrite_rules_array','HALORouter::__halo_insert_rewrite_rules' );
		add_filter( 'query_vars','HALORouter::__halo_insert_query_vars' );
		add_action( 'wp_loaded','HALORouter::__halo_flush_rules' );
		
		return;
	}
	
}
