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
 
class HALOPoolManager
{

    protected static $__modelPool = array();
    protected static $__modelPending = array();

    /**
     * init Model Pool
     * 
     * @param  string $modelName
     * @return array
     */
    public static function initModelPool($modelName)
    {
        if (!isset(self::$__modelPool[$modelName])) {
            self::$__modelPool[$modelName] = array();
            self::$__modelPending[$modelName] = array();
        }
    }
    /**
     * sync Model Pool description]
     * 
     * @param  string $modelName
     * @return array
     */
    public static function syncModelPool($modelName)
    {
        self::initModelPool($modelName);
        if (!empty(self::$__modelPending[$modelName])) {
            if (class_exists($modelName)) {
                //special treatment for HALOUserModel
                if ($modelName == 'HALOUserModel') {
                    $collection = HALOUserModel::init(self::$__modelPending[$modelName]);
                } else {
                    $query = call_user_func_array(array($modelName, 'whereIn'), array('id', self::$__modelPending[$modelName]));
                    $collection = $query->get();
                }
                if (!empty($collection)) {
                    foreach ($collection as $item) {
                        self::$__modelPool[$modelName][$item->id] = $item;
                    }
                }
            }
            //reset pending list
            self::$__modelPending[$modelName] = array();
        }
    }

    /**
     * function to load model relation from pool
     * 
     * @param  string $modelName
     * @param  Illuminate\Database\Eloquent\Collection $collection
     * @param  array  $relationArr 
     * @return array
     */
    public static function loadModel($modelName, &$collection, array $relationArr)
    {
        //check for valid input parameter
        if (!is_a($collection, 'Illuminate\Database\Eloquent\Collection')) {
            if (is_a($collection, 'Illuminate\Database\Eloquent\Model')) {
                //for single model, convert it to array
                $collection = array($collection);
            } else {
                //invalid input, just skip it
                return;
            }
        }
        //init model pool
        self::initModelPool($modelName);

        $relations = array();
        foreach ($relationArr as $relationStr => $key) {
            $relationStr = trim($relationStr);
            if (!empty($relationStr)) {
                $relationLevels = explode('.', $relationStr);
                $relations[] = array('relationLevels' => $relationLevels, 'key' => $key);
            }
        }

        foreach ($relations as $relation) {
            foreach ($collection as &$item) {
                //foreach item, access to the leaf relation and check for existing
                $obj = &$item;
                $levels = $relation['relationLevels'];
                $key = $relation['key'];
                $level = $levels[0];
                if ($level) {
                    //if the leaf relation and is a HALOUserModel instance
                    if (!isset($levels[1])) {
                        if (isset($obj->$key) && $obj->$key) {
                            //if the model key doesn't exist in pool, jus init it
                            if (!isset(self::$__modelPool[$modelName][$obj->$key])) {
                                self::$__modelPool[$modelName][$obj->$key] = null;
                            }
                            //set reference to the pool
                            //$obj->$level = & self::$__modelPool[$modelName][$obj->$key];
                            $obj->setRelationRef($level, self::$__modelPool[$modelName][$obj->$key]);
                            //add key to the pending load pool
                            self::$__modelPending[$modelName][$obj->$key] = $obj->$key;
                        } else {
                            //relation key does not exists just set to null
                            $nullRelation = null;
                            $obj->setRelationRef($level, $nullRelation);
                        }
                    } else {
                        $obj = &$obj->$level;
                        //continue access to the leaf relation
                        $subRelation = implode('.', array_slice($levels, 1));
                        self::loadModel($modelName, $obj, array($subRelation => $key));
                    }
                }
            }
        }
        self::syncModelPool($modelName);
    }
}
