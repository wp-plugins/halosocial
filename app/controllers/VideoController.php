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

class VideoController extends BaseController
{

    /**
     * Initializer
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Show a single video
     *
     * @param  int $videoId
     * @return View
     */
    public function getVideo($videoId)
    {
        $my = HALOUserModel::getUser();

        $video = HALOVideoModel::find($videoId);

        if (!$video) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Invalid Video'));
            return View::make('site/error', compact('title', 'messages'));
        }

        $title = $video->getTitle();

        return View::make('site/video/video', compact('title', 'my', 'video'));
    }

    /**
     * handler for video.create action
     *
     * @param  array $data
     * @return bool
     */
    public function handlerCreate($data)
    {
        $msg = new stdClass();

        if (empty($data['video_path'])) {
            HALOResponse::addMessage(HALOError::failed(__halotext('No video')));
            return false;
        }

        //link video
        if (!HALOVideoAPI::link(array('path' => $data['video_path'], 'title' => $data['video_title'], 'description' => $data['video_description']
            , 'category_id' => 0, 'published' => 1, 'status' => HALO_MEDIA_STAT_READY))) {
            return false;
        }

        //video created successfully
        return true;
    }

    /**
     * handler for delete activity action
     *
     * @param  string $act
     * @return JSON
     */
    public function handlerDelete($act)
    {
        $msg = new stdClass();

        $actid = $act->id;

        $act->delete();

        HALOResponse::addScriptCall('halo.status.deleteCallBack', $actid);

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handle to load comments to popup
     *
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxLoadPopupContent($target_id, $limit = HALO_COMMENT_LIMIT_DISPLAY)
    {

        //get the video object

        $video = HALOVideoModel::find($target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid video'))
            ->when(is_null($video->id))
            ->apply();

        //load comments
        $video->comments()->get()->load('actor', 'likes');

        if ($limit == 'all') {
            $limit = $video->comments->count();
        }
        //update zone
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'photo.popup_content', array('target' => $video, 'limit' => $limit,
            'zone' => 'popup_comment.' . $video->getZone()))->fetch());

        //GA video hit
        HALOGATracking::addHit('pageview', array('page' => URL::to('?view=video&task=show&uid=' . $video->id), 'title' => $video->title));

        return HALOResponse::sendResponse();

    }

    /**
     * ajax Fetch Video
     *
     * @param  string $url
     * @param  string $width
     * @param  string $height
     * @return JSON
     */
    public function ajaxFetchVideo($url, $width, $height)
    {

        $video = HALOVideoModel::createVideo($url);
        if (!$video) {
            //set error message
            HALOResponse::addScriptCall('halo.video.displayShareVideo', '', '', '');
        } else {
            HALOResponse::addScriptCall('halo.video.displayShareVideo', $video->getTitle(), $video->getDescription(), $video->getEmbededViewHtml($width, $height), $video->getProviderVid());
        }
        HALOResponse::addScriptCall('halo.video.validateForm');
        return HALOResponse::sendResponse();

    }

    /**
     * ajax Remove Video
     *
     * @param  int  $videoId
     * @param  int $confirm
     * @return JSON
     */
    public function ajaxRemoveVideo($videoId, $confirm = 0)
    {
        $video = HALOVideoModel::find($videoId);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid video'))
            ->when(is_null($video->id))
            ->apply();
        //rule: only video owner and tagged user can remove video tag
        $my = HALOUserModel::getUser();
        if (!$confirm) {
            //show the confirm form
            $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
                ->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('Are you sure you want to delete this video?'), 'type' => 'warning')))
            ;
            $title = __halotext('Delete Video');
            $content = $builder->fetch();
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Delete'), "onclick" => "halo.video.remove('" . $videoId . "','1')", "icon" => "check", 'class' => 'halo-btn-primary'));
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm')
            ;
            return HALOResponse::sendResponse();
        } else {
            if (!HALOVideoAPI::remove($video)) {
                return HALOResponse::sendResponse();
            } else {
                HALOResponse::addScriptCall('halo.video.removeVideoUI', $videoId);
                return HALOResponse::sendResponse();
            }
        }

    }

}
