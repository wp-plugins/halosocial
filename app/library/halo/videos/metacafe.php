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
 
class HALOVideoProviderMetacafe extends HALOVideoProvider
{
    public $provider = 'metacafe';

    /**
     * get feedUrl of the video
     * 
     * @return string
     */
    public function getFeedUrl()
    {
        return 'http://api.embed.ly/1/oembed?format=json&url=' . $this->url;
    }


    /**
     * return video id defined by provider
     * 
     * @return mixed
     */
    public function getId()
    {
        $pattern = '/http\:\/\/\w{3}\.?metacafe.com\/watch\/(.*)\//';
        preg_match($pattern, $this->url, $match);

        return !empty($match[1]) ? $match[1] : null;
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
     * $embedvideo specific embeded code to play the video
     * 
     * @param  string $videoWidth
     * @param  string $videoHeight
     * @return string
     */
    public function getEmbededViewHtml($videoWidth, $videoHeight)
    {
        $videoId = $this->getId();
        $html = '<embed src="http://www.metacafe.com/fplayer/' . $videoId . '.swf" width="' . $videoWidth . '" height="' . $videoHeight . '" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowFullScreen="true" wmode="transparent"> </embed>';
        return $html;
    }
}
