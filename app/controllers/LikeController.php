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

class LikeController extends BaseController
{

    /**
     * Initializer
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handler to like an likable object
     *
     * @param  string $context
     * @param  int $target_id
     * @param  string $mode
     * @return JSON
     */
    public function ajaxLike($context, $target_id, $mode)
    {
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Login required'));
        }

        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        if (empty($target)) {
            HALOResponse::redirect(URL::to('/'), __halotext('Could not find a target object for this like action'), 'error');
            return HALOResponse::sendResponse();
        }

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();

        //add the current user to like list
        HALOLikeAPI::like($target);

        //reload the like list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOLikeAPI::getLikeHtml($target, $mode));
        HALOResponse::updateZone(HALOLikeAPI::getDislikeHtml($target, $mode));
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handler to unlike an likable object
     *
     * @param  string $context
     * @param  int $target_id
     * @param  string $mode
     * @return JSON
     */
    public function ajaxUnLike($context, $target_id, $mode)
    {
        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this like action'))
            ->when(is_null($target->id) || !method_exists($target, 'likes'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();

        HALOLikeAPI::unlike($target);

        //reload the like list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOLikeAPI::getLikeHtml($target, $mode));
        HALOResponse::updateZone(HALOLikeAPI::getDislikeHtml($target, $mode));
        return HALOResponse::sendResponse();

    }
    /**
     * ajax handler to dislike an object
     *
     * @param  string $context
     * @param  int $target_id
     * @param  string $mode
     * @return JSON
     */
    public function ajaxDislike($context, $target_id, $mode)
    {
        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this like action'))
            ->when(is_null($target->id) || !method_exists($target, 'likes'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();

        //add the current user to like list
        HALOLikeAPI::dislike($target);

        //reload the like list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOLikeAPI::getLikeHtml($target, $mode));
        HALOResponse::updateZone(HALOLikeAPI::getDislikeHtml($target, $mode));
        return HALOResponse::sendResponse();

    }
    /**
     * ajax handler to undislike an object
     *
     * @param  string $context
     * @param  int $target_id
     * @param  string $mode
     * @return JSON
     */
    public function ajaxUnDislike($context, $target_id, $mode)
    {
        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this like action'))
            ->when(is_null($target->id) || !method_exists($target, 'likes'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();

        HALOLikeAPI::undislike($target);

        //reload the like list to update the cache
        HALOUserModel::init(array($user->id), false);
        $target = HALOModel::getCachedModel($context, $target_id, false);

        //update zones
        HALOResponse::updateZone(HALOLikeAPI::getLikeHtml($target, $mode));
        HALOResponse::updateZone(HALOLikeAPI::getDislikeHtml($target, $mode));

        return HALOResponse::sendResponse();

    }

}
