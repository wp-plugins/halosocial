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

class HALOLogModel extends HALOModel
{
    public $timestamps = true;

    protected $table = 'halo_logs';

    protected static $_counters = array();
    //////////////////////////////////// Define Relationships /////////////////////////
    /*
    
     */
    /**
     * HALOUserModel, HALOLogModel: belong to (owner)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('HALOUserModel', 'user_id');
    }

    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relation\morphTo
     */
    public function arg0()
    {
        return $this->morphTo();
    }
    
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relation\morphTo
     */
    public function arg1()
    {
        return $this->morphTo();
    }

    /**
     * 
     * @return Illuminate\Database\Eloquent\Relation\morphTo
     */
    public function arg2()
    {
        return $this->morphTo();
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Add a change log ti Database
     * 
     * @param string $actionName 
     * @param array $data    
     * @return HALOLogModel 
     */
    public static function addLog($actionName, $data)
    {
        if (!HALOConfig::get('actlog.enable', 0)) {
            return false;
        }
        //validate input data
        $parts = explode('.', $actionName);
        if (count($parts) < 2) {
            return false;
        }

        //get log action id
        $actionId = self::registerAction($actionName);
        $log = new HALOLogModel();
        $my = HALOUserModel::getUser();
        $user_id = $my ? $my->id : 0;

        $log->user_id = $user_id;
        $log->action_id = $actionId;
        $log->setParams('client_ip', Request::getClientIp());

        //setup log content
        $morphIdx = 0;
        foreach ($data as $idx => $arg) {
            if (is_a($arg, 'HALOModel') || is_a($arg, 'HALONestedModel')) {
                $morphType = 'arg' . $morphIdx . '_type';
                $morphId = 'arg' . $morphIdx . '_id';
                $log->$morphType = get_class($arg);
                $log->$morphId = $arg->id;
                $morphIdx++;
            }
            if (is_object($arg) && method_exists($arg, 'toArray')) {
                $data[$idx] = $arg->toArray();
            }
        }
        $log->content = json_encode($data);
        $log->save();
        return $log;
    }

    /**
     * Return auto allocated action id from an action name
     * 
     * @param  string $actionName 
     * @return int            
     */
    public static function registerAction($actionName)
    {
        $actionId = Cache::rememberForever('log.actionid.' . $actionName, function () use ($actionName) {
            $action = DB::table('halo_log_actions')->where('action_name', $actionName)->first();
            if ($action) {
                return $action->id;
            } else {
                $id = DB::table('halo_log_actions')->insertGetId(
                    array('action_name' => $actionName)
                );
                return $id;
            }
        });
        return $actionId;
    }

    /**
     * Return action name of this log
     * 
     * @return string
     */
    public function getActionName()
    {
        $actionId = $this->action_id;
        $actionName = Cache::rememberForever('log.actionname.' . $actionId, function () use ($actionId) {
            $action = DB::table('halo_log_actions')->where('id', $actionId)->first();
            if ($action) {
                return $action->action_name;
            } else {
                return null;
            }
        });
        return $actionName;
    }
}
