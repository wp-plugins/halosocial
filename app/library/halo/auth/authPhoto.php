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

class HALOAuthPhotoHandler
{

    /**
     * Check if user is the owner of test object
     * 
     * @param  mixed $user_id
     * @param  object  $obj
     * @return bool
     */
    public function isOwner($user_id = null, $obj)
    {
        $user = HALOUserModel::getUser($user_id);
        return !is_null($user) && (is_a($obj, 'HALOPhotoModel') && $user->user_id == $obj->owner_id);
    }


    /**
     * Check if user is a comment creator of test object
     * 
     * @param  mixed $user_id
     * @param  object  $obj
     * @return bool
     */
    public function isCommentCreator($user_id = null, $obj)
    {
        $user = HALOUserModel::getUser($user_id);
        return !is_null($user);

    }

}
