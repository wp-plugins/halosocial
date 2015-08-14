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

class ActivityEventHandler
{

	/**
	 * Subscribe 
	 *
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        //status created
        $events->listen('status.onAdding', 'ActivityEventHandler@onStatusAdding');
        //photo created
        $events->listen('photo.onAfterStoring', 'ActivityEventHandler@onPhotoStoring');
        //video linked
        $events->listen('video.onAfterLinking', 'ActivityEventHandler@onVideoLinking');

        //comment created
        //$events->listen('comment.onAfterAdding', 'ActivityEventHandler@onCommentAdding');

        //event for activity rendering
        $events->listen('activity.onRender', 'ActivityEventHandler@onActivityRender');

        $events->listen('activity.onLoadRelatedModels', 'ActivityEventHandler@onLoadRelatedModels');
    }

    /**
     * Event handler to load activity related madels
     * 
     * @param  array $acts       
     */
    public function onLoadRelatedModels($acts)
    {
        //load comments, likes, location
        $acts->load('actor', 'comments', 'comments.actor', 'comments.likes', 'likes', 'location', 'tagusers', 'photos');

        //cache user models
        HALOUserModel::cacheUsers($acts, array('actor', 'comments.actor'));
    }

    /**
     * event handler to create status submit activity
     * 
     * @return HALOActivityAPI
     */
    public function onStatusAdding()
    {

        //grab all activiti data
        $data = HALOActivityAPI::getData(null);
        //setup param for photo activity
        $params = HALOParams::getInstance();

        $data['params'] = $params->toString();

        //call api to create new activity stream
        return HALOActivityAPI::add($data);

    }

    /**
     * Render status attachment
     * 
     * @param  HALOUserModel $act 
     */
    protected function renderStatusCreate(&$act)
    {
        $my = HALOUserModel::getUser();
        $attachment = new stdClass();
        //only render attachement with valid data input
        $actor = HALOUserModel::getUser($act->actor_id);
        $context = $act->context == 'profile' ? 'user' : $act->context;

        $target = HALOModel::getCachedModel($context, $act->target_id);

        if (is_null($actor) || is_null($target)) {
            //wrong format activity, just do nothing to skip it
        } else {
            //compare the actor and the target to get the act headline
            if ($act->context != 'profile' || ($my && $act->target_id != $my->user_id && $act->target_id != $act->actor_id)) {
                if (HALOResponse::getData('streamContext')) {
                    //if showing the context page
                    $headline = '%s';
                    $attachment->headline = sprintf(__halotext($headline), $act->getActorLinks('halo-stream-author'));
                } else {
                    $headline = '%s wrote to %s %s';
                    $attachment->headline = sprintf(__halotext($headline), $act->getActorLinks('halo-stream-author'), $target->getDisplayLink('halo-stream-author'), $target->getContext());
                }
            } else {
                $headline = '%s';
                $attachment->headline = sprintf(__halotext($headline), $act->getActorLinks('halo-stream-author'), $target->getDisplayLink('halo-stream-author'));
            }
        }
        $act->attachment = $attachment;
    }

    /**
     * Event handler to create comment submit activity
     * 
     * @param  string $comment 
     * @param  string $target  
     * @return HALOActivityAPI          
     */
    public function onCommentAdding($comment, $target)
    {

        //grab all activiti data
        $data = array();
        $data['action'] = 'comment.create';
        $data['context'] = $target->getContext();
        $data['target_id'] = $target->id;

        $my = HALOUserModel::getUser();
        //setup param for photo activity
        $params = HALOParams::getInstance();
        $params->set('act_readonly', 1);

        $data['params'] = $params->toString();

        //call api to create new activity stream
        return HALOActivityAPI::add($data, true);

    }

