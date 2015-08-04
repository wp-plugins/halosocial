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

class HALOFollowAPI
{

    /**
     * add the current login user to the folloing list
     * 
     * @param  object $target
     * @param  mixed $user_id 
     * @return bool
     */
    public static function follow(&$target, $user_id = null)
    {
        //check if target is followable
        if (is_object($target) && method_exists($target, 'followers')) {
            if (is_null($user_id)) {
                $user_id = HALOUserModel::getUser()->id;
            }
            $userIds = (array) $user_id;
            //only add followers if not yet followed
            $followerIds = $target->followers()->whereIn('follower_id', $userIds)->lists('follower_id');
            $newFollowerIds = array_diff($userIds, $followerIds);
            if (count($newFollowerIds) != 0) {
                foreach ($newFollowerIds as $id) {
                    $follower = new HALOFollowerModel();
                    $follower->follower_id = $id;
                    $target->followers()->save($follower);
                }
            }

            return true;
        } else {
            HALOResponse::addMessage(HALOError::failed(__halotext('Target is not a followable object')));
            return false;
        }
    }
    /**
     * remove the current login user from the following list
     * 
     * @param  object $target
     * @param  mixed $user_id
     * @return bool
     */
    public static function unfollow(&$target, $user_id = null)
    {
        if (is_object($target) && method_exists($target, 'followers')) {
            if (is_null($user_id)) {
                $user_id = HALOUserModel::getUser()->id;
            }
            $userIds = (array) $user_id;
            $target->followers()->whereIn('follower_id', $userIds)->delete();
            return true;
        } else {
            HALOResponse::addMessage(HALOError::failed(__halotext('Target is not a followable object')));
            return false;
        }
    }

    /**
     * check whether if current login user is in following list of this target model or not
     * 
     * @param  object $target
     * @param  int  $user_id [description]
     * @return bool
     */
    public static function isFollow($target, $user_id)
    {
        if (is_object($target) && method_exists($target, 'followers')) {
            $user = HALOUserModel::getUser($user_id);
            //only add followers if there is no record
            $follower = $target->followers()->where('follower_id', '=', $user->user_id)->get();
            return (boolean) count($follower);
        } else {
            return false;
        }

    }

}
