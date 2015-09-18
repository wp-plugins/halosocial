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

class PhotoEventHandler
{
	/**
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        $events->listen('photo.create', 'PhotoEventHandler@onPhotoCreate');

        $events->listen('photo.create.render', 'PhotoEventHandler@onPhotoCreateRender');

        $events->listen('photo.update', 'PhotoEventHandler@onPhotoUpdate');
    }

    /**
     * Event handler to update an existing photo
     * 
     * @param  string $msg  
     * @param  array $data   
     */
    public function onPhotoUpdate($msg, $data)
    {
        //prepare data

        $act = new HALOActivityModel();
        $act->action = $data['share_action'];
        $act->context = $data['share_context'];
        $act->tagged_list = $data['share_tagged_list'];
        $act->target_id = $data['share_target_id'];
        $act->message = $data['share_message'];
        $act->access = $data['share_privacy'];

        //actor is the current user
        $my = HALOUserModel::getUser();
        $act->actor_id = $my->user_id;

        //store the attachment information
        $act->setParams('album_id', $data['album_id']);
        $act->setParams('photo_ids', $data['photo_ids']);

        //validate input
        if ($act->validate()->fails()) {
            $msg = $act->getValidator();
            return false;
        }

        $act->save();
        //add actor to the follower list of this activity
        HALOFollowAPI::follow($act);
        $msg->_act = $act;
    }

    /**
     * Event handler to create a new activities
     * 
     * @param  string $msg  
     * @param  array $data   
     */
    public function onPhotoCreate($msg, $data)
    {
        //prepare data
        $act = new HALOActivityModel();
        $act->action = $data['share_action'];
        $act->context = $data['share_context'];
        $act->tagged_list = $data['share_tagged_list'];
        $act->target_id = $data['share_target_id'];
        $act->message = $data['share_message'];
        $act->access = $data['share_privacy'];

        //actor is the current user
        $my = HALOUserModel::getUser();
        $act->actor_id = $my->user_id;

        //store the attachment information
        $act->setParams('album_id', $data['album_id']);
        $act->setParams('photo_ids', $data['photo_ids']);

        //validate input
        if ($act->validate()->fails()) {
            $msg = $act->getValidator();
            return false;
        }

        $act->save();
        //add actor to the follower list of this activity
        HALOFollowAPI::follow($act);
        $msg->_act = $act;
    }

    /**
     * Event handler to display photo create activity
     * 
     * @param  mixed $attachment 
     * @param  HALOUserModel $act        
     */
    public function onPhotoCreateRender($attachment, $act)
    {

        //prepare data
        $album_id = $act->getParams('album_id');
        $photo_ids = $act->getParams('photo_ids');

        $actor = HALOUserModel::getUser($act->actor_id);
        $album = HALOAlbumModel::find($album_id);
        $photos = HALOPhotoModel::find($photo_ids);
        if (is_null($actor) || is_null($album) || is_null($photos)) {
            //wrong format activity, just do nothing to skip it
        } else {
            $attachment->headline = sprintf(__halotext('%s added %s photos to the album %s'), $actor->getDisplayLink('halo-stream-author'), count($photos), $album->getDisplayLink('halo-stream-author'));
            //render photo gallary on stream
            $builder = HALOUIBuilder::getInstance('', 'photo.gallery_thumb', array('photos' => $photos));

            $attachment->content = $builder->fetch();
        }
    }

}
