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

class AdminRolesController extends AdminController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Roles Management');

		// Toolbar
		if(HALOConfig::isDev()) {
			if(HALOAuth::can('feature.acl')){
				HALOToolbar::addToolbar('Delete Role','','',"halo.popup.confirmDelete('Delete Role Confirm','Are you sure to delete this role?','halo.role.deleteSelectedRole()')",'times');
				HALOToolbar::addToolbar('Add Role','','','halo.role.showEditRoleForm(0)','plus');
			} else {
				HALOUtilHelper::getVersionToolbar();
			}
		}
		HALOToolbar::addToolbar('Sync Roles','halo-btn-primary','','halo.role.syncRoles()','refresh');

        // Grab all the roles
		if(HALOConfig::isDev()){
			$roles = new HALORoleModel();
		} else {
			$roles = HALORoleModel::where('type', 'system');
		}
		
		$roles = HALOPagination::getData($roles->with('permissions'));
		
        // Show the page
        return View::make('admin/roles/roles', compact('roles', 'title'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getPermissions()
    {
        // Title
        $title = __halotext('Permissions Management');
		// Toolbar
		if(HALOConfig::isDev()) {
			if(HALOAuth::can('feature.acl')){
				HALOToolbar::addToolbar('Delete Permission','','',"halo.popup.confirmDelete('Delete Permission Confirm','Are you sure to delete this permission?','halo.role.deleteSelectedPermission()')",'times');
				HALOToolbar::addToolbar('Add Permission','','','halo.role.showEditPermissionForm(0)','plus');
			} else {
				HALOUtilHelper::getVersionToolbar();
			}
		}

        // Grab all the permissions
        $permissions = new HALOPermissionModel();
		
		$permissions = HALOPagination::getData($permissions->with('roles'));
		
        // Show the page
        return View::make('admin/roles/permissions', compact('permissions', 'title'));
    }

    /**
     * Show popup to edit permission to role mapping
     *
     * @param $roleId
     * @return Response
     */
	public function ajaxShowEditPermissionToRole($roleId=0){
		
		$role = HALORoleModel::with('permissions')->find($roleId);
        // Title
		$title = __halotext('Assign Permissions to Role');
		//redirect page if role is not found
		Redirect::ajaxError(__halotext('Role does not exists'))
				->when(empty($role))
				->apply();

		// $builder = HALOUIBuilder::getInstance('editRolePermission','form.form',array('name'=>'popupForm'))
					// ->addUI('permissions', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'permissions','title'=>'Permissions','placeholder'=>'Enter permission name, seperate by comma','value'=>$role->getPermisionStr())))
					// ;
		$builder = HALOUIBuilder::getInstance('editRolePermission','form.form',array('name'=>'popupForm'))
					->addUI('permissions', HALOUIBuilder::getInstance('','form.multiple_select',array('name'=>'permissions','title'=>'Permissions',
						'options' => HALOPermissionModel::getPermissionOptions(),
						'placeholder'=>'Select permission to assign','value'=>$role->permissions->lists('id'))))
					;
		
		$content = $builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.role.savePermissionToRole('".$role->id."')","icon"=>"check"));
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

    /**
     * Save the permission to role mapping
     *
     * @param $roleId
     * @param $postData
     * @return Response
     */
	public function ajaxSavePermissionToRole($roleId, $postData){
		//form content
		$role = HALORoleModel::with('permissions')->find($roleId);
		
		Redirect::ajaxError(__halotext('Role does not exists'))
				->when(empty($role))
				->apply();

		$permissionIds = isset($postData['permissions'])?$postData['permissions']:'';
		if(!$permissionIds) $permissionIds = array(-1);

		//verify permission
		$permissionsArr = HALOPermissionModel::whereIn('id', $permissionIds)->lists('id');
		
		$role->permissions()->sync($permissionsArr);
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
		
	}

    /**
     * Show popup to edit role to permission mapping
     *
     * @param $permissionId
     * @return Response
     */
	public function ajaxShowEditRoleToPermission($permissionId=0){
				
		$permission = HALOPermissionModel::with('roles')->find($permissionId);
        // Title
		$title = __halotext('Map Roles to Permissions');
		//show error on permission does not exist
		Redirect::ajaxError(__halotext('Permission does not exist'))
				->when(empty($permission))
				->apply();

		// $builder = HALOUIBuilder::getInstance('editRolePermission','form.form',array('name'=>'popupForm'))
					// ->addUI('permissions', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'roles','title'=>'Roles','placeholder'=>'Enter role names, seperate by comma. Order is important in matching role','value'=>$permission->getRoleStr())))
					// ;

		$builder = HALOUIBuilder::getInstance('editRolePermission','form.form',array('name'=>'popupForm'))
					->addUI('roles', HALOUIBuilder::getInstance('','form.multiple_select',array('name'=>'roles','title'=>'Roles',
						'options' => HALORoleModel::getRoleOptions(),
						'value'=>$permission->roles->lists('id'))))
					;
		$content = $builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.role.saveRoleToPermission('".$permission->id."')","icon"=>"check"));
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

    /**
     * Save the permission to permission mapping
     *
     * @param $permissionId
     * @param $postData
     * @return Response
     */
	public function ajaxSaveRoleToPermission($permissionId, $postData){
		//form content
		$permission = HALOPermissionModel::with('roles')->find($permissionId);
		
		Redirect::ajaxError(__halotext('Permission does not exist'))
				->when(is_null($permission))
				->apply();

		$rolesIds = isset($postData['roles'])?$postData['roles']:'';
		if(!$rolesIds) $rolesIds = array(-1);

		$rolesArr = HALORoleModel::whereIn('id', $rolesIds)->lists('id');
		
		$permission->roles()->sync($rolesArr);
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
		
	}


    /**
     * Show popup to edit/create Role
     *
     * @param $roleId
     * @return Response
     */
	public function ajaxShowEditRoleForm($roleId=0){
		
		//determind edit or create mode
		$mode = ($roleId == 0)?'create':'edit';

		//init role model
		if($mode == 'create'){
			//initialize default category 
			$role = new HALORoleModel();
			$role->id = 0;
			// Title
			$title = __halotext('Create New Role');
			$roleType = 'system';
		} else {
			$role = HALORoleModel::find($roleId);
            // Title
        	$title = __halotext('Edit Role');
			Redirect::ajaxError(__halotext('Role does not exists'))
					->when(empty($role->id))->apply();
			$roleType = $role->type;
		}
		
		if($mode != 'create' && $roleType != 'system') {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}
		$builder = HALOUIBuilder::getInstance('editRole','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'name','value'=>$role->name)))
					->addUI('display_name', HALOUIBuilder::getInstance('','form.text',array('name'=>'display_name','title'=>'Name','placeholder'=>'Role Name','value'=>$role->getDisplayName())))
					->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'type','title'=>'Type','placeholder'=>'Role Type','value'=>$roleType, 'disabled'=>'disabled', 'class'=>'hidden',
																					'options'=>array(array('value'=>'','title'=>'-- Select Type --'),
																											array('value'=>'system','title'=>'System'),
																											array('value'=>'context','title'=>'Context'),
																											array('value'=>'dynamic','title'=>'Dynamic'),
																									)
																					)))
					->addUI('description', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'description','title'=>'Description','placeholder'=>'Description','value'=>$role->description)))
					->addUI('params', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'params','title'=>'Params String','placeholder'=>'Params String','value'=>HALOParams::getInstance($role->params)->toQuery(','), 'disabled'=>$disabled)))
					;
		$content = $builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.role.saveRole('".$roleId."')","icon"=>"check"));
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

    /**
     * Save roles
     *
     * @param $roleId
     * @param $postData
     * @return Response
     */
	public function ajaxSaveRole($roleId, $postData){
		//form content
		$role = HALORoleModel::find($roleId);
		if(is_null($role)){
			$role = new HALORoleModel();
		}
		//verify display name
		if(!isset($postData['display_name']) || empty($postData['display_name'])) {
			HALOResponse::addMessage(HALOError::getMessageBag()->add('display_name', __halotext('Please enter role name')));
			return HALOResponse::sendResponse();
		}
		//validate data
		if($role->bindData($postData)->validate()->fails()){			
            $msg = $role->getValidator()->messages();
            HALOResponse::addMessage($msg);
			return HALOResponse::sendResponse();
		} else {
			//set role display name
			$role->setParams('display_name', $postData['display_name']);
			$role->save();
			HALOResponse::refresh();
			
		}
				
		return HALOResponse::sendResponse();

	}

    /**
     * Remove role.
     *
     * @param array $roleIds list of roles
     * @return JAXResponse
     */
	public function ajaxDeleteRole($roleIds){
		$roleIds = (array)$roleIds;
		
		//@rule: delete role will delete role-permission mapping also
		DB::table('halo_permission_role')->whereIn('role_id', $roleIds)->delete();
		
		HALORoleModel::destroy($roleIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Role was deleted', 'Roles were deleted', count($roleIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}


    /**
     * Show popup to edit/create Permission
     *
     * @param $roleId
     * @return Response
     */
	public function ajaxShowEditPermissionForm($permissionId=0){
		
		//determind edit or create mode
		$mode = ($permissionId == 0)?'create':'edit';

		//init permission model
		if($mode == 'create'){
			//initialize default category 
			$permission = new HALOPermissionModel();
			$permission->id = 0;
			// Title
			$title = __halotext('Create New Permission');
		
		} else {
			$permission = HALOPermissionModel::find($permissionId);
            // Title
        	$title = __halotext('Edit Permission');
			Redirect::ajaxError(__halotext('Permission does not exist'))
					->when(empty($permission->id))->apply();
		}
						
		$builder = HALOUIBuilder::getInstance('editPermission','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Permission Name in the format: <asset>.<action>','value'=>$permission->name)))
					->addUI('description', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'description','title'=>'Description','placeholder'=>'Description','value'=>$permission->description)))
					;
		$content = $builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.role.savePermission('".$permissionId."')","icon"=>"check"));
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

    /**
     * Save permissions
     *
     * @param $permissionId
     * @param $postData
     * @return Response
     */
	public function ajaxSavePermission($permissionId, $postData){
		//form content
		$permission = HALOPermissionModel::find($permissionId);
		if(is_null($permission)){
			$permission = new HALOPermissionModel();
		}
		
		//validate data
		if($permission->bindData($postData)->validate()->fails()){
			
			$error = $permission->getValidateMessages();
			//ajax redirect to display function to display error
			Redirect::ajaxError(__halotext('Save permission failed'))
					->setArgs(array($permissionId))
					->withErrors($permission->getValidator())
					->with('errorMsg',$error)
					->apply();
		} else {
			$permission->save();
			HALOResponse::refresh();
			
		}
				
		return HALOResponse::sendResponse();

	}

    /**
     * Remove permission.
     *
     * @param array $permissionIds list of permissions
     * @return JAXResponse
     */
	public function ajaxDeletePermission($permissionIds){
		$permissionIds = (array)$permissionIds;
		
		//@rule: delete permission will delete role-permission mapping also
		DB::table('halo_permission_role')->whereIn('permission_id', $permissionIds)->delete();
		
		HALOPermissionModel::destroy($permissionIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Permission was deleted', 'Permissions were deleted', count($permissionIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}

   /**
     * Save permissions
     *
     * @param $permissionId
     * @param $postData
     * @return Response
     */
	public function ajaxRemovePermissionFromRole($permissionId, $roleId){
		$role = HALORoleModel::with('permissions')->find($roleId);
		
		Redirect::ajaxError(__halotext('Role does not exists'))
				->when(empty($role))
				->apply();
	
		$role->permissions()->detach($permissionId);
		
		//HALOResponse::refresh();
		return HALOResponse::sendResponse();

	}
	
   /**
     * Sync WordPress roles
     *
     * @return Response
     */
	public function ajaxSyncRoles(){
		$wpRoles = HALOAuthSystemHandler::syncRoles();
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
	}
	
}