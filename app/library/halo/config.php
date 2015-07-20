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

class HALOConfig
{
    public $_params = null;

    protected static $_namespaces = null;

    /*
    int the configuration
     */

    private static function init()
    {
        //load configuration from db if not loaded
        if (!App::isBooted()) {
            self::$_namespaces = array();
        } else if (empty(self::$_namespaces)) {
			try {
				$namespaces = HALOConfigModel::all();

				//convert params to HALOParam objects and map to $_namespace
				$namespaces->each(function ($namespace) {
					$namespace->initParam();
					self::$_namespaces[$namespace->param_name] = $namespace;
				});
			} catch(\Exception $e) {
			
			}
        }

    }

    /**
     * get value of a configuration param
     * 
     * @param string $param
     * @param  string $default
     * @return mixed
     */
    public static function get($param, $default = null)
    {
        //load configuration from db if not loaded
        self::init();

        //$param format : <namespace>.<param name>
        $dot = strrpos($param, '.');
        $namespace = substr($param, 0, $dot);
        $paramName = substr($param, $dot + 1);

        if (isset(self::$_namespaces[$namespace])) {
            return self::$_namespaces[$namespace]->getParam($paramName, $default);
        } else {
            return $default;
        }
    }

    /**
     * set value for a configuration param
     * 
     * @param string $param
     * @param mixed $value
     */
    public static function set($param, $value)
    {
        //load configuration from db if not loaded
        self::init();
        //$param format : <namespace>.<param name>
        $dot = strrpos($param, '.');
        $namespace = substr($param, 0, $dot);
        $paramName = substr($param, $dot + 1);
        //@todo: apply filter for namespace and paramName to make sure param are well controlled

        if (!isset(self::$_namespaces[$namespace])) {
            //new namespace, create new record in database
            $config = new HALOConfigModel();
            $config->param_value = '{}';
            $config->param_name = $namespace;
            $config->initParam();
            $config->save();

            self::$_namespaces[$namespace] = $config;
        }
        self::$_namespaces[$namespace]->setParam($paramName, $value);
        return $value;
    }

    /*
    function to store configuration to database
     */
    public static function store()
    {
        foreach (self::$_namespaces as $ns) {
            $ns->prepareSave();
            $ns->save();
        }
    }

    /**
     * function to check if seo is enabled
     * 
     * @return bool
     */
    public static function seo()
    {
        return HALOURL::isSEOEnabled();
    }
	
	public static function activateLicense($licenseString) {
		//update license key
		update_option( 'edd_halo_social_license_key', $licenseString );
		update_option( 'edd_halo_social_last_check_stick', 0 );
		
		$productName = HALOUtilHelper::___getProductName();
		$edd_action = 'activate_license';
		$api_params = array( 
			'edd_action'=> $edd_action, 
			'license' 	=> $licenseString, 
			'item_name' => urlencode( $productName ),
			'url'       => home_url()
		);		
		$storeUrl = HALOUtilHelper::___getStoreUrl();
		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, $storeUrl ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			HALOConfig::set('license.key', $licenseString);
			HALOConfig::store();
			
			//update license status
			if($license_data && isset($license_data->license) && $license_data->license == 'valid'){ 
				$license_status = 1;
			} else {
				$license_status = 0;
			}
			update_option( 'edd_halo_social_license_status', $license_status );
			
			//update License
			HALOConfig::updateLicense($license_data);
			return $license_data;
		}
	}
	
	public static function updateLicense($license_data) {
		Cache::put('license_data', $license_data, 60);
	}
	
	public static function loadLicense($reload = true) {
		if($reload) {
			Cache::forget('license_data');
		}
		return Cache::remember('license_data', 60, function() {
			$licenseString = HALOConfig::get('license.key', '' );
			$defaultLicense = new stdClass();
			$defaultLicense->license = '';
			$defaultLicense->customer_name = '';
			$defaultLicense->customer_email = '';
			$defaultLicense->payment_id = '';
			$defaultLicense->expires = '';
			
			if($licenseString) {
				$productName = HALOUtilHelper::___getProductName();
				$edd_action = 'activate_license';
				$api_params = array( 
					'edd_action'=> $edd_action, 
					'license' 	=> $licenseString, 
					'item_name' => urlencode( $productName ),
					'url'       => home_url()
				);
				$storeUrl = HALOUtilHelper::___getStoreUrl();
				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, $storeUrl ) );
				// make sure the response came back okay
				if ( is_wp_error( $response ) ) {
					return $defaultLicense;
				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					//update License
					return $license_data;
				}
			}
			return $defaultLicense;
		});
	}
	
	public static function checkUpdate() {
		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater
			include_once( HALO_PLUGIN_PATH . 'EDD_SL_Plugin_Updater.php' );
		}
		$licenseString = HALOConfig::get('license.key', '' );
		if($licenseString) {
			$edd_updater = new EDD_SL_Plugin_Updater( HALOUtilHelper::___getStoreUrl(), HALO_PLUGIN_PATH . 'halosocial.php', array( 
					'version' 	=> HALO_PLUGIN_VER, // current version number
					'license' 	=> $licenseString, 	// license key (used get_option above to retrieve from DB)
					'item_name' => HALOUtilHelper::___getProductName(), 	// name of this plugin
					'author' 	=> 'HaloSocial',    // author of this plugin
					'url'           => home_url()
				)
			);
			return $edd_updater;
		} else {
			return null;
		}
	
	}
	
	/*
		check if dev parameter has been enabled
	*/
	public static function isDev() {
		$dev = Input::get('dev', null);
		return !empty($dev);
	}
	
	/*
		clear current caches
	*/
	public static function clearCache() {
		Artisan::call('cache:clear');
	}
}
