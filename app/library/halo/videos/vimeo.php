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

class HALOVideoProviderVimeo extends HALOVideoProvider
{
    public $provider = 'vimeo';

    /**
     * get feedUrl of the video
     * 
     * @return string
     */
    public function getFeedUrl()
    {
        return 'http://vimeo.com/api/v2/video/' . $this->getId() . '.json';
    }

    /**
     * return video id defined by provider
     * 
     * @return mixed
     */
    public function getId()
    {
        $pattern = '/vimeo.com\/(hd#)?(channels\/[a-zA-Z0-9]*#)?(\d*)/';
        preg_match($pattern, $this->url, $match);

        if (!empty($match[3])) {
            return $match[3];
        } else {
            return !empty($match[2]) ? $match[2] : null;
        }
    }
    /**
     * get Title
     * 
     * @return string
     */
    public function getTitle()
    {
        return (isset($this->metaData[0])) ? $this->metaData[0]['title'] : '';
    }
    /**
     * get Description
     * 
     * @return string
     */
    public function getDescription()
    {
        return (isset($this->metaData[0])) ? $this->metaData[0]['description'] : '';
    }
    /**
     * get Duration
     * 
     * @return string
     */
    public function getDuration()
    {
        return (isset($this->metaData[0])) ? $this->metaData[0]['duration'] : '';
    }
    /**
     * get Thumbnail 
     * 
     * @return string
     */
    public function getThumbnail()
    {
        //var_dump($this->metaData);
        return (isset($this->metaData[0])) ? $this->metaData[0]['thumbnail_medium'] : '';
    }

    /**
     *get Embeded View Html
     *
     * @param  string $videoWidth
     * @param   string $videoHeight
     * @return string $embedvideo specific embeded code to play the video
     */
    public function getEmbededViewHtml($videoWidth, $videoHeight)
    {
        $videoId = $this->getId();
        $html = '<iframe src="http://player.vimeo.com/video/' . $videoId . '" width="' . $videoWidth . '" height="' . $videoHeight . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

        return $html;
    }
}
