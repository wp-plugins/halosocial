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

class UserpointEventHandler
{

	/**
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        $events->listen('comment.onAfterAdding', 'UserpointEventHandler@onCommentAdding');

        $events->listen('friend.onAfterApprove', 'UserpointEventHandler@onFriendApprove');

        $events->listen('tag.onAfterTaggingUser', 'UserpointEventHandler@onTagUser');

        $events->listen('status.onAdding', 'UserpointEventHandler@onStatusAdding');

        $events->listen('photo.onAfterStoring', 'UserpointEventHandler@onPhotoStoring');

        //event handler for getting notification settings
        $events->listen('userpoint.onLoadingSettings', 'UserpointEventHandler@onUserpointLoading');

    }

    /**
     * Event handler to load default userppoint settings
     * 
     * @param  HALOObject $settings 
     */
    public function onUserpointLoading(HALOObject $settings)
    {
        //new comment userpoint
        $settings->setNsValue('comment.create.s', 1);
        $settings->setNsValue('comment.create.p', 1);
        $settings->setNsValue('comment.create.d', __halotext('Add New Comment'));

        //new tag userpoint
        $settings->setNsValue('system.tag.s', 1);
        $settings->setNsValue('system.tag.p', 1);
        $settings->setNsValue('system.tag.d', __halotext('Tag a user'));

        //friend approved
        $settings->setNsValue('friend.approve.s', 1);
        $settings->setNsValue('friend.approve.p', 5);
        $settings->setNsValue('friend.approve.d', __halotext('Approved Friend Request'));

        //share new status
        $settings->setNsValue('share.status.s', 1);
        $settings->setNsValue('share.status.p', 5);
        $settings->setNsValue('share.status.d', __halotext('Share a new status'));

        //share new photo
        $settings->setNsValue('share.photo.s', 1);
        $settings->setNsValue('share.photo.p', 5);
        $settings->setNsValue('share.photo.d', __halotext('Share photos'));

    }

    /**
     * Event handler to update userpoint on new comment
     * 
     * @param  string $comment 
     * @param  array $target  
     */
    public function onCommentAdding($comment, $target)
    {
        HALOUserpointAPI::update('comment.create');

    }

    /**
     * Event handler to update userppoint on tag action notification
     * 
     * @param  array $target  
     * @param  int $user_id 
     */
    public function onTagUser($target, $user_id)
    {
        HALOUserpointAPI::update('system.tag');
    }

    /**
     * Event handler to update userpoint on friend request approve action
     * 
     * @param  object $from 
     * @param  HALOUserModel $to   
     */
    public function onFriendApprove($from, $to)
    {
        HALOUserpointAPI::update('friend.approve', $from);
        HALOUserpointAPI::update('friend.approve', $to);
    }

    /**
     * Event handler to update userpoint on share a new status action
     * 
     */
    public function onStatusAdding()
    {
        HALOUserpointAPI::update('share.status');
    }

	/**
	 * Event handler to update userpoint on share photo action
	 * 
	 * @param  object $album  
	 * @param  object $photos 
	 */
    public function onPhotoStoring($album, $photos)
    {
        HALOUserpointAPI::update('share.photo');
    }

}
