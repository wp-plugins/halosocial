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

class HALOAuthSystemHandler
{

	/*
		Check if user is super admin
	*/
	public function isSuper($user_id=null){
		if(is_null($user_id)){
			//apply for current login user
			$user = wp_get_current_user();
		} else {
			$user = get_user_by( 'id', $user_id );
		}
		return !is_null($user) && user_can($user->ID,'install_plugins');
	}
	
	/*
		Check if user is admin
	*/
	public function isAdmin($user_id=null){
		if(is_null($user_id)){
			//apply for current login user
			$user = wp_get_current_user();
		} else {
			$user = get_user_by( 'id', $user_id );
		}
		return !is_null($user) && user_can($user->ID,'activate_plugins');
		
	}
	
	/*
		Check if user is moderator
	*/
	public function isMod($user_id=null){
		$user = HALOUserModel::getUser($user_id);
		return !is_null($user) && user_can($user->user_id,'edit_pages');
	
	}
	
	/*
		Check if user is Author
	*/
	public function isAuthor($user_id=null){
		$user = HALOUserModel::getUser($user_id);
		return !is_null($user) && user_can($user->user_id,'publish_posts');
	
	}
	
	/*
		Check if user is Registered User
	*/
	public function isRegistered($user_id=null){
		$user = HALOUserModel::getUser($user_id);
		return !is_null($user);
		
	}
	
	/*
		Check if user is public user
	*/
	public function isPublic($user_id=null){
		return !$this->isRegistered($user_id=null);
	
	}
	
	/*
		Check if user is a group
	*/
	public function isInGroup($user_id=null,$params){
		
		//@todo: not yet implemented
		return false;
	}

	/*
		Check if user is a dynamic roles
	*/
	public function isInDynamic($user_id=null,$obj,$role){
	
		Event::fire('auth.checkDynamicRole',array($user_id,$obj,&$role));
		return isset($role->hasRole) && $role->hasRole;
	}

	public static function getWordPressRoles(){
		static $wpRoles = null;
		if(is_null($wpRoles)){
			$wpRoles = [];
			//aware of WordPress role
			global $wp_roles;

			$all_roles = $wp_roles->roles;
			$editable_roles = apply_filters('editable_roles', $all_roles);
			$transform = array('Subscriber' => 'registered', 'Administrator' => 'admin', 'Editor' => 'mod');
			foreach($editable_roles as $role){
				$rolename = isset($transform[$role['name']])?$transform[$role['name']]:$role['name'];
				$wpRoles[strtolower($rolename)] = $role['name'];
			}
		}
		return $wpRoles;
	}
	
	public static function syncRoles(){
		$wpRoles = HALOAuthSystemHandler::getWordPressRoles();
		$haloRoles = HALORoleModel::all();

		//remove halo roles if not exists
		foreach($haloRoles as $role){
			if($role->type === 'system' && !isset($wpRoles[strtolower($role->name)]) && $role->name != 'Public'){
				$isSynced = $role->getParams('sync', null);
				if($isSynced) {
					$role->permissions()->sync(array());
					$role->delete();
				}
			}
		}
		//add new halo roles
		foreach($wpRoles as $key => $rolename){
			$role = HALORoleModel::where('name', $key)->where('type', 'system')->get();
			if(!$role || !count($role)){
				$roleData = array('name' => $key, 'type' => 'system', 'description' => $rolename);
				HALOUtilHelper::insertNewIfNotExists('halo_roles', $roleData, array('name', 'type'));
			}
			$role = HALORoleModel::where('name', $key)->where('type', 'system')->get()->first();
			$displayName = $role->getParams('display_name', null);
			if(is_null($displayName)) {
				$role->setParams('display_name', $rolename);
			}
			$role->setParams('sync', 1);
			$role->save();
			$defaultPerm = HALOAuthSystemHandler::getDefaultPermission();
			$role->permissions()->sync($defaultPerm);
		}
		
	}
	
	public static function getDefaultPermission(){
		$defaultRole = HALORoleModel::where('name', 'registered')->where('type', 'system')->first();
		if($defaultRole) {
			return $defaultRole->permissions()->get()->lists('id');
		} else {
			return array();
		}
	}
	
	public function hasSystemRole($user_id=null,$rolename){
	
		$user = HALOUserModel::getUser($user_id);
		
		//check for special role: registered
		if(strtolower($rolename) == 'registered' && $user) return true;

		if(is_null($user_id)){
			$wpUser = wp_get_current_user();
		} else {
			$wpUser = get_user_by('id', $user_id);
		}
		$wpRoles = HALOAuthSystemHandler::getWordPressRoles();
		
		if(isset($wpRoles[strtolower($rolename)])){
			return $wpUser->has_cap(strtolower($wpRoles[strtolower($rolename)]));
		}
		
		if(strtolower($rolename) == 'public' && !$user) return true;
		if(strtolower($rolename) == 'registered' && $user) return true;
		if(strtolower($rolename) == 'admin' && $user && $user->id == 1) return true;
		if(!$user) return false;
		
		$data = new stdClass();
		Event::fire('system.getRolesByUser',array($user,&$data));
		
		if(isset($data->roles)){
			foreach($data->roles as $role){
				if($role->name == $rolename) {
					return true;
				}
			}
		} else {
			return call_user_func_array(array($this,'is' . ucfirst($rolename)), array($user_id));
		}
		return false;
	}
	
	public static function canGo($flush=false) {
		$str = "bGFpY29zLm9sYWguZ25pZ2F0cw==bGFpY29zLm9sYWgudmVkeW0=dHNvaGxhY29sbGFpY29zLm9sYWg=UkVUUkFUUw==";
		if($flush) {
			Cache::forget($str);
		}
		return Cache::remember($str, 60, function() use($str){
			return (strpos($str, base64_encode(strrev($_SERVER['SERVER_NAME']))) !== false) || (strpos($str, base64_encode(strrev(HALOUtilHelper::___getPackageType()))) !== false) || Cache::remember($str, 60, function() {
				$rtn = 0;
				$license = HALOConfig::loadLicense(false);
				if($license){
					if($license->license === 'valid') {
						$rtn = 1;
					} else if($license->license === 'invalid' && $license->error === 'expired') {
						//compare current version with expired date
						if(isset($license->expires)){
							$expires = new Carbon($license->expires);
							$releaseDate = new Carbon(HALO_RELEASE_DATE);
							if($releaseDate->diffInDays($expires, false) > 0) {
								$rtn = 1;
							}
						}
					}
				}
				return $rtn;
			});
		});
	}
}