    /**
     * Render status attachment
     * 
     * @param  array $act 
     */
    protected function renderCommentCreate(&$act)
    {
        $attachment = new stdClass();
        //only render attachement with valid data input
        $context = $act->context == 'profile' ? 'user' : $act->context;

        $target = HALOModel::getCachedModel($context, $act->target_id);
        if (is_null($target)) {
            //wrong format activity, just do nothing to skip it
        } else {
            $attachment->headline = sprintf(__halotext('%s commented on a %s'), $act->getActorLinks('halo-stream-author'), $target->getDisplayLink('halo-stream-author'));

        }
        $act->attachment = $attachment;
    }

    /**
     * Event handler to create a photo storing activities
     * 
     * @param  string $album  
     * @param  string $photos 
     * @return HALOActivityAPI         
     */
    public function onPhotoStoring($album, $photos)
    {

        //grab all activiti data
        $data = HALOActivityAPI::getData(null);

        //setup param for photo activity
        $params = HALOParams::getInstance();
        $params->set('album_id', $album->id);
        $params->set('photo_ids', $photos->modelKeys());
        $params->set('headline', __halotext('%s uploaded %s photos on %s album'));

        $data['params'] = $params->toString();

        //call api to create new activity stream
        return HALOActivityAPI::add($data);

    }

    /**
     * Render photo attachement
     * 
     * @param  object $act 
     */
    protected function renderPhotoCreate(&$act)
    {
        $attachment = new stdClass();
        $album_id = $act->getParams('album_id');
        $photo_ids = $act->getParams('photo_ids');
        $headline = $act->getParams('headline');
        //only render attachement with valid data input
        if (!empty($album_id) && !empty($photo_ids)) {
            $actor = HALOUserModel::getUser($act->actor_id);
            $album = HALOModel::getCachedModel('album', $album_id);
            $photos = HALOModel::getCachedModel('photo', $photo_ids);
            if (is_null($actor) || is_null($album) || is_null($photos)) {
                //wrong format activity, just do nothing to skip it
            } else {
                $attachment->headline = sprintf(__halotext($headline), $actor->getDisplayLink('halo-stream-author'), count($photos), $album->getDisplayLink('halo-stream-author'));
                //render photo gallary on stream
                $builder = HALOUIBuilder::getInstance('', 'photo.stream_thumbnail', array('photos' => $photos));

                $attachment->content = $builder->fetch();
            }
        }
        $act->attachment = $attachment;
    }

    /**
     * Event handler to link new video activities
     * 
     * @param  int $video 
     * @return HALOActivityAPI        
     */
    public function onVideoLinking($video)
    {

        //grab all activiti data
        $data = HALOActivityAPI::getData(null);

        //setup param for photo activity
        $params = HALOParams::getInstance();
        $params->set('video_id', $video->id);

        $data['params'] = $params->toString();

        //call api to create new activity stream
        return HALOActivityAPI::add($data, false);

    }

    /**
     * render video attachement
     * 
     * @param  Object $act 
     */
    protected function renderVideoLink(&$act)
    {
        $attachment = new stdClass();
        $video_id = $act->getParams('video_id');
        //only render attachement with valid data input
        if (!empty($video_id)) {
            $actor = HALOUserModel::getUser($act->actor_id);
            $video = HALOModel::getCachedModel('video', $video_id);
            if (is_null($actor) || is_null($video)) {
                //wrong format activity, just do nothing to skip it
            } else {
                $attachment->headline = sprintf(__halotext('%s shared a new video: %s'), $actor->getDisplayLink('halo-stream-author'), $video->getTitle());
                //render video gallary on stream
                $builder = HALOUIBuilder::getInstance('', 'video_player', array('video' => $video));

                $attachment->content = $builder->fetch();
            }
        }
        $act->attachment = $attachment;
    }

    /**
     * Event handler to render activity
     * 
     * @param  string $act 
     */
    public function onActivityRender($act)
    {
        switch ($act->action) {
            case 'photo.create':
                //prepare data
                $this->renderPhotoCreate($act);
                break;
            case 'video.create':
                //prepare data
                $this->renderVideoLink($act);
                break;
            case 'status.create':
                //prepare data
                $this->renderStatusCreate($act);
                break;
            case 'comment.create':
                //prepare data
                $this->renderCommentCreate($act);
                break;
            default:

        }

    }

}
