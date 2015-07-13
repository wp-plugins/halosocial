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
 
class HALOVideoProviderDailymotion extends HALOVideoProvider
{
    public $provider = 'dailymotion';

    /**
     * get feedUrl of the video
     * 
     * @return string
     */
    public function getFeedUrl()
    {
        return 'http://www.dailymotion.com/services/oembed?format=json&url=http://www.dailymotion.com/embed/video/' . $this->getId();
    }

    /**
     * return video id defined by provider
     * 
     * @return mixed
     */
    public function getId()
    {
        $pattern = '/dailymotion.com\/?(.*)\/video\/(.*)/';
        preg_match($pattern, $this->url, $match);
        if (empty($match)) {
            return null;
        }

        $parts = explode('_', $match[2]);

        return !empty($match[2]) ? array_shift($parts) : null;
    }
    /**
     * get Title
     * 
     * @return string 
     */
    public function getTitle()
    {
        return (isset($this->metaData['title'])) ? $this->metaData['title'] : '';
    }
    /**
     * get Description
     *  
     * @return string 
     */
    public function getDescription()
    {
        return (isset($this->metaData['description'])) ? $this->metaData['description'] : '';
    }
    /**
     * get Duration
     * 
     * @return string 
     */
    public function getDuration()
    {
        return (isset($this->metaData['duration'])) ? $this->metaData['duration'] : '';
    }
    /**
     * get Thumbnail
     * 
     * @return string
     */
    public function getThumbnail()
    {
        return (isset($this->metaData['thumbnail_url'])) ? $this->metaData['thumbnail_url'] : '';
    }

    /**
     * get Embeded View Html
     * 
     * @param  string $videoWidth
     * @param  string $videoHeight
     * @return string $embedvideo specific embeded code to play the video
     */
    public function getEmbededViewHtml($videoWidth, $videoHeight)
    {
        $videoId = $this->getId();
        $html = '<iframe src="http://www.dailymotion.com/embed/video/' . $videoId . '" width="' . $videoWidth . '" height="' . $videoHeight . '" frameborder="0"></iframe>';

        return $html;
    }
}
