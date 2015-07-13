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

class HALOTagAPI
{

    /**
     * add user to the tag list
     * 
     * @param  object $target
     * @param  int $user_id
     * @param  string $param 
     * @return bool
     */
    public static function tagUser($target, $user_id, $param = '')
    {
        //check if target is taggable
        if (is_object($target) && method_exists($target, 'tagusers')) {

            if (Event::fire('tag.onBeforeTaggingUser', array($target, $user_id)) === false) {
                //error occur, return
                return false;
            }

            $userIds = (array) $user_id;
            $users = HALOUserModel::init($userIds);
            if (empty($users)) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user IDs')));
                return false;

            }
            $syncArray = array();
            foreach ($users as $user) {
                //create user tag  if not exists
                if (!$user->tag) {
                    $tag = new HALOTagModel();
                    $user->tag()->save($tag);
                } else {
                    $tag = $user->tag;
                }

                $syncArray[$tag->id] = array('tagging_id' => $target->id, 'params' => $param);
            }
            $target->tagusers()->sync($syncArray, false);

            //also add users as the follower of the target
            HALOFollowAPI::follow($target, $user_id);

            //trigger event on tag User
            Event::fire('tag.onAfterTaggingUser', array($target, $user_id));
            return true;
        } else {
            HALOResponse::addMessage(HALOError::failed(__halotext('Target is not a taggable object')));
            return false;
        }
    }

    /**
     * remove user from the tag list
     * 
     * @param  object $target
     * @param  int $user_id
     * @return bool
     */
    public static function removeTagUser($target, $user_id = null)
    {
        //check if target is taggable
        if (is_object($target) && method_exists($target, 'tagusers')) {

            if (Event::fire('tag.onBeforeRemoveTaggingUser', array($target, $user_id)) === false) {
                //error occur, return
                return false;
            }
            $userIds = (array) $user_id;
            $users = HALOUserModel::init($userIds);
            if (empty($users)) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user IDs')));
                return false;

            }

            foreach ($users as $user) {
                //create user tag  if not exists
                if (!$user->tag) {
                    $tag = new HALOTagModel();
                    $user->tag()->save($tag);
                } else {
                    $tag = $user->tag;
                }

                $target->tagusers()->detach($tag->id);
            }
            return true;
        } else {
            HALOResponse::addMessage(HALOError::failed(__halotext('Target is not a taggable object')));
            return false;
        }
    }


    /**
     * check whether if current login user is in following list of this target model or not
     * 
     * @param  [type]  $target  [description]
     * @param  [type]  $user_id [description]
     * @return bool
     */
    public static function isTagged($target, $user_id)
    {

    }

}
