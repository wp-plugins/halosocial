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

class HALOPermissionModel extends HALOModel
{

	public $timestamps = false;
	
    protected $table = 'halo_permissions';

    protected $fillable = array('name', 'description');
    

	private $source = 'Unknown';
	
    //define relationship with role model
    /**
     * 
     * @return  Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('HALORoleModel', 'halo_permission_role', 'permission_id', 'role_id')
                    ->withPivot('ordering')
                    ->orderBy('ordering', 'asc');
    }

    /**
     * Validate post data rules
     *
     * @return Array permission string
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required|regex:#^[a-zA-Z0-9]+\.[a-zA-Z0-9]+$#',
        ));

    }

    /**
     * Return display name for this permission
     *
     * @return String display name
     */
	public function getDisplayName() {
		$parts = explode('.', $this->name);
		$parts = array_reverse($parts);
		return ucwords(implode(' ', $parts));
	}

    /**
     * Return all available permission options
     *
     * @return Object permissions
     */
    public static function getPermissionOptions()
    {
		$permissions = HALOPermissionModel::all();
		$options = HALOUtilHelper::collection2options($permissions, 'id', 'getDisplayName', false);
        return $options;
    }

	
    /**
     * Return roles string that seperate by comma
     *
     * @return String roles string
     */
    public function getRoleStr()
    {
        $roleStr = '';
        $roleArr = array();
        foreach ($this->roles as $role) {
            $roleArr[] = $role->name;
        }
        $roleStr = implode(',', $roleArr);

        return $roleStr;
    }

    /**
     * Parse role string to array that ready to sync
     *
     * @param $roleId
     * @return Array roles string ready to sync
     */
    public function parseroleStr($roleStr)
    {
        $roleArr = explode(',', $roleStr);
        //get list of role by their name
        $roles = HALORoleModel::whereIn('name', $roleArr)->get();

        //ordering is important, need to parse the ordering also
        $rtn = array();
        foreach ($roles as $role) {
            $rtn[$role->id] = array('ordering' => array_search($role->name, $roleArr));
        }
        return $rtn;
    }

    /**
     * Return source name of the permission
     *
     * @return String source name of this permission
     */
	public function getSourceName() {
		Event::fire('auth.getPermissionSource', array(&$this));		
		return $this->source;
	}
	
	
	public function setSourceName($source) {
		return $this->source = $source;
	}
}
