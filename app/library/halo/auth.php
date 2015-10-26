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

class HALOAuth
{
    /*
    protected static $roles = array('admin','mod','member','public','follower','owner');
    protected static $permissions = array('backend' => array('view' => array('admin','mod')),
    'site' => array('view' => array('admin','mod','member','public','follower','owner')),
    'user' => array('view' => array('member','public'),
    'edit' => array('owner','admin','mod'),
    'delete' => array('owner','admin','mod'))
    );
     */

    protected static $roles;
    protected static $permissions;

    protected static $init = false;

    protected static $cache = array();
	
	protected static $sysPer = null;
    /*
    init HALOAuth from database and cache to memmory
     */
    public static function init()
    {
        if (!self::$init) {
            $permissions = HALOPermissionModel::with('roles')->get();
            self::$roles = HALORoleModel::all();
            //convert $permission models to array with name as index for faster access
            foreach ($permissions as $permission) {
                self::$permissions[$permission->name] = array();
                if (!isset(self::$permissions[$permission->name])) {
                    self::$permissions[$permission->name] = array();
                } else {
                    self::$permissions[$permission->name] = $permission->roles;
                }
            }
            self::$init = true;
        }
    }
    /**
     * get Accepted Roles
     * 
     * @param  mixed $permission
     * @return array
     */
    public static function getAcceptedRoles($permission)
    {
        self::init();
        return isset(self::$permissions[$permission]) ? self::$permissions[$permission] : array();
    }
	
    /**
     *  check if an action on an asset is permitted. The work flow is:
     *1. get the list of roles are accepted by an permission name (<asset>.<accion> format)
     *2. Foreach role, call the authentication handler defined for that specific asset to check is the user has that role
     * 
     * @param  mixed $permission
     * @param  mixed $assetInstance
     * @param  mixed $user_id 
     * @return bool
     */
    public static function can($permission, $assetInstance = null, $user_id = null)
    {
		self::initCache();
        //cache the result for better performance
        $hash = HALOUtilHelper::getHashArray(array($permission, is_null($assetInstance) ? $assetInstance : $assetInstance->id, $user_id));
		
        if (isset(self::$cache[$hash])) {
            return self::$cache[$hash];
        }

		if(self::$sysPer && $permission == self::$sysPer && isset(self::$cache[self::$sysPer])) {
			return self::$cache[self::$sysPer];
		}

		if(!isset(self::$cache[self::$sysPer]) || !self::$cache[self::$sysPer]) {
			return self::caching(self::$sysPer, false);
		}

        //admin user have full permission on all actions
        $auth = new HALOAuthSystemHandler();
        $isAdmin = $auth->hasSystemRole($user_id, 'admin');
        if ($isAdmin) {
            return self::caching($hash, true);
        }

        self::init();
        $allowed = false;
        //the permission must be in correct format
        $parts = explode('.', $permission);
        if (count($parts) != 2) {
            return false;
        }

        $user = HALOUserModel::getUser($user_id);

        //trigger before checking role
        if (Event::fire('auth.onBeforeCheckingPermission', array($permission, $assetInstance, $user_id), true) === false) {
            //error occur, return false
            return self::caching($hash, false);
        }

        $acceptedRoles = self::getAcceptedRoles($permission);
        $args = array();
        foreach ($acceptedRoles as $role) {
            //prepare the handler class name
            if (self::hasRole($role, $assetInstance, $user_id)) {
                return self::caching($hash, true);
            }
        }
        return self::caching($hash, false);
    }

    /**
     *  check if user has a specific role on a asset object
     *  
     * @param  mixed  $role 
     * @param  mixed  $assetInstance
     * @param  mixed  $user_id 
     * @return bool
     */
    public static function hasRole($role, $assetInstance = null, $user_id = null)
    {
        //cache the result for better performance
        $hash = HALOUtilHelper::getHashArray(array($role, is_null($assetInstance) ? $assetInstance : get_class($assetInstance) . '@' . $assetInstance->id, $user_id));

        if (isset(self::$cache[$hash])) {
            return self::$cache[$hash];
        }

        self::init();
        if (is_string($role)) {
            //user role string as input
            //search roles to match for $roleName
            $roleName = $role;
            foreach (self::$roles as $r) {
                if (strtolower($r->name) == strtolower($roleName)) {
                    $role = $r;
                }
            }
        }
        if (!is_object($role)) {
            return self::caching($hash, false);
        }
        //only accept configured roles
        //trigger before checking role
        if (Event::fire('auth.onBeforeCheckingRole', array($role, $assetInstance, $user_id), true) === false) {
            //error occur, return false
            return self::caching($hash, false);
        }

        switch ($role->type) {
            case 'system':
                //trigger system handler
            $handler = 'HALOAuthSystemHandler';
                $method = 'hasSystemRole';
                $args[] = $user_id;

                $args[] = $role->name;

                break;
            case 'context':
                //trigger context dependency handler
            if (!$assetInstance) {
                    return self::caching($hash, false);
                }

                $handler = 'HALOAuth' . ucfirst($assetInstance->getContext()) . 'Handler';
                $method = 'is' . ucfirst($role->name);
                $args[] = $user_id;

                $args[] = $assetInstance;

                break;
            case 'group':
                //trigger group handler, it turns into system handler with the method name is 'isInGroup'
            $handler = 'HALOAuthSystemHandler';
                $method = 'isInGroup';

                $args[] = $user_id;

                $args[] = $role->getHALOParams();

                break;
            case 'dynamic':
                //trigger dynamic handler, it turns into system handler with the method name is 'isInDynamic'
            $handler = 'HALOAuthSystemHandler';
                $method = 'isInDynamic';

                $args[] = $user_id;

                $args[] = $assetInstance;

                $args[] = $role;

                break;

            default:
                //safe protection, just return false for unknown role type
            return self::caching($hash, false);
        }
        //check if handler function exists
        if (class_exists($handler)) {
            $obj = new $handler();
            if (method_exists($obj, $method)) {
                //call handler function with prepared parameters
                $rtn = call_user_func_array(array($obj, $method), $args);

                if ($rtn) {
                    return self::caching($hash, $rtn);
                }
            }
        }

        return self::caching($hash, false);
    }
    /**
     * 
     * @param  mixed $hash
     * @param  mixed $value
     * @return mixed
     */
    protected static function caching($hash, $value)
    {
        return (self::$cache[$hash] = $value);
    }

