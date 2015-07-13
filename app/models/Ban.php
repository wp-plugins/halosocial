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

class HALOBanModel extends HALOModel
{
    public $timestamps = true;

    protected $table = 'halo_ban_actions';

    protected static $_counters = array();
    //////////////////////////////////// Define Relationships /////////////////////////
    
    /**
     * HALOUserModel, HALOLogModel: belong to (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('HALOUserModel', 'user_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /*
   
     */
    /**
     *  Add ban action record to a specific user
     * 
     * @param  int $user_id 
     * @param  array $data    
     * @return bool    
     */
    public static function banActions($user_id, $data)
    {
        if (empty($data) || !isset($data['ban_actions']) || !is_array($data['ban_actions']) || !isset($data['ban_duration'])) {
            return false;
        }
        if (empty($data['reason'])) {
            $error = HALOError::getMessageBag()->add('reason', __halotext('Ban reason must be defined'));
            HALOResponse::addMessage($error);
            return false;
        }
        //check user id
        if (!$user_id || !($user = HALOUserModel::getUser($user_id))) {
            return false;
        }
        //check permission
        $my = HALOUserModel::getUser();
        if (!HALOBanModel::canBan($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
            return false;
        }
        $settings = self::getSettings($user);
        $now = Carbon::now();
        $banActions = array();
        foreach ($settings as $action => $setting) {
            if (isset($setting['value'])) {
                $banAction = $setting['value'];
            } else {
                $banAction = new HALOBanModel();
            }
            $index = array_search($action, $data['ban_actions']);
            if ($index === false) {
                //disable ban action
                if ($banAction->id) {
                    $banAction->delete();
                }
            } else {
                if (is_numeric($data['ban_duration'][$index]) && ($duration = intval($data['ban_duration'][$index])) > 0) {
                    $banAction->actor_id = $my->id;
                    $banAction->target_id = $user_id;
                    $banAction->action = $action;
                    $banAction->target_id = $user_id;
                    $banAction->expired_at = $now->addHours($duration);
                    $banAction->setParams('reason', $data['reason']);
                    $banAction->save();
                    $banActions[] = $banAction;
                }
            }
        }
        Event::fire('ban.onSubmit', array($banActions));
        return true;
    }

    /**
     * Load ban action setting
     * 
     * @param  object $user
     * @return array  
     */
    public static function getSettings($user = null)
    {
        static $__settings = array();
        static $__default = null;
        if (is_null($user) && !is_null($__default)) {
            return $__default;
        }

        if ($user && !empty($settings[$user->id])) {
            return $settings[$user->id];
        }

        $settings = Cache::rememberForever('banActionSettings', function () {
            $rtn = array();
            for ($i = 1; $i <= 5; $i++) {
                $level = array();
                $level['description'] = HALOConfig::get('ban.level' . $i . '.description');
                $level['permissions'] = HALOConfig::get('ban.level' . $i . '.permissions', array());
                $level['roles'] = HALOConfig::get('ban.level' . $i . '.roles', array());
                if (!empty($level['description']) && (!empty($level['permissions']) || !empty($level['roles']))) {
                    $rtn['level' . $i] = $level;
                }
            }
            return $rtn;
        });
        //setup dynamic relationship
        HALOBanModel::setupUserRelationship();

        if ($user) {
            $now = Carbon::now();
            $actions = $user->banActions()->where('expired_at', '>', $now)->get();
            foreach ($actions as $action) {
                $action->expired_at = new Carbon($action->expired_at);
                if (isset($settings[$action->action])) {
                    $settings[$action->action]['value'] = $action;
                }
            }
            $__setings[$user->id] = $settings;
        } else {
            $__default = $settings;
        }
        return $settings;
    }

    /**
     * Get load ban action setting
     * 
     * @param  object $user 
     * @return object 
     */
    public static function getUserBanActions($user)
    {
        static $banActions = null;
        if (!is_null($banActions)) {
            return $banActions;
        }

        $settings = self::getSettings();
        $banActions = array();
        foreach ($settings as $idx => $setting) {
            if (isset($setting['value'])) {
                $banActions[$idx] = $setting;
            }
        }
        return $banActions;
    }

    /**
     * Get help text for ban action
     * 
     * @param  string $action 
     * @return string         
     */
    public static function getHelpText($action)
    {
        $helpText = '';
        if (isset($action['permissions'])) {
            $permissions = HALOPermissionModel::whereIn('name', $action['permissions'])->lists('description');
            if (!empty($permissions)) {
                $helpText .= __halotext('This user will not be able to') . ":\n - " . implode("\n - ", $permissions);
            }
        }
        if (isset($action['roles'])) {
            $roles = HALOPermissionModel::whereIn('name', $action['roles'])->lists('description');
            if (!empty($roles)) {
                $helpText .= __halotext('This user will not have role(s)') . ":\n - " . implode("\n - ", $roles);
            }
        }
        return $helpText;
    }

    /**
     * Funciton to set relationship for user model
     * 
     * @return bool
     */
    public static function setupUserRelationship()
    {
        //only run one time
        static $ran = null;
        if (is_null($ran)) {
            HALOUserModel::setResourceCb('banActions', function ($user) {
                $query = $user->hasMany('HALOBanModel', 'target_id');
                return $query;
            });
            $ran = true;
        }
    }

    /**
     * Get description text for this ban action
     * 
     * @return string
     */
    public function getDescription()
    {
        $settings = HALOBanModel::getSettings();
        if (isset($settings[$this->action]) && isset($settings[$this->action]['description'])) {
            return $settings[$this->action]['description'];
        }
        return __halotext('unknown');
    }

    /**
     * Get duration in(s) for this ban acrtion
     * 
     * @return object
     */
    public function getDuration()
    {
        if (is_string($this->expired_at)) {
            $this->expired_at = new Carbon($this->expired_at);
        }
        return $this->expired_at->diffInSeconds(Carbon::now());
    }

    /**
     * Check if current user can do ban action
     * 
     * @param  object $user
     * @return bool
     */
    public static function canBan($user)
    {
        static $can = null;
        if (!is_null($can)) {
            return $can;
        }

        $my = HALOUserModel::getUser();
        if (!$my) {
            return ($can = false);
        }
		
		//@rule: can not ban admin/mod
		if($user && isset($user->id) && (HALOAuth::hasRole('Admin', null, $user->id) || HALOAuth::hasRole('Mod', null, $user->id))) {
			return ($can = false);
		}
		
        $banRoles = HALOConfig::get('ban.roles', array());
        foreach ($banRoles as $roleName) {
            if (HALOAuth::hasRole($roleName)) {
                return ($can = true);
            }
        }
        return ($can = false);
    }

}
