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

class HALOCommentModel extends HALONestedModel
{
    protected $table = 'halo_comments';

    /**
     * Column name which stores reference to parent's node.
     *
     * @var int
     */
    protected $parentColumn = 'parent_id';

    /**
     * Column name for the left index.
     *
     * @var int
     */
    protected $leftColumn = 'lft';

    /**
     * Column name for the right index.
     *
     * @var int
     */
    protected $rightColumn = 'rgt';

    /**
     * Column name for the depth field.
     *
     * @var int
     */
    protected $depthColumn = 'depth';

    /**
     * With Baum, all NestedSet-related fields are guarded from mass-assignment
     * by default.
     *
     * @var array
     */
    protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

    protected $hidden = array('created_at', 'updated_at', 'published');

    protected $fillable = array('actor_id', 'message', 'published');

    private $validator = null;

    private $_params = null;

    /**
     * Get validate rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('message' => 'required');

    }

    //////////////////////////////////// Define Relationships /////////////////////////
    //define polymorphic relationship
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * HALOUserModel, HALOActivityModel: one to one (actor)
     * 
     * @return Illuminate\Database\Eloquent\Relations\ebelongTo
     */
    public function actor()
    {
        return $this->belongsTo('HALOUserModel', 'actor_id');
    }

    /**
     * HALOCommentModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany 
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    /**
     * HALOCommentModel, HALOReportModel: polymorphic
     * 
     * @return  Illuminate\Database\Eloquent\Relations\morphMany 
     */
    public function reports()
    {
        return $this->morphMany('HALOReportModel', 'reportable');
    }

    /**
     * HALOUserModel, HALOCommentModel: one to one
     * 
     * @return object
     */
    public function owner()
    {
        return $this->actor();
    }

    /**
     * HALOUserModel, HALOCommentModel: one to one
     * 
     * @return object
     */
    public function photo()
    {
        return $this->hasParam('HALOPhotoModel', 'photo_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

	/**
	 * Get actors
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getDisplayActor()
	{
		$actorIds = array();
		if($this->actor_list) {
			return HALOActorListHelper::getActorListFromColumn($this, 'actor_list');
		} else {
			return $this->actor;
		}
	}

    /**
     * Return the comment message
     * 
     * @param  integer $limitChar
     * @return string
     */
    public function getMessage($limitChar = 0)
    {
        //process message hashtag
        $message = HALOHashTagAPI::renderMessage($this->message, $this->commentable);

        $message = HALOUtilHelper::renderMessage($message);
        return $message;
    }

    /**
     * Return the the name for display, either username of name based on backend config
     * @return  string
     */
    public function getDisplayName()
    {
        return __halotext($this->getContext());
    }

    /**
     * Return display name with link for this model
     * @return  string
     */
    public function getDisplayLink($class = '')
    {

        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . __halotext($this->getContext()) . '</a> "' . $this->getMessage() . '"';
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
        $content = '"' . HALOOutputHelper::ellipsis(HALOOutputHelper::striptags($message, array('p'))) . '"';
        $content = $this->getContext() . '; ' . $content;
        return $content;
    }

    /**
     * Return photo object attached to this comment
     * 
     * @return object
     */
    public function getPhoto()
    {
        if ($this->getParams('photo_id',null) && $this->photo) {
			return $this->photo->first();
		} else {
			return null;
		}
    }
	
    /**
     * Return path to the attached photo
     * 
     * @param   int   $height the photo height (optional)
     * @return  string path to the photo
     */
    public function getPhotoUrl($height = 100)
    {
        if ($this->getParams('photo_id',null) && $this->photo) {
            $path = $this->photo->first()->getPhotoURL();
            return HALOPhotoHelper::getResizePhotoURL($path, null, $height);
        } else {
			return '';
        }
    }
	
    /**
     * Get url for this comment
     * 
     * @return string
     */
    public function getUrl()
    {
        $target = $this->commentable;
        if ($target && method_exists($target, 'getUrl')) {
            return $target->getUrl();
        } else {
            return '';
        }
    }

    /**
     * Get the onclick action for notification on activity
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
     * Check if user can edit preview link
     * 
     * @return string
     */
    public function canEditPreview()
    {
        return (HALOAuth::can('comment.edit', $this) || HALOAuth::can('comment.delete', $this));
    }
}
