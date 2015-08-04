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

class PhotoController extends BaseController
{

    /**
     * Initializer.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a single photo
     * @param  int $photoId
     * @return View
     */
    public function getShow($photoId)
    {
        $my = HALOUserModel::getUser();

        $photo = HALOPhotoModel::find($photoId);

        if (!$photo) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Invalid Photo Id'));
            return View::make('site/error', compact('title', 'messages'));
        }

        //load comments
        $photo->comments()->get()->load('actor', 'likes');

        $title = $photo->caption;

        //photo tags
        $tags = array();
        $taggedUsers = $photo->tagusers;
        //preload all tagged users
        $userIds = array();
        foreach ($taggedUsers as $tagUser) {
            $userIds[] = $tagUser->taggable_id;
        }
        HALOUserModel::init($userIds);
        foreach ($taggedUsers as $tagUser) {
            $params = isset($tagUser->pivot->params) ? $tagUser->pivot->params : '';
            $params = HALOParams::getInstance($params);
            $user = HALOUserModel::getUser($tagUser->taggable_id);
            $removable = ($my && ($my->id == $user->id || $my->id == $photo->owner->id));
            $tags[] = array('user' => $user, 'params' => $params, 'removable' => $removable);
        }
        //GA photo hit
        HALOGATracking::addHit('pageview', array('page' => URL::to('?view=photo&task=show&uid=' . $photo->id), 'title' => $photo->caption));

