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

class HALOPhotoAPI
{

    /**
     * @api: store a photo to an album
     * 
     * @param array $data --album => album object model --photoIds=> array of photo Ids
     * @return bool
     */
    public static function store($data)
    {
        $default = array('album' => null,
            'photoIds' => array());
        $data = array_merge($default, $data);
        //trigger before photo store event
        if (Event::fire('photo.onBeforeStoring', array($data), true) === false) {
            //error occur, return
            return false;
        }

        $photos = HALOPhotoModel::find($data['photoIds']);
        //update status of photos so that it will not be removed on crontask
        HALOPhotoModel::whereIn('id', $photos->modelKeys())->update(array('status' => HALO_MEDIA_STAT_READY));
        $album = $data['album'];
        $arr = array();
        foreach ($photos as $photo) {
            $arr[] = $photo;
        }
        $album->photos()->saveMany($arr);
        //trigger after photo store event
        Event::fire('photo.onAfterStoring', array($album, $photos));
        //on photos stored, add its reference as HALOResponse data
        HALOResponse::setData('photos', $photos);
        return true;
    }
    /**
     * remove photo
     * 
     * @param  object $photo
     * @return bool
     */
    public static function remove($photo)
    {
        if (Event::fire('photo.onBeforeRemove', array($photo), true) === false) {
            //error occur, return
            return false;
        }
        if (!$photo->canDelete()) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission dennied.')));
            return false;
        }

        $photoId = $photo->id;
        $photo->markDelete();
        Event::fire('photo.onAfterRemove', array($photoId));
        return true;
    }
}
