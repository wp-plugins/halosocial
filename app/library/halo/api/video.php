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
 
class HALOVideoAPI
{

    /**
     * @api: link a video
     * 
     * @param  array $data album=> album object model. photoIds=> array of photo Ids
     * @return bool
     */
    public static function link($data)
    {
        $default = array('path' => null,
            'title' => null,
            'description' => null,
            'category_id' => 0,
            'published' => 0,
            'status' => 0,
            'params' => '');
        $data = array_merge($default, $data);
        //trigger before photo store event
        if (Event::fire('video.onBeforeLinking', array($data), true) === false) {
            //error occur, return
            return false;
        }

        $video = HALOVideoModel::createVideo($data['path']);
        if (!$video) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Video')));
            return false;
        }
        //owner is the current user
        $my = HALOUserModel::getUser();
        $video->owner_id = $my->user_id;

        $video->bindData($data);
        //validate data
        if ($video->bindData($data)->validate()->fails()) {

            $msg = $video->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        } else {

            $video->save();
        }

        //trigger event
        Event::fire('video.onAfterLinking', array($video));
        //on photos stored, add its reference as HALOResponse data
        HALOResponse::setData('video', $video);
        return true;
    }
    /**
     * remove video
     * 
     * @param  object $video
     * @return bool
     */
    public static function remove($video)
    {
        if (Event::fire('video.onBeforeRemove', array($video), true) === false) {
            //error occur, return
            return false;
        }
        if (!$video->canDelete()) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission dennied.')));
            return false;
        }

        $videoId = $video->id;
        $video->markDelete();
        Event::fire('video.onAfterRemove', array($videoId));
        return true;
    }

}
