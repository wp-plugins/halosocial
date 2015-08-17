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

class HALOAlbumAPI
{

    /**
     * add new album
     * 
     * @param array $data
     * @return   bool
     */
    public static function add($data)
    {
        $default = array('name' => '',
            'description' => '',
            'published' => 0,
            'params' => '');
        $data = array_merge($default, $data);
        //trigger before album create event
        if (Event::fire('album.onBeforeAdding', array($data), true) === false) {
            //error occur, return
            return false;
        }

        $album = new HALOAlbumModel();
        $album->bindData($data);
        //validate input
        if ($album->validate()->fails()) {
            $msg = $album->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        }
		
        $album->save();
        //trigger after album create event
        Event::fire('album.onAfterAdding', array($album));
        //on album added, add its reference as HALOResponse data
        HALOResponse::setData('album', $album);
        return true;
    }

    /**
     * Save or Create album
     *    
     * @param  array $data
     * @return bool
     */
    public static function saveOrCreate($data)
    {
        $default = array('name' => '',
            'description' => '',
            'published' => 1,
            'params' => '');
        $data = array_merge($default, $data);
        $isCreate = empty($data['id']) ? true : false;
        //trigger before event
        if (Event::fire(($isCreate ? 'album.onBeforeAdding' : 'album.onBeforeSave'), array($data), true) === false) {
            //error occur, return
            return false;
        }
        if ($isCreate) {
            $album = new HALOAlbumModel();
            $album->id = 0;//dummy album
        } else {
            $album = HALOAlbumModel::find($data['id']);
        }

        //permission checking
        if (!$isCreate && (!$album || !$album->canEdit())) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission denied.')));
            return HALOResponse::sendResponse();
        }

        $album->bindData($data);
        //validate input
        if ($album->validate()->fails()) {
            $msg = $album->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        }
        
        $album->save();
        //trigger after album create album
        Event::fire(($isCreate ? 'album.onAfterAdding' : 'album.onAfterSave'), array($album));
        //on album added, add its reference as HALOResponse data
        HALOResponse::setData('album', $album);
        return true;
    }
}
