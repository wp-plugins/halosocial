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

use \OAuth\Common\Consumer\Credentials;
use \OAuth\ServiceFactory;

class HALOOAuth
{
    /**
     * @var ServiceFactory
     */
    private $_serviceFactory;

    /**
     * Storege name from config
     * @var string
     */
    private $_storage_name = 'Memory';

    /**
     * Client ID from config
     * @var string
     */
    private $_client_id;

    /**
     * Client secret from config
     * @var string
     */
    private $_client_secret;

    /**
     * Scope from config
     * @var array
     */
    private $_scope = array();

    /**
     * Constructor
     *
     * @param ServiceFactory $serviceFactory - (Dependency injection) If not provided, a ServiceFactory instance will be constructed.
     */
    
    public function __construct(ServiceFactory $serviceFactory = null)
    {
        if (null === $serviceFactory) {
            // Create the service factory
            $serviceFactory = new ServiceFactory();
        }
        $this->_serviceFactory = $serviceFactory;
    }

    /**
     * Detect config and set data from it
     *
     * @param string $service
     */
    public function setConfig($service)
    {
        $service = lcfirst($service);
        $this->_storage_name = HALOConfig::get('social.' . $service . '.Storage', 'Memory');
        $this->_client_id = HALOConfig::get('social.' . $service . '.oauthClientId');
        $this->_client_secret = HALOConfig::get('social.' . $service . '.oauthClientSecret');
        $this->_scope = HALOConfig::get('social.' . $service . '.oauthScope', '');
        $this->_scope = explode(',', $this->_scope);

    }

    /**
     * Create storage instance
     *
     * @param string $storageName
     * @return OAuth\Common\\Storage
     */
    public function createStorageInstance($storageName)
    {
        $storageClass = "\\OAuth\\Common\\Storage\\$storageName";
        $storage = new $storageClass();

        return $storage;
    }

    /**
     * Set the http client object
     *
     * @param string $httpClientName
     * @return void
     */
    public function setHttpClient($httpClientName)
    {
        $httpClientClass = "\\OAuth\\Common\\Http\\Client\\$httpClientName";
        $this->_serviceFactory->setHttpClient(new $httpClientClass());
    }

    /**
     * @param  string $service
     * @param  string $url
     * @param  array  $scope
     * @return \OAuth\Common\Service\AbstractService
     */
    public function consumer($service, $url = null, $scope = null)
    {
        // get config
        $this->setConfig($service);

        // get storage object
        $storage = $this->createStorageInstance($this->_storage_name);
		
        // create credentials object
        $credentials = new Credentials(
            $this->_client_id,
            $this->_client_secret,
            $url ?: URL::to('?view=user&task=oauthLogin&uid=' . $service, array('noSEO' => true))
        );

        // check if scopes were provided
        if (is_null($scope)) {
            // get scope from config (default to empty array)
            $scope = $this->_scope;
        }

        // return the service consumer object
        return $this->_serviceFactory->createService($service, $credentials, $storage, $scope);

    }

    /**
     * check if a consumer service is configured properly
     * 
     * @param  string  $service
     * @return boole
     */
    public function hasConsumer($service)
    {
        $this->setConfig($service);
        return ($this->_client_id && $this->_client_secret);
    }
}