        return View::make('site/photo/photo', compact('title', 'my', 'photo', 'tags'));
    }

    /**
     * Show a single album
     *
     * @param  int $albumId
     * @return View
     */
    public function getAlbum($albumId)
    {
        $my = HALOUserModel::getUser();

        $album = HALOAlbumModel::find($albumId);

        if (!$album) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Invalid Album Id'));
            return View::make('site/error', compact('title', 'messages'));
        }

        //load comments
        //$album->comments()->get()->load('actor','likes');
        $album->load('owner', 'photos');

        $title = $album->name;

        return View::make('site/photo/album', compact('title', 'my', 'album'));
    }

    /**
     * handler for photo.create action
     *
     * @param  array $data
     * @return bool
     */
    public function handlerCreate($data)
    {
        $msg = new stdClass();

        //store the photo attachment
        $album_id = isset($data['album_id']) ? $data['album_id'] : 0;
        $album_name = isset($data['album_name']) ? $data['album_name'] : '';

        if (!empty($album_name)) {
            // create new album
            if (!HALOAlbumAPI::add(array('name' => $album_name, 'published' => 1))) {
                return false;
            }
            $album = HALOResponse::getData('album');
        } else {
            if (!$album_id || !($album = HALOAlbumModel::find($album_id))) {
				$my = HALOUserModel::getUser();
				if($my) {
					$album = $my->getDefaultAlbum();
				} else {
					HALOResponse::addMessage(HALOError::getMessageBag()->add('album_id', __halotext('Please select a target album to upload photo')));
					return false;
				}
            }
        }

        $photo_ids = isset($data['media_id']) ? $data['media_id'] : array();
        if (empty($photo_ids)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Photos were not uploaded')));
            return false;
        }

        //store photo to album
        if (!HALOPhotoAPI::store(array('album' => $album, 'photoIds' => $photo_ids))) {
            return false;
        }

        //photo created successfully
        return true;
    }

    /*

     */
    /**
     * handler for delete activity action
     *
     * @param  int $act
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
     * @param  string $limit
     * @return JSON
     */
    public function ajaxLoadPopupContent($target_id, $limit = HALO_COMMENT_LIMIT_DISPLAY)
    {

        $my = HALOUserModel::getUser();
        //get the photo object

        $photo = HALOPhotoModel::find($target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid photo'))
            ->when(is_null($photo->id))
            ->apply();

        //load comments
        $photo->comments()->get()->load('actor', 'likes');

        if ($limit == 'all') {
            $limit = $photo->comments->count();
        }

        //photo actions
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'photo.popup_actions', array('photo' => $photo))->fetch());

        //update zone
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'photo.popup_content', array('target' => $photo, 'limit' => $limit,
            'zone' => 'popup_comment.' . $photo->getZone()))->fetch());
        //photo tags
        $taggedUsers = $photo->tagusers;
        //preload all tagged users
        $userIds = array();
        foreach ($taggedUsers as $tagUser) {
            $userIds[] = $tagUser->taggable_id;
        }
        HALOUserModel::init($userIds);
        foreach ($taggedUsers as $tagUser) {
            $params = isset($tagUser->pivot->params) ? $tagUser->pivot->params : '';
            $params = HALOParams::getInstance($params);
            $user = HALOUserModel::getUser($tagUser->taggable_id);
            $removable = ($my && ($my->id == $user->id || $my->id == $photo->owner->id));
            HALOResponse::addScriptCall('halo.photo.showTag', $user->getDisplayLink(), $user->id, $params->get('x1'), $params->get('y1'), $params->get('x2'), $params->get('y2'), $removable);
        }

        //GA photo hit
        HALOGATracking::addHit('pageview', array('page' => URL::to('?view=photo&task=show&uid=' . $photo->id), 'title' => $photo->caption));

        return HALOResponse::sendResponse();

    }

    /**
     * Show edit avatar form
     *
     * @param string $context
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxShowEditAvatarForm($context, $target_id)
    {
        //get target model
        $target = HALOModel::getCachedModel($context, $target_id);

		if(!HALOAuth::can($context . '.edit', $target)){
			HALOResponse::addMessage(HALOError::failed(__halotext('Access denied')));
		}

        $avatar = $target->avatar()->get();
        $mode = (count($avatar) == 0) ? 'create' : 'edit';

        $title = __halotext('Change Avatar');

        //init photo model
        if ($mode == 'create') {
            //initialize default photo
            $photo = new HALOPhotoModel();
            $photo->id = 0;

        } else {
            $photo = $avatar->first();
        }
		
		if($photo->id && !$photo->isExists()){
			HALOResponse::addMessage(HALOError::failed(__halotext('The physical file for this photo is removed. Trying to use default avatar.')));
            $photo = new HALOPhotoModel();
            $photo->id = 0;
		}

        $builder = HALOUIBuilder::getInstance('editPhoto', 'form.form', array('name' => 'popupForm'))
            ->addUI('photo', HALOUIBuilder::getInstance('', 'photo.edit', array('id' => 'photo_edit', 'width' => 250, 'size' => 6,
            'photo' => $photo, 'default' => $target->getAvatar(),
            'photoZoom' => $target->getParams('avatarZoom', '100'),
            'photoLeft' => $target->getParams('avatarLeft', '0'),
            'photoTop' => $target->getParams('avatarTop', '0'),
            'photoWidth' => $target->getParams('avatarWidth', '0'),
            'viewRatio' => '1:1', //cover ratio is 8/3
            'inputName' => 'photo_id'
        )))
        ;
        $content = $builder->fetch();
        $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Save'), "onclick" => "halo.photo.saveAvatar('" . $target->getContext() . "','" . $target->id . "')", "icon" => "check", 'class' => 'halo-btn halo-btn-primary'));
        $actionUpload = HALOPopupHelper::getAction(array("name" => __halotext('Upload Photo'), "class" => "halo-btn-success halo-photo-upload-btn", "icon" => "cloud-upload"));
        HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormAction', $actionUpload)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm')
            ->addScriptCall('halo.photo.initUploadBtn', '.halo-photo-upload-btn', '[data-halo-photo-upload-btn]')
            ->addScriptCall('halo.photo.startEdit', '.halo-photo-edit');

        //init uploader attached to the form
        //HALOResponse::addScriptCall('halo.uploader.initUploaders','#popupForm','{"multipleUpload":false}');
        return HALOResponse::sendResponse();

    }
    /**
     * Save user avatar
     *
     * @param  string $context
     * @param  int $target_id
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSaveAvatar($context, $target_id, $postData)
    {

        //get target model
        $target = HALOModel::getCachedModel($context, $target_id);

        //check permission
        Redirect::ajaxError(__halotext('Access denied'))
            ->when(!HALOAuth::can($context . '.edit', $target))
            ->apply();

        //check photo id
        $photo_id = isset($postData['photoId']) ? $postData['photoId'] : 0;
        $photo_id = is_array($photo_id) ? array_shift($photo_id) : $photo_id;
        $photo = HALOPhotoModel::find($photo_id);

        Redirect::ajaxError(__halotext('The photo does not exists on the server'))
            ->when(empty($photo->id))
            ->apply();

        //update photo status
        $photo->status = HALO_MEDIA_STAT_READY;
        $photo->save();

        $oldAvatarFile = '';
        $oldThumbFile = '';
        //change avatar create new photo cropped image on the server so we need to make sure old cropped image is delete before saving new file
        if ($target->avatar_id) {
            //only check for non default avatar targets
            if (method_exists($target, 'getAvatar')) {
                $oldAvatarFile = File::fromUrl($target->getAvatar());
            }
            if (method_exists($target, 'getThumb')) {
                $oldThumbFile = File::fromUrl($target->getThumb());
            }
        }

        $target->avatar()->associate($photo);
        //save others parameter
        $photoZoom = isset($postData['photoZoom']) ? $postData['photoZoom'] : 100;
        $photoTop = isset($postData['photoTop']) ? $postData['photoTop'] : 0;
        $photoLeft = isset($postData['photoLeft']) ? $postData['photoLeft'] : 0;
        $photoWidth = isset($postData['photoWidth']) ? $postData['photoWidth'] : 0;
        $target->setParams('avatarZoom', $photoZoom);
        $target->setParams('avatarTop', $photoTop);
        $target->setParams('avatarLeft', $photoLeft);
        $target->setParams('avatarWidth', $photoWidth);
        $target->save();

        //delete the avatar
        if ($oldAvatarFile && file_exists($oldAvatarFile)) {
            File::delete($oldAvatarFile);
        }
        if ($oldThumbFile && file_exists($oldThumbFile)) {
            File::delete($oldThumbFile);
        }

        //trigger event on avatar changed
        Event::fire('photo.onAfterChangingAvatar', array($target, $photo));
        //init uploader attached to the form
        HALOResponse::refresh();
        return HALOResponse::sendResponse();

    }

    /**
     * Show edit cover form
     *
     * @param  string $context
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxShowEditCoverForm($context, $target_id)
    {

        //get target model
        $target = HALOModel::getCachedModel($context, $target_id);
        //check permission
        Redirect::ajaxError(__halotext('Access denied'))
            ->when(!HALOAuth::can($context . '.edit', $target))
            ->apply();

        $cover = $target->cover()->get();
        $mode = (count($cover) == 0) ? 'create' : 'edit';

        $title = __halotext('Change Cover');

        //init photo model
        if ($mode == 'create') {
            //initialize default category
            $photo = new HALOPhotoModel();
            $photo->id = 0;

        } else {
            $photo = $cover->first();
        }
		if($photo->id && !$photo->isExists()){
			HALOResponse::addMessage(HALOError::failed(__halotext('The physical file for this photo is removed. Trying to use default cover.')));
            $photo = new HALOPhotoModel();
            $photo->id = 0;
		}

        $builder = HALOUIBuilder::getInstance('editPhoto', 'form.form', array('name' => 'popupForm'))
            ->addUI('photo', HALOUIBuilder::getInstance('', 'photo.edit', array('id' => 'photo_edit', 'width' => 250, 'height' => 150, 'size' => 11,
            'photo' => $photo, 'default' => $target->getCover(),
            'photoZoom' => $target->getParams('coverZoom', '100'),
            'photoLeft' => $target->getParams('coverLeft', '0'),
            'photoTop' => $target->getParams('coverTop', '0'),
            'photoWidth' => $target->getParams('coverWidth', '0'),
            'viewRatio' => $target->getCoverRatio(), //cover ratio is 8/3
            'inputName' => 'photo_id'
        )))
        ;
        $content = $builder->fetch();
        $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Save'), "onclick" => "halo.photo.saveCover('" . $target->getContext() . "','" . $target->id . "')", "icon" => "check", 'class' => 'halo-btn halo-btn-primary'));
        $actionUpload = HALOPopupHelper::getAction(array("name" => __halotext('Upload Photo'), "class" => "halo-btn-success halo-photo-upload-btn", "icon" => "cloud-upload"));

        HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormAction', $actionUpload)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm')
            ->addScriptCall('halo.photo.initUploadBtn', '.halo-photo-upload-btn', '[data-halo-photo-upload-btn]')
            ->addScriptCall('halo.photo.startEdit', '.halo-photo-edit');

        //init uploader attached to the form
        //HALOResponse::addScriptCall('halo.uploader.initUploaders','#popupForm','{"multipleUpload":false}');
        return HALOResponse::sendResponse();

    }
    /**
     * Save user cover
     *
     * @param  string $context
     * @param  int $target_id
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSaveCover($context, $target_id, $postData)
    {

        //get target model
        $target = HALOModel::getCachedModel($context, $target_id);

        //check permission
        if (!HALOAuth::can($context . '.edit', $target)) {
            HALOResponse::enqueueMessage(__halotext('Access denied'), 'warning');
            return HALOResponse::sendResponse();
        }
        //check photo id
        $photo_id = isset($postData['photoId']) ? $postData['photoId'] : 0;
        $photo_id = is_array($photo_id) ? array_shift($photo_id) : $photo_id;
        $photo = HALOPhotoModel::find($photo_id);

        if (empty($photo->id)) {
            HALOResponse::enqueueMessage(__halotext('The photo does not exists on the server'), 'warning');
            return HALOResponse::sendResponse();
        }

        //update photo status
        $photo->status = HALO_MEDIA_STAT_READY;
        $photo->save();

        $oldCoverFile = '';
        //change cover create new photo cropped image on the server so we need to make sure old cropped image is delete before saving new file
        if ($target->cover_id) {
            //only check for non default cover targets
            $oldCoverFile = File::fromUrl($target->getCover());
        }

        $target->cover()->associate($photo);
        //save others parameter
        $photoZoom = isset($postData['photoZoom']) ? $postData['photoZoom'] : 100;
        $photoTop = isset($postData['photoTop']) ? $postData['photoTop'] : 0;
        $photoLeft = isset($postData['photoLeft']) ? $postData['photoLeft'] : 0;
        $photoWidth = isset($postData['photoWidth']) ? $postData['photoWidth'] : 0;
        $target->setParams('coverZoom', $photoZoom);
        $target->setParams('coverTop', $photoTop);
        $target->setParams('coverLeft', $photoLeft);
        $target->setParams('coverWidth', $photoWidth);
        $target->save();

        //delete the oldcover
        if ($oldCoverFile && file_exists($oldCoverFile)) {
            File::delete($oldCoverFile);
        }

        //trigger event
        Event::fire('photo.onAfterChangingCover', array($target, $photo));
        //init uploader attached to the form
        HALOResponse::refresh();
        return HALOResponse::sendResponse();

    }

    /**
     * Update the select2edit-photo tab in popup  to display photo listing of the selected albumId
     *
     * @param  int $albumId
     * @return JSON
     */
    public function ajaxChangeAlbumListing($albumId)
    {

        $album = HALOAlbumModel::find($albumId);
        Redirect::ajaxError(__halotext('Invalid album ID'))->when(!$album)->apply();

        //load all album photos
        $album->load('photos');
        //var_dump($album->photos()->toSql());
        $builder = HALOUIBuilder::getInstance('', 'photo.photo_listing', array('photos' => $album->photos));
        $html = '<div class="halo-select2edit-photo-outter" data-halozone="select2edit-album-photo">' . $builder->fetch() . '</div>';

        return HALOResponse::updateZone($html)->sendResponse();
    }

    /**
     * Display list of user's album in select2edit-photo tab popup
     *
     * @return JSON
     */
    public function ajaxShowAlbumListing()
    {

        $my = HALOUserModel::getUser();

        Redirect::ajaxError(__halotext('Permission dennied'))->when(!$my)->apply();

        $my->load('albums', 'albums.photos');
        //load all album photos
        $builder = HALOUIBuilder::getInstance('', 'photo.album_listing', array('albums' => $my->albums));
        $html = '<div class="halo-select2edit-photo-outter" data-halozone="select2edit-album-photo">' . $builder->fetch() . '</div>';

        return HALOResponse::updateZone($html)->sendResponse();
    }

    /**
     * ajax to show photo in an album
     *
     * @param  int $albumId
     * @return JSON
     */
    public function ajaxShowAlbumPhotos($albumId, $userId = null)
    {
		if(is_array($albumId)) {	//come frompaging request
			Input::merge($albumId);
			$albumId = Input::get('albumId');
			$mode = HALO_CONTENT_INSERT_MODE;
		} else {
			$mode = HALO_CONTENT_UPDATE_MODE;
		}
		
		Input::merge(array('albumId' => $albumId));
        $album = HALOAlbumModel::find($albumId);

        Redirect::ajaxError(__halotext('Invalid album ID'))->when(!$album)->apply();

        //load all album photos
        $album->load('photos');
        $photos = HALOPagination::getData($album->photos());
        $builder = HALOUIBuilder::getInstance('', 'photo.photo_album_view', array('photos' => $photos));
        $photoListHtml = $builder->fetch();

        $paginationHtml = $photos->links('ui.pagination_auto')->__toString();

        HALOResponse::setZonePagination('halo-albums-wrapper', $paginationHtml);
		
        HALOResponse::setZoneContent('halo-albums-wrapper', $photoListHtml, $mode);

        // HALOResponse::addScriptCall('halo.util.setHtml', '.halo-pg-result-' . Str::slug(__halotext('Albums')), 'Back to albums');
		if($userId){
			HALOResponse::addZoneScript('halo-albums-wrapper', 'halo.util.setHtml', '.halo-pg-result-' . Str::slug(__halotext('Albums')), '<span class="halo-inline-text">' . $album->name . '</span> | <a href="javascript:void(0)" title="'. __halotext('Back to albums') .'" onclick="halo.photo.showUserAlbums(\'' . $userId .'\')">'. __halotext('Back') .'</a>');
		}
		
        return HALOResponse::sendResponse();
    }

    /**
     * update the editing photo in upload2edit-photo tab popup
     *
     * @param  int $photoId
     * @return JSON
     */
    public function ajaxSelectListingPhoto($photoId)
    {

        $photo = HALOPhotoModel::find($photoId);
        Redirect::ajaxError(__halotext('Invalid photo ID'))->when(!$photo)->apply();

        HALOResponse::addScriptCall('halo.photo.updateEditingPhoto', '.halo-photo-edit', $photo->id, $photo->getPhotoURL());
        return HALOResponse::sendResponse();

    }

    /*
    add tag user
     */
    /**
     * [ajaxAddTag description]
     * @param  int $photoId
     * @param  int $userId
     * @param  string $x1
     * @param  string $y1
     * @param  string $x2
     * @param  string $y2
     * @return JSON
     */
    public function ajaxAddTag($photoId, $userId, $x1, $y1, $x2, $y2)
    {
        $photo = HALOPhotoModel::find($photoId);

        $my = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Invalid photo'))
            ->when(is_null($photo->id))
            ->apply();

        if (!HALOTagAPI::tagUser($photo, $userId, HALOParams::getInstance(array('x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2), 'array')->toString())) {
            return HALOResponse::sendResponse();
        } else {
            //insert the tag text to photo
            $user = HALOUserModel::getUser($userId);
            $removable = ($my && ($my->id == $userId || $my->id == $photo->owner->id));
            HALOResponse::addScriptCall('halo.photo.showTag', $user->getDisplayLink(), $userId, $x1, $y1, $x2, $y2, $removable);

            return HALOResponse::sendResponse();

        }

    }

    /**
     * remove tag user
     *
     * @param  int $photoId
     * @param  int $userId
     * @return JSON
     */
    public function ajaxRemoveTag($photoId, $userId)
    {
        $photo = HALOPhotoModel::find($photoId);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid photo'))
            ->when(is_null($photo->id))
            ->apply();
        //rule: only photo owner and tagged user can remove photo tag
        $my = HALOUserModel::getUser();
        if (!($my && ($my->id == $userId || $my->id == $photo->owner->id))) {
            return HALOResponse::sendResponse();
        }
        if (!HALOTagAPI::removeTagUser($photo, $userId)) {
            return HALOResponse::sendResponse();
        } else {
            //insert the tag text to photo
            HALOResponse::addScriptCall('halo.photo.clearTag', $userId);

            return HALOResponse::sendResponse();

        }

    }

    /**
     * remove tag user
     *
     * @param  int  $photoId
     * @param  int $confirm
     * @return JSON
     */
    public function ajaxRemovePhoto($photoId, $confirm = 0)
    {
        $photo = HALOPhotoModel::find($photoId);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid photo'))
            ->when(is_null($photo->id))
            ->apply();
        //rule: only photo owner and tagged user can remove photo tag
        $my = HALOUserModel::getUser();
        if (!$confirm) {
            //show the confirm form
            $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
                ->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('Are you sure you want to delete this photo?'), 'type' => 'warning')))
            ;
            $title = __halotext('Delete Photo');
            $content = $builder->fetch();
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Delete'), "onclick" => "halo.photo.remove('" . $photoId . "','1')", "icon" => "check", 'class' => 'halo-btn halo-btn-primary'));
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm')
            ;
            return HALOResponse::sendResponse();
        } else {
            if (!HALOPhotoAPI::remove($photo)) {
                return HALOResponse::sendResponse();
            } else {
                HALOResponse::addScriptCall('halo.photo.removePhotoUI', $photoId);
                return HALOResponse::sendResponse();
            }
        }

    }

}