    /**
     * function to handle invalid token request
     * 
     * @return string
     */
    public static function responseInvalidToken()
    {
        //check if login remember option is checked
        if (JAXResponse::isAjaxRequest()) {
            //refresh the page
            //HALOResponse::addAlert(__halotext('Session expired! Click Ok to reload your browser'));
            HALOResponse::refresh();
            return HALOResponse::sendResponse();
        } else {
            //refresh the page with invalid token message
            return Redirect::intended('?view=user&task=login')
                ->with('error', __halotext('Session expired!'));
        }

    }

    /**
     * flush cache
     * 
     * @return array
     */
    public static function flushCache()
    {
        self::$cache = array();
    }

    /**
     * get role options
     * 
     * @return array
     */
    public static function getRoleOptions($valueKey = 'id', $systemOnly = false)
    {
        static::init();
		if($systemOnly) {
			$roles = HALORoleModel::where('type', 'system')->get();
		} else {
			$roles = static::$roles;
		}
		$options = HALOUtilHelper::collection2options($roles, $valueKey, 'getDisplayName', false);
        return $options;
    }
    /**
     * get permission options
     * 
     * @return array
     */
    public static function getPermissionOptions()
    {
        static::init();
        $options = array();
        $permissions = HALOPermissionModel::get();
        foreach ($permissions as $permission) {
            $options[] = array('title' => $permission->name . ' : ' . $permission->description, 'value' => $permission->name);
        }
        return $options;
    }

    /**
     * get all role
     * 
     * @return array
     */
    public static function getRoles()
    {
        static::init();
        return static::$roles;
    }

    /**
     * get all permission
     * 
     * @return array
     */
    public static function getPermissions()
    {
        static::init();
        return static::$permissions;
    }

    /**
     * return user by permission name
     * 
     * @param  mixed $permissionName
     * @return array
     */
    public static function getUsersByPermissionName($permissionName)
    {
        $data = new stdClass();
        Event::fire('system.getUsersByPermissionName', array($permissionName, &$data));
        return isset($data->users) ? $data->users->lists('id') : array();
    }

    /*
		init authentication cache
     */
	public static function initCache() {
		static $inited = null;
		if(is_null($inited)) {
			$features = HALOUtilHelper::___getFeatures();	//Todo: get from database or EDD
			foreach($features as $fe => $val) {
				$hash = HALOUtilHelper::getHashArray(array('fea' . 'ture' . '.' . $fe, null, null));
				self::caching($hash, $val);
			}
			$can = HALOAuthSystemHandler::canGo();
			self::$sysPer = 'h' . 'a' . 'l' . 'o'  . '.' . 'g' . 'o';
			self::caching(self::$sysPer, $can);
			$inited = true;
		}
	}

    /**
     * return roles of a given user
     * 
     * @param  mixed $user
     * @return array
     */
    public static function getUserRoles($user)
    {
        $data = new stdClass();
        Event::fire('system.getRolesByUser', array($user, &$data));
        return isset($data->roles) ? $data->roles : array();
    }

    /**
     * return users of a given role name
     * 
     * @param  string $roleName
     * @return array
     */
    public static function getUsersByRole($roleName)
    {
        $data = new stdClass();
        //find role by rolename
        $roles = self::getRoles();
        $role = null;
        foreach ($roles as $r) {
            if (strtoupper($r->name) == strtoupper($roleName)) {
                $role = $r;
            }
        }
        if ($role) {
            Event::fire('system.getUsersByRole', array($role, &$data));
            return isset($data->users) ? $data->users : array();
        } else {
            return array();
        }
    }
}
