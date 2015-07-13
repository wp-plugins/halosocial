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

class BanController extends BaseController
{

    /**
     * Inject the models.
     * 
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }
			
/**
 * ajax handler to like an likable object
 * 
 * @param  string $user_id 
 */
	public function ajaxShowForm($user_id)
    {
		//check permission
		$my = HALOUserModel::getUser();
		$user = HALOUserModel::getUser($user_id);
		if (!$user) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Unknown user')));
			return HALOResponse::sendResponse();
		}
		if (!HALOBanModel::canBan($user)) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
			return HALOResponse::sendResponse();
		}

		//load ban settings
		$settings = HALOBanModel::getSettings($user);
		//get ban form for target user
		$builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
					->addUI('actions', HALOUIBuilder::getInstance('', 'ban.actions', array('settings' => $settings)))
					->addUI('reason', HALOUIBuilder::getInstance('', 'form.textarea', array('name' => 'reason', 'title' => __halotext('Reason'), 'value' => '')));
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name" => "Save", "onclick" => "halo.user.submitBan('" . $user_id . "')", "icon" => "check", 'class' => 'halo-btn halo-btn-primary'));
		$title = sprintf(__halotext('Ban user - %s'), $user->getDisplayName());
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm');
		return HALOResponse::sendResponse();
		
	}

/**
 * ajax handler to unlike an likable object
 * 
 * @param  string $user_id 
 * @param  string $actions 
 */
	public function ajaxSubmitForm($user_id, $actions) {

		//check permission.
		$my = HALOUserModel::getUser();
		$user = HALOUserModel::getUser($user_id);
		if (!$user) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Unknown user')));
			return HALOResponse::sendResponse();
		}
		if (!HALOBanModel::canBan($user)) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
			return HALOResponse::sendResponse();
		}
		if (HALOBanModel::banActions($user_id, $actions)) {
			HALOResponse::addScriptCall('halo.popup.close');
		}
		return HALOResponse::sendResponse();

		
	}
		
}
