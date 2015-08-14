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

class HALOLikeModel extends HALOModel
{
    protected $table = 'halo_likes';

    protected $hidden = array('created_at', 'updated_at', 'published');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array();

    }

    //////////////////////////////////// Define Relationships /////////////////////////
    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Get Like Count 
     * 
     * @return int
     */
    public function getLikeCount()
    {
        if (trim($this->like) == '') {
            return 0;
        }

        $likeArr = explode(',', trim($this->like));
        return count($likeArr);
    }

    /**
     * Get Dis Like Count 
     * 
     * @return int
     */
    public function getDisLikeCount()
    {
        if (trim($this->dislike) == '') {
            return 0;
        }

        $dislikeArr = explode(',', trim($this->dislike));
        return count($dislikeArr);
    }

    /**
     * Get Like List 
     * 
     * @return HALOUserModel
     */
    public function getLikeList()
    {
        if (trim($this->like) == '') {
            return array();
        }

        $likeArr = explode(',', trim($this->like));
        return HALOUserModel::init($likeArr);
    }

    /**
     * Get Dis Like List 
     * 
     * @return HALOUserModel
     */
    public function getDisLikeList()
    {
        if (trim($this->dislike) == '') {
            return array();
        }

        $dislikeArr = explode(',', trim($this->dislike));
        return HALOUserModel::init($dislikeArr);
    }

}
