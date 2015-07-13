<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class AuthSeeder extends Seeder {

    public function run()
    {
		try {

			//insert roles
			$roles = $this->getRoleSettings();
			foreach($roles as $role) {
				HALOUtilHelper::insertNewIfNotExists('halo_roles', $role, array('name', 'type'));
			}
			
			//insert permission
			$permissions = $this->getPermissionSettings();
			foreach($permissions as $permission) {
				HALOUtilHelper::insertNewIfNotExists('halo_permissions', $permission, array('name'));
			}
			
			//permission to role mapping
			//backend.view
			$permission = HALOPermissionModel::with('roles')->where('name','backend.view')->first();
			$rolesArr = $permission->parseRoleStr('Admin,Mod');
			$permission->roles()->sync($rolesArr);
			
			//site.view
			$permission = HALOPermissionModel::with('roles')->where('name','site.view')->first();
			$rolesArr = $permission->parseRoleStr('Public,Registered,Author,Mod,Admin');
			$permission->roles()->sync($rolesArr);
			
			//user.view
			$permission = HALOPermissionModel::with('roles')->where('name','user.view')->first();
			$rolesArr = $permission->parseRoleStr('Registered,Author,Mod,Admin');
			$permission->roles()->sync($rolesArr);
			
			//user.edit
			$permission = HALOPermissionModel::with('roles')->where('name','user.edit')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);
			
			//user.delete
			$permission = HALOPermissionModel::with('roles')->where('name','user.delete')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);

			//user.changeProfile
			$permission = HALOPermissionModel::with('roles')->where('name','user.changeProfile')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);
					
			//comment.view
			$permission = HALOPermissionModel::with('roles')->where('name','comment.view')->first();
			$rolesArr = $permission->parseRoleStr('Registered,Author,Mod,Admin');
			$permission->roles()->sync($rolesArr);
			
			//comment.create
			$permission = HALOPermissionModel::with('roles')->where('name','comment.create')->first();
			$rolesArr = $permission->parseRoleStr('CommentCreator,Admin');
			$permission->roles()->sync($rolesArr);
			
			//comment.edit
			$permission = HALOPermissionModel::with('roles')->where('name','comment.edit')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);
			
			//comment.delete
			$permission = HALOPermissionModel::with('roles')->where('name','comment.delete')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);
			
			//activity.create
			$permission = HALOPermissionModel::with('roles')->where('name','activity.create')->first();
			$rolesArr = $permission->parseRoleStr('Registered');
			$permission->roles()->sync($rolesArr);
			
			//activity.edit
			$permission = HALOPermissionModel::with('roles')->where('name','activity.edit')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin,ContentAdmin');
			$permission->roles()->sync($rolesArr);
			
			//activity.delete
			$permission = HALOPermissionModel::with('roles')->where('name','activity.delete')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin,ContentAdmin');
			$permission->roles()->sync($rolesArr);
			
			//review.create
			$permission = HALOPermissionModel::with('roles')->where('name','review.create')->first();
			$rolesArr = $permission->parseRoleStr('Reviewer,Admin');
			$permission->roles()->sync($rolesArr);
			
			//review.edit
			$permission = HALOPermissionModel::with('roles')->where('name','review.edit')->first();
			$rolesArr = $permission->parseRoleStr('Owner,Admin');
			$permission->roles()->sync($rolesArr);
			
			//review.delete
			$permission = HALOPermissionModel::with('roles')->where('name','review.delete')->first();
			$rolesArr = $permission->parseRoleStr('Mod,Admin');
			$permission->roles()->sync($rolesArr);
			
			//review.approve
			$permission = HALOPermissionModel::with('roles')->where('name','review.approve')->first();
			$rolesArr = $permission->parseRoleStr('Mod,Admin');
			$permission->roles()->sync($rolesArr);
		
		} catch (\Exception $e) {
			return;
		}
    }

	/*
		return core role settings
	*/
	public function getRoleSettings() {
		return  $roles = array(
        	array(
            	'name'      	=> 'Admin',
            	'type'      	=> 'system',
            	'description' 	=> 'Site Admininistrator',
        	),
        	array(
            	'name'      	=> 'Mod',
            	'type'      	=> 'system',
            	'description' 	=> 'Site moderator',
           ),
            array(
            	'name'      	=> 'Author',
            	'type'      	=> 'system',
            	'description' 	=> 'Site Authors',
            ),
            array(
            	'name'      	=> 'Registered',
            	'type'      	=> 'system',
            	'description' 	=> 'Site Registered Users',
            ),
            array(
            	'name'      	=> 'Public',
            	'type'      	=> 'system',
            	'description' 	=> 'Publilc Users',
            ),
            array(
                'name'      	=> 'Follower',
                'type'      	=> 'context',
            	'description' 	=> 'Follower context dependency users'
            ),
            array(
              'name'      	=> 'Owner',
            	'type'		 	=> 'context',
            	'description' 	=> 'Owner context dependency users'
            ),
            array(
              'name'      	=> 'Member',
            	'type'		 	=> 'context',
            	'description' 	=> 'Member context dependency users'
            )       	
            ,array(
              'name'      	=> 'CommentCreator',
            	'type'		 	=> 'context',
            	'description' 	=> 'User that can create new comment on a content'
            )       	
            ,array(
              'name'      	=> 'Reviewer',
            	'type'		 	=> 'context',
            	'description' 	=> 'User that can create new review on a content'
            )       	
            ,array(
              'name'      	=> 'ContentAdmin',
            	'type'		 	=> 'dynamic',
            	'description' 	=> 'Check if user has admin role for a specific content'
            )
		);
	}

	/*
		return core permissing settings
	*/
	public function getPermissionSettings() {
		return  $permission = array(
        	array(
            	'name'		 	=> 'backend.view',
            	'description'   => 'Allow to view backend'
        	),
        	array(
            	'name'		 	=> 'site.view',
            	'description'   => 'Allow to view frontend'
            ),
            array(
            	'name'		 	=> 'user.view',
            	'description'   => 'Allow to view user profile'
            ),
            array(
            	'name'		 	=> 'user.edit',
            	'description'   => 'Allow to edit user profile'
            ),
            array(
            	'name'		 	=> 'user.delete',
            	'description'      	=> 'Allow to delete user profile'
            ),       	
            array(
            	'name'		 	=> 'comment.view',
            	'description'   => 'Allow to comment'
            ),
            array(
            	'name'		 	=> 'comment.create',
            	'description'   => 'Allow to create new comment'
            ),
            array(
            	'name'		 	=> 'comment.edit',
            	'description'   => 'Allow to edit comment'
            ),
            array(
            	'name'		 	=> 'comment.delete',
            	'description'      	=> 'Allow to delete comment'
            ),
            array(
            	'name'		 	=> 'activity.create',
            	'description'   => 'Allow to create new activities'
            ),
            array(
            	'name'		 	=> 'activity.edit',
            	'description'   => 'Allow to edit activities'
            ),
            array(
            	'name'		 	=> 'activity.delete',
            	'description'      	=> 'Allow to delete an activity'
            )       	
            ,array(
            	'name'		 	=> 'user.changeProfile',
            	'description'      	=> 'Allow to change user profile type'
            )       	
            ,array(
            	'name'		 	=> 'review.create',
            	'description'      	=> 'Allow to create new review'
            )       	
            ,array(
            	'name'		 	=> 'review.edit',
            	'description'      	=> 'Allow to edit a review'
            )       	
            ,array(
            	'name'		 	=> 'review.approve',
            	'description'      	=> 'Allow to edit a review'
            )       	
            ,array(
            	'name'		 	=> 'review.delete',
            	'description'      	=> 'Allow to delete a review'
            )       	
		);
	}
}
