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

abstract class HALOVideoProvider
{

    public $metaData = null;
    public $url = '';
    public $videoId = '';
    public $provider = '';

    abstract public function getId();

    abstract public function getTitle();

    abstract public function getDuration();

    abstract public function getThumbnail();

    abstract public function getFeedUrl();

    abstract public function getEmbededViewHtml($videoWidth, $videoHeight);

    /**
     * Initializer
     * 
     * @param string
     */
    public function __construct($url) 
    {
        $this->url = $url;
        $this->videoId = $this->getId();

    }
    /**
     * get Provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * curl the feedUrl to get meta data for this video. return false if fail
     *
     * @return array
     */
    public function getVideoMeta()
    {
        if (empty($this->videoId)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknow Video Id')));
            return false;
        }
        $this->metaData = HALOUtilHelper::getCurl($this->getFeedUrl());
        if (is_null($this->metaData)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Error while fetching video meta')));
            return false;
        }

        if (empty($this->metaData)) {
            return false;
        }

        $rtn = array('title' => $this->getTitle(), 'description' => $this->getDescription(), 'thumbnail' => $this->getThumbnail());
        return $rtn;

    }

    /**
     * function to detect provider name from a given url
     *
     * @param  string $url
     * @return bool
     */
    public static function detectProvider($url)
    {
        $videoFolder = dirname(__FILE__) . '/videos/';
        $providerFiles = File::files($videoFolder);
        foreach ($providerFiles as $file) {
            $className = 'HALOVideoProvider' . ucfirst(basename($file, '.php'));
            if (class_exists($className)) {
                $class = new $className($url);
                if (!empty($class->videoId)) {
                    return $class;
                }
            }
        }
        return false;
    }

}
