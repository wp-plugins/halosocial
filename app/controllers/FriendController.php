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

class FriendController extends BaseController
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
     * ajax handler to send friend request
     *
     * @param  int $toId
     * @return JSON
     */
    public function ajaxSendFriendRequest($toId)
    {
        $my = HALOUserModel::getUser();
        $toUser = HALOUserModel::getUser($toId);
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to make a friend request'));
        }
        Redirect::ajaxError(__halotext('Could not find user'))
            ->when(!$toUser)
            ->apply();

        if (!HALOConnectionAPI::addFriendRequest($my, $toUser, true)) {
            return HALOResponse::sendResponse();
        } else {
            //friend request button is on user response actions so just update the response actions
            $actionsHtml = HALOUIBuilder::getInstance('', 'user.responseActions', array('user' => $toUser))->fetch();
            HALOResponse::updateZone($actionsHtml);
			//add system message
			HALOResponse::addScriptCall('halo.util.setSystemMessage', __halotext('Friend request has been sent'), 'info');
            return HALOResponse::sendResponse();

        }

    }

    /**
     * ajax handler to approve friend request
     * @param  int  $toId
     * @param  int $refresh
     * @return JSON
     */
    public function ajaxApproveRequest($toId, $refresh = 0)
    {
        $my = HALOUserModel::getUser();
        $toUser = HALOUserModel::getUser($toId);
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to approve a friend request'));
        }
        Redirect::ajaxError(__halotext('Could not find user'))
            ->when(!$toUser)
            ->apply();

        if (!HALOConnectionAPI::approveFriendRequest($toUser, $my)) {
            return HALOResponse::sendResponse();
        } else {
            //refresh the page if option is set
            if ($refresh) {
                HALOResponse::refresh();
                return HALOResponse::sendResponse();
            }

            //update the notificaiton display
            $notifController = new NotificationController();
            $notifController->ContentUpdater(null);

            //redirect to the ajaxLoadNotification to update the notification popup
            return $notifController->ajaxLoadNotification();
        }

    }

    /**
     * ajax handler to reject friend request
     * @param  int  $toId
     * @param  int $refresh
     * @return JSON
     */
    public function ajaxRejectRequest($toId, $refresh = 0)
    {
        $my = HALOUserModel::getUser();
        $toUser = HALOUserModel::getUser($toId);
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to respond to a friend request'));
        }
        Redirect::ajaxError(__halotext('Could not find user'))
            ->when(!$toUser)
            ->apply();

        if (!HALOConnectionAPI::rejectFriendRequest($toUser, $my)) {

            return HALOResponse::sendResponse();
        } else {
            //refresh the page if option is set
            if ($refresh) {
                HALOResponse::refresh();
                return HALOResponse::sendResponse();
            }
            //update the notificaiton display
            $notifController = new NotificationController();
            $notifController->ContentUpdater(null);

            //redirect to the ajaxLoadNotification to update the notification popup
            return $notifController->ajaxLoadNotification();
        }

    }

    /**
     * ajax handler to unfriend request
     * @param  int $toId
     * @return JSON
     */
    public function ajaxUnFriend($data)
    {
        $toId = $data['user_id'];
        $my = HALOUserModel::getUser();
        $toUser = HALOUserModel::getUser($toId);
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to change your friendship'));
        }
        Redirect::ajaxError(__halotext('Could not find user'))
            ->when(!$toUser)
            ->apply();

        if (!HALOConnectionAPI::unFriend($my, $toUser)) {

            return HALOResponse::sendResponse();
        } else {
            // update the notificaiton display
            // $notifController = new NotificationController();
            // $notifController->ContentUpdater(null);

            //redirect to the ajaxLoadNotification to update the notification popup
            // return $notifController->ajaxLoadNotification();
            HALOResponse::redirect(URL::to(URL::to($data['url'])));
            return HALOResponse::sendResponse();
        }

    }

    /**
     * Get user's friend list
     *
     * @param   [varname] [description]int $userId
     * @return mixed
     */
    public function getFriends($userId)
    {
        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            return HALOError::abort();
        }

        $title = sprintf(__halotext("%s's friends"), $user->getDisplayName());

        //$friends = $user->friends;

        $users = HALOPagination::getData($user->friends());

        //init users
        $userIds = array();
        foreach ($users as $u) {
            $userIds[] = $u->id;
        }
        $cachedUsers = HALOModel::getCachedModel('user', $userIds);

        $friendList = HALOUIBuilder::getInstance('', 'user.list', array('users' => $cachedUsers))->fetch();

        return View::make('site/user/friends', compact('user', 'title', 'friends', 'users', 'friendList'));
    }

}
