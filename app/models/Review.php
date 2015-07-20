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

class HALOReviewModel extends HALOModel
{
    protected $table = 'halo_reviews';

    protected $fillable = array('actor_id', 'message', 'rating');

    private $validator = null;

    private $_params = null;

    public function getValidateRule()
    {
        return array('message' => 'required', 'rating' => 'required');

    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOUserModel, HALOReviewModel: one to one (actor)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function actor()
    {
        return $this->belongsTo('HALOUserModel', 'actor_id');
    }

    /**
     * HALOReviewModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function reviewable()
    {
        return $this->morphTo();
    }

    /**
     * HALOReviewModel, HALOFollowerModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function followers()
    {
        return $this->morphMany('HALOFollowerModel', 'followable');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * return the review message
     * 
     * @param  int $limitChar 
     * @return HALOUtilHelper
     */
    public function getMessage($limitChar = 0)
    {
        //process message hashtag
        $message = HALOHashTagAPI::renderMessage($this->message, new HALOPostModel());

        return HALOUtilHelper::renderMessage($message);
    }

    /**
     * Return the the name for display, either username of name based on backend config
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return __halotext($this->getContext());
    }

    /**
     * Return display name with link for this model
     * 
     * @param  string $class 
     * @return stirng        
     */
    public function getDisplayLink($class = '')
    {

        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        $target = $this->reviewable;
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . __halotext($this->getContext()) . '</a> ' . __halotext('on') . ' ' . $target->getDisplayLink();
    }

    /**
     * Get Notification Content 
     * 
     * @param  string $class 
     * @return string
     */
    public function getNotifContent($class = '')
    {
        $message = $this->getMessage();
        return $this->getDisplayLink($class) . ' "' . HALOOutputHelper::ellipsis(HALOOutputHelper::striptags($message, array('p'))) . '"';
    }

    /** 
     * return url for this comment
     * 
     * @return string
     */
    public function getUrl()
    {
        $target = $this->reviewable;
        if ($target && method_exists($target, 'getUrl')) {
            return $target->getUrl();
        } else {
            return '';
        }
    }

    /**
	 * return the onclick action for notification on activity
	 * 
     * @return bool
     */
    public function getNotificationTargetAction()
    {
        //open the single view mode for this activity
        HALOResponse::redirect($this->getUrl());
        return true;
    }

    /**
     * Return brief information builder for this group
     * 
     * @return HALOUIBuilder
     */
    public function getBriefBuilder()
    {
        $builder = HALOUIBuilder::getInstance('', 'review.brief', array('review' => $this, 'zone' => 'brief_' . $this->getContext() . '_' . $this->id));
        return $builder;
    }

    /**
     * Check if this review is in moderating mode
     * 
     * @return boolean
     */
    public function isModerated()
    {
        return $this->published;
    }

    /**
     * set this reviewe in moderated mode
     */
    public function setModerated()
    {
        $this->published = 1;
    }
    
    /**
     * set this review in moderating mode
     */
    public function setModerating()
    {
        $this->published = 0;
    }
}
