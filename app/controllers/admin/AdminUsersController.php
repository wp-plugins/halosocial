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

class AdminUsersController extends AdminController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('User Management');

		// Toolbar
		HALOToolbar::addToolbar(__halotext('Add User'),'',URL::to('?app=admin&view=users&task=create'),'','plus');
//		HALOToolbar::addToolbar(__halotext('Block User'),'','','halo.user.showEditProfileForm(0)','unlock-alt');
		HALOToolbar::addToolbar(__halotext('Delete User'),'','',"halo.popup.confirmDelete('Delete User Confirm','Are you sure to delete this user?','halo.user.deleteSelectedUser()')",'times');
		
        // Grab all the profiles
        //$profiles = new HALOProfileModel();
		$model = new HALOUserModel();
		$model = UserModel::joinUserTable($model);
		$users = HALOPagination::getData($model);

        // Show the page
        return View::make('admin/users/index', compact('users', 'title'));

    }

    /**
     * Display online users.
     *
     * @return Response
     */
    public function getOnlineIndex()
    {
        // Title
        $title = __halotext('Online User Management');

		// Toolbar
		//HALOToolbar::addToolbar(__halotext('Force Logout'),'','','halo.online.forceLogout(0)','unlock-alt');
		
        // Grab all the profiles
        //$profiles = new HALOProfileModel();
		$model = new HALOOnlineuserModel();
		$users = HALOPagination::getData($model);

		//load HALOUserModel for all online users
		$userIds = array();
		foreach($users as $user){
			$userIds[] = $user->user_id;
		}
		HALOUserModel::init($userIds);
		
        // Show the page
        return View::make('admin/users/online', compact('users', 'title'));

    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function getCreate()
    {
		$user = new HALOUserModel();
		$user->id = 0;
		
		// Title
		$title = __halotext('Create a new user');

		// Toolbar
		HALOToolbar::addToolbar('Save','','',"halo.form.submit('halo-admin-form')",'save');
		HALOToolbar::addToolbar('Cancel','',URL::to('?app=admin&view=users'),'','undo');

		//get profile
		$profile = $user->getProfile();
		
		$profileFields = $user->getProfileFields()->get();
		
		return View::make('admin/users/create', compact('user', 'title', 'profile', 'profileFields'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postCreate()
    {
		$user = new HALOUserModel();
		$user->id = 0;

        // Validate the inputs
		$rules = $user->getValidateRule();
		
		$profileFields = $user->getProfileFields()->get();

		foreach($profileFields as $field){
		
			$rules = array_merge($rules,$field->toHALOField()->getValidateValueRule());
		}
		
        $validator = Validator::make(Input::all(), $rules);
		
        if ($validator->passes())
        {
			//create new HALOUserModel
			if(!($user = HALOUserModel::createNew(Input::all()))){
				$error = HALOResponse::getMessage();
				return Redirect::to('?app=admin&view=users&task=create')->withErrors($error)->withInput();
			}

			//save user profile & user point
			$user->profile_id = Input::get('profile_id',HALO_PROFILE_DEFAULT_USER_ID);
			$user->point_count = Input::get('point_count',0);
			
            if(!$user->save()){
				$error = HALOResponse::getMessage();
				return Redirect::to('?app=admin&view=users&task=create')->withErrors($error)->withInput();
			}
			
			//save user profile fields
			$profile = $user->getProfile()->first();
			$profile->saveFieldValues($user,Input::get('field',array()),Input::get('access',array()));
            return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->with('success', __halotext('The user was added successfully.'));
        } else {
            return Redirect::to('?app=admin&view=users&task=create')->withErrors($validator)->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $user
     * @return Response
     */
    public function getEdit($userId)
    {
		$user = HALOUserModel::getUser($userId);

        if ( $user->id )
        {
            // Title
        	$title = __halotext('Edit User');

			// Toolbar
			HALOToolbar::addToolbar('Save','','',"halo.form.submit('halo-admin-form')",'save');
			HALOToolbar::addToolbar('Cancel','',URL::to('?app=admin&view=users'),'','undo');

			// mode
        	$mode = 'edit';
			
			//get profile
			$profile = $user->getProfile();
			
			$profileFields = $user->getProfileFields()->get();
			

        	return View::make('admin/users/create_edit', compact('user', 'title', 'mode', 'profile', 'profileFields'));
        }
        else
        {
            return Redirect::to('admin/users')->with('error', __halotext('User does not exist'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $user
     * @return Response
     */
    public function postEdit($userId)
    {
		$user = HALOUserModel::getUser($userId);
        // Validate the inputs
		$rules = $user->getValidateRule();
		
		$profileFields = $user->getProfileFields()->get();

		foreach($profileFields as $field){
		
			$rules = array_merge($rules,$field->toHALOField()->getValidateValueRule());
		}
		
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes())
        {
			//rule: do not allow user to change username/email address from frontend

            $password = Input::get( 'password' );
            $passwordConfirmation = Input::get( 'password_confirmation' );

            if(!empty($password)) {
                if($password === $passwordConfirmation) {
                    $user->user->password = $password;
                    $user->user->password_confirmation = $passwordConfirmation;
                } else {
                    // Redirect to the new user page
					return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->with('error', __halotext('The passwords provided do not match.'));
                }
            } else {
                unset($user->password);
                unset($user->password_confirmation);
            }

			//save user name and display name
			if(($username = Input::get('username',null))){
				$user->user->setUserName($username);
			}
			if(($display_name = Input::get('display_name',null))){
				$user->user->setDisplayName($display_name);
			}
			//save user profile & user point
			$user->profile_id = Input::get('profile_id', HALOProfileModel::getDefaultProfileId('user'));
			$user->point_count = Input::get('point_count',0);

            // Save if valid. Password field will be hashed before save
            if(!$user->user->amend()){
				$error = HALOResponse::getMessage();
				return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->withErrors($error);
			}
            if(!$user->save()){
				$error = HALOResponse::getMessage();
				return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->withErrors($error);
			}
            // Save system user
            $user->user->save();
            
			//save user profile fields
			$profile = $user->getProfile()->first();
			$profile->saveFieldValues($user,Input::get('field',array()),Input::get('access',array()));
            return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->with('success', __halotext('The user was edited successfully.'));
        } else {
            return Redirect::to('?app=admin&view=users&task=edit&uid=' . $user->id)->withErrors($validator);
        }
    }

	/*
		ajax handler to delete users
	*/
	public function ajaxDeleteUser($userIds){
		//@rule: from the backend do not allow to delete yourself
		$my = HALOUserModel::getUser();

		$userIds = (array)$userIds;
		$userIds = array_diff($userIds, array($my->id));
		//loop on each profile to delete
		$title = __halotext('Delete User');
		if(empty($userIds)){
			HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setMessage', __halotext('Ultimate Truth: You cannot delete yourself.') , 'warning', true)
					->addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}')
					->addScriptCall('halo.popup.showForm' );
					
			return HALOResponse::sendResponse();
		
		}
		
		//loop through each users to delete. Peformance potential if delete large number of users
		$users = HALOUserModel::init($userIds);
		$count = 0;
		foreach($users as $user){
			if($user->delete()){
				$count++;
			}
		}
		
		HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setMessage', __halontext('%s User was deleted', '%s Users were deleted', $count) , 'warning', true)
					->addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}')
					->addScriptCall('halo.popup.showForm' );
				
		return HALOResponse::sendResponse();
	}

	/*
		ajax handler to force logout users
	*/
	public function ajaxForceLogout($ids){
		//@rule: from the backend do not allow to delete yourself
		$my = HALOUserModel::getUser();

		$userIds = array();
		
		$rows = HALOOnlineuserModel::find($ids);
		if($rows){
			$userIds = $rows->lists('user_id');
		}
		
		$userIds = (array)$userIds;
		$userIds = array_diff($userIds, array($my->id));
		//loop on each profile to delete
		$title = __halotext('Force Logout');
		if(empty($userIds)){
			HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setMessage', __halotext('You cannot logout yourself') , 'warning')
					->addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}')
					->addScriptCall('halo.popup.showForm' );
					
			return HALOResponse::sendResponse();
		
		}
		//loop through each users to delete. Peformance potential if delete large number of users
		$users = HALOUserModel::init($userIds);
		$count = 0;
		foreach($users as $user){
			$user->forceLogout();
			$count++;
		}
		
		HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setMessage', sprintf(__halotext('%s User(s) are logged out'),$count) , 'warning')
					->addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}')
					->addScriptCall('halo.popup.showForm' );
				
		return HALOResponse::sendResponse();
	}
	
}
