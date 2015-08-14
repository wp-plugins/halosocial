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

class FollowerController extends BaseController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handler to follow a followable object
     *
     * @param  string $context
     * @param  string $target_id
     * @return JSON
     */
    public function ajaxFollow($context, $target_id)
    {
        //get the target object

        $target = HALOModel::getCachedModel($context, $target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this follow action'))
            ->when(is_null($target->id) || !method_exists($target, 'followers'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();
        //add user to target's following list
        HALOFollowAPI::follow($target, $user->user_id);

        //reload the followers list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOFollowerModel::getFollowerHtml($target));
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handler to unfollow a follow object
     * @param  [type] $context   [description]
     * @param  [type] $target_id [description]
     * @return [type]            [description]
     */
    public function ajaxUnFollow($context, $target_id)
    {
        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this follow action'))
            ->when(is_null($target->id) || !method_exists($target, 'followers'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();
        //remove current user from the target's following list
        HALOFollowAPI::unfollow($target, $user->user_id);

        //reload the followers list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOFollowerModel::getFollowerHtml($target));
        return HALOResponse::sendResponse();

    }

}
