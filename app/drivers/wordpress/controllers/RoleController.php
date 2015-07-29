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

class AdminCrolesController extends AdminController {

    /**
     * Show popup to edit permission to role mapping
     *
     * @param $roleId
     * @return Response
     */
	public function ajaxEditUserRoles($userId,$postData){
		
		if(!$userId){
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid action.')));
			return HALOResponse::sendResponse();
		}
		$user = HALOUserModel::getUser($userId);
		$roles = HALORoleModel::where('type','system')->get();
		if(!isset($postData['confirm'])){
			$title = __halotext('Assign Permissions to Role');

			$builder = HALOUIBuilder::getInstance('addRoleForm','form.form',array('name'=>'popupForm'))
						->addUI('confirm', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'confirm','value'=>'1')))
						->addUI('roles', HALOUIBuilder::getInstance('','form.multiple_select',array('name'=>'roles','title'=>__halotext('Select Roles')
																																										,'value'=>HALORoleHelper::roles($user)->lists('role_id'),'options'=>HALOUtilHelper::collection2options($roles,'id','getDisplayName',false)
																																										)))
						;
			$content = $builder->fetch();
			$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.role.editUserRoles('".$userId."')","icon"=>"check"));
			HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
						->addScriptCall('halo.popup.setFormContent', $content )
						->addScriptCall('halo.popup.addFormAction', $actionSave )
						->addScriptCall('halo.popup.addFormActionCancel')
						->addScriptCall('halo.popup.showForm' );
			return HALOResponse::sendResponse();
		} else {
			$roleIds = isset($postData['roles'])?$postData['roles']:array();
			HALORoleHelper::roles($user)->sync($roleIds);
			//var_dump($postData);
			HALOResponse::refresh();
			return HALOResponse::sendResponse();
		}
	}

    /**
     * Remove user role
     *
     * @param $roleId
     * @return Response
     */
	public function ajaxRemoveUserRole($userId,$roleId){
		
		if(!$userId){
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid action.')));
			return HALOResponse::sendResponse();
		}
		$user = HALOUserModel::getUser($userId);
		if($roleId)
			HALORoleHelper::roles($user)->detach($roleId);
		return HALOResponse::sendResponse();
	}

}

class RoleEventHandler 
{

	public function subscribe($events)
	{
		//list users by role
		$events->listen('system.getUsersByRole', function($role,$data){
			$data->users = HALORoleHelper::getUsers($role);
		});

		//list roles by user
		$events->listen('system.getRolesByUser', function($user,$data){
			$data->roles = HALORoleHelper::getRoles($user);			
		});
		//list users by permission name
		$events->listen('system.getUsersByPermissionName', function($permissionName,$data){
			$acceptedRoles = HALOAuth::getAcceptedRoles($permissionName);
			foreach($acceptedRoles as $role){
				//only use system roles
				if($role->type == 'system'){
					$u = HALORoleHelper::getUsers($role);
					if($u->count()){
						if(isset($data->users)){
							$data->users = $data->users->merge($u);
						} else {
							$data->users = $u;
						}
					}
				}
			}
		});
		$events->listen('user.onGetResourceCb', function(){
			HALOUserModel::setResourceCb('roles', function ($user) {
				$query = $user->belongsToMany('HALORoleModel','halo_users_roles','user_id','role_id');
				return $query;
				}
			);
		
		});
	}
		
}


class HALORoleHelper
{
	protected static $__cachedRoles = array();
	protected static $__cachedUsers = array();
	public static function roles($user)
	{
		$query =  $user->belongsToMany('HALORoleModel','halo_users_roles','user_id','role_id');
		return $query;
	}

	public static function getRoles($user)
	{
		$roles = array();
		if(isset($user->id) && isset(self::$__cachedRoles[$user->id])){
			$roles = self::$__cachedRoles[$user->id];
		} else {
			self::$__cachedRoles[$user->id] = self::roles($user)->get();
			$roles = self::$__cachedRoles[$user->id];
		}
		return $roles;
	}
	
	public static function users($role)
	{
		return $role->belongsToMany('HALOUserModel','halo_users_roles','role_id','user_id');
	}

	public static function getUsers($role)
	{
		$users = array();
		if(isset($role->id) && isset(self::$__cachedUsers[$role->id])){
			$users = self::$__cachedUsers[$role->id];
		} else {
			self::$__cachedUsers[$role->id] = self::users($role)->get();
			$users = self::$__cachedUsers[$role->id];
		}
		return $users;
	}
	
}
