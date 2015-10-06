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

class HALORoleModel extends HALOModel
{
	public $timestamps = false;
    protected $table = 'halo_roles';
    protected $fillable = array('name', 'type', 'description');

    //define relationship with permission model
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('HALOPermissionModel', 'halo_permission_role', 'role_id', 'permission_id')
                    ->withPivot('ordering')->orderBy('ordering', 'asc');
    }

    /**
     * Validate post data rules
     *
     * @return Array permission string
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required',
            'type' => 'required|in:system,context,group,dynamic',
        ));

    }
	
    /**
     * Return all available role options
     *
     * @return String permission string
     */
    public static function getRoleOptions($valueKey = 'id')
    {
		return HALOAuth::getRoleOptions($valueKey);
    }

    /**
     * Return permission string that seperate by comma
     *
     * @return String permission string
     */
    public function getPermisionStr()
    {
        $permissionStr = '';
        $permissionArr = array();
        foreach ($this->permissions as $permission) {
            $permissionArr[] = $permission->name;
        }
        $permissionStr = implode(',', $permissionArr);

        return $permissionStr;
    }

    /**
     * Parse permission string to array that ready to sync
     *
     * @param $roleId
     * @return Array permissions string ready to sync
     */
    public function parsePermissionStr($permissionStr)
    {
        $permissionArr = explode(',', $permissionStr);
        //get list of permission by their name
        $permissions = HALOPermissionModel::whereIn('name', $permissionArr)->get();

        $rtn = array();

        foreach ($permissions as $permission) {
            $rtn[] = $permission->id;
        }
        return $rtn;
    }

    /**
     * Get display name 
     * 
     * @return string
     */
    public function getDisplayName()
    {
		$displayName = $this->getParams('display_name', '');
		if(!$displayName) {
			//default system role
			$defaultRoles = array('Admin' => 'Admin', 'Mod' => 'Editor', 'Author' => 'Author', 'Registered' => 'Subscriber', 'Public' => 'Public users');
			if(isset($defaultRoles[$this->name])) {
				$displayName = $defaultRoles[$this->name];
			} else {
				$displayName = $this->name;
			}
		}
		return htmlentities($displayName);
    }

    /**
     * Get Url 
     * 
     * @return string
     */
    public function getUrl()
    {
        return URL::to('/admin/users?roleid=' . $this->id);

    }

    /**
     * Get display link 
     * 
     * @param  string $class 
     * @return string      
     */
    public function getDisplayLink($class = '')
    {

        return '<a href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

}
