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

class HALOVideoProviderYoutube extends HALOVideoProvider
{
    public $provider = 'youtube';

    /**
     * get feedUrl of the video
     *
     * @return string
     */
    public function getFeedUrl()
    {
        return 'http://www.youtube.com/oembed?url=' . $this->url . '&format=json';
    }

    /**
     * get video id defined by provider
     *
     * @return bool
     */
    public function getId()
    {
        if ($this->videoId) {
            return $this->videoId;
        }

        preg_match_all('~
	        # Match non-linked youtube URL in the wild. (Rev:20111012)
	        https?://         # Required scheme. Either http or https.
	        (?:[0-9A-Z-]+\.)? # Optional subdomain.
	        (?:               # Group host alternatives.
	          youtu\.be/      # Either youtu.be,
	        | youtube\.com    # or youtube.com followed by
	          \S*             # Allow anything up to VIDEO_ID,
	          [^\w\-\s;]       # but char before ID is non-ID char.
	        )                 # End host alternatives.
	        ([\w\-]{11})      # $1: VIDEO_ID is exactly 11 chars.
	        (?=[^\w\-]|$)     # Assert next char is non-ID or EOS.
	        (?!               # Assert URL is not pre-linked.
	          [?=&+%\w]*      # Allow URL (query) remainder.
	          (?:             # Group pre-linked alternatives.
	            [\'"][^<>]*>  # Either inside a start tag,
	          | </a>          # or inside <a> element text contents.
	          )               # End recognized pre-linked alts.
	        )                 # End negative lookahead assertion.
	        [?=&+%\w]*        # Consume any URL (query) remainder.
	        ~ix',
            $this->url, $matches);

        if (isset($matches) && !empty($matches[1])) {
            return $matches[1][0];
        }

        return false;
    }
    /**
     * get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return isset($this->metaData['title']) ? $this->metaData['title'] : '';
    }
    /**
     * get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return isset($this->metaData['description']) ? $this->metaData['description'] : '';
    }

    public function getDuration()
    {

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
     * get VideoMeta
     * 
     * @return mixed
     */
    public function getVideoMeta()
    {
        $metaData = parent::getVideoMeta();
        if (!$metaData) {
            return false;
        }

        if ($metaData == 'Not Found') {
            HALOResponse::addMessage(HALOError::failed(__halotext('Video was not found')));
            return false;
        }
        if ($metaData == 'Invalid id') {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid video ID')));
            return false;
        }

        //prepare meta data to a unique format
        $rtn = array('title' => $this->getTitle(), 'description' => $this->getDescription(), 'thumbnail' => $this->getThumbnail());
        return $rtn;
    }

    /**
     * specific embeded code to play the video
     * 
     * @param  string $videoWidth
     * @param  string $videoHeight
     * @return string
     */
    public function getEmbededViewHtml($videoWidth, $videoHeight)
    {
        $videoId = $this->getId();
        $html = '<embed src="http://www.youtube.com/v/' . $videoId . '&hl=en&fs=1&hd=1&showinfo=0&rel=0" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $videoWidth . '" height="' . $videoHeight . '" wmode="transparent"></embed>';

        return $html;
    }
}
