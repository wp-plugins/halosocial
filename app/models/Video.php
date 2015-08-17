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

class HALOVideoModel extends HALOModel
{
    protected $table = 'halo_videos';

    protected $fillable = array('category_id', 'title', 'description', 'provider', 'published', 'path', 'thumbnail');

    protected $_videoProvider;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // try to set owner_id as the id of current login user
        $this->owner_id = UserModel::getCurrentUserId();
    }

    /**
     * Get validate rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'title' => 'required',
            'path' => 'required|url',
            'provider' => 'required',
        ));

    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOUserModel, HALOActivityModel: one to one (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo('HALOUserModel', 'owner_id');
    }

    /**
     * HALOActivityModel, HALOCommentModel: polymorphic (comments)
     * 
     * @return Illuminate\Database\Query\Builder
     */
    public function comments()
    {
        $builder = $this->morphMany('HALOCommentModel', 'commentable')->orderBy('created_at');
        return $builder;
    }

    /**
     * HALOActivityModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    //define polymorphic relationship
    /**
     * Linkable
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function linkable()
    {
        return $this->morphTo();
    }

    /**
     * HALOVideoModel, HALOReportModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function reports()
    {
        return $this->morphMany('HALOReportModel', 'reportable');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    
    /**
     * Return video title
     * 
     * @return string
     */
    public function getTitle()
    {
        return HALOOutputHelper::cleanText($this->title);
    }

    /**
     * Return video description
     * 
     * @return string
     */
    public function getDescription()
    {
        return HALOOutputHelper::text2html($this->description);
    }

    /**
     * Return video duration in second
     */
    public function getDuration()
    {

    }

    /**
     * Return video thumbnail
     * 
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /*
     * Return video provider
     *
     * @return object
     */
    public function getProvider()
    {
        if (!$this->init()) {
            return '';
        }

        return $this->_videoProvider->getProvider();
    }

    /**
     * Return provider video id
     *
     * @return int|null
     */
    public function getProviderVid()
    {
        if (!$this->init()) {
            return '';
        }

        return $this->_videoProvider->getId();
    }

    /**
     * Return video embede view html
     * 
     * @param  int $videoWidth  
     * @param  int $videoHeight 
     * @return string
     */
    public function getEmbededViewHtml($videoWidth, $videoHeight)
    {
        if (!$this->init()) {
            return '';
        }

        return $this->_videoProvider->getEmbededViewHtml($videoWidth, $videoHeight);
    }

    /**
     * update video fields by fetch video meta data from provider
     *
     * @return bool
     */
    public function updateVideoMeta()
    {
        //to get video meta, need to init provider
        if (!$this->init()) {
            return false;
        }

        //bind all meta data return by provider to this video model
        $data = $this->_videoProvider->getVideoMeta();
        if ($data === false) {
            return false;
        }
        //invalid video

        $this->bindData($data);
        return true;
    }

	/**
     * init provider object associated to this video
     * 
     * @return bool
     */
    public function init()
    {
        $className = 'HALOVideoProvider' . ucfirst($this->provider);
        if (class_exists($className)) {
            $this->_videoProvider = new $className($this->path);
            $inited = true;
        } else {
            $inited = false;
        }
        return $inited;
    }

    /**
     * Create a new video model form url
     * 
     * @param  string $url 
     * @return object      
     */
    public static function createVideo($url)
    {

        //detect provider
        $p = HALOVideoProvider::detectProvider($url);
        $provider = ($p) ? $p->provider : '';

        if (empty($provider)) {
            return null;
        }
        //provider must be detected

        $video = new HALOVideoModel();
        $video->path = $url;
        $video->provider = $provider;

        if (!$video->updateVideoMeta()) {
            return null;
        }

        return $video;
    }

    /**
     * Return the onclick action fornotiication on activity 
     * 
     * @return string
     */
    public function getNotificationTargetAction()
    {
        return "location.reload('" . $this->getPhotoURL() . "')";
    }

    /**
     * Return total video counter
     * 
     * @param  string $status 
     * @return int         
     */
    public static function getTotalVideosCounter($status = null)
    {
        if (is_null($status)) {
            return self::count();
        }

        return self::where('status', $status)->count();
    }

    /**
     * Return display name with link for this model
     * 
     * @param  string $class 
     * @return string        
     */
    public function getDisplayLink($class = '')
    {

        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Return display name without link for this model
     * 
     * @return string
     */
    public function getDisplayName()
    {

        return __halotext('video');
    }

    /**
     * Return view url
     * 
     * @return string
     */
    public function getUrl()
    {
        return URL::to('?view=video&task=show&uid=' . $this->id);
    }

    /**
     * Mark delete 
     * 
     * @return bool
     */
    public function markDelete()
    {
        $this->delete();
    }

    /**
     * Can delete 
     * 
     * @return HALOUserModel
     */
    public function canDelete()
    {
        $my = HALOUserModel::getUser();
        return $my && (HALOAuth::can('backend.view') || $my->id == $this->owner_id);
    }

}
