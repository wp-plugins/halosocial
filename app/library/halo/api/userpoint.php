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
 
class HALOUserpointAPI
{

    /**
     * @api: update userpoint
     * 
     * @param  string $namespace
     * @param  HALOUserModel object $user
     * @return bool
     */
    public static function update($namespace, HALOUserModel $user = null)
    {
        if (is_null($user) && is_null($user = HALOUserModel::getUser())) {
            return false;
        }

        //load settings
        $settingObj = HALOObject::getInstance(self::loadUserpointSettings());
        if ($user && $settingObj->getNsValue($namespace . '.s', 0)) {
            //check if userpoint rule is enabled
            $point = $settingObj->getNsValue($namespace . '.p', 0);
            $user->point_count = $point+(int) $user->point_count;
            $user->save();
        }
        return true;
    }

    /**
     * function to load userpoint setting
     * 
     * @return array $settings
     */
    public static function loadUserpointSettings()
    {
        static $defaultSettings = null;
        if (is_null($defaultSettings)) {
            //load default settings
            $defaultSettings = new HALOObject();
            Event::fire('userpoint.onLoadingSettings', array(&$defaultSettings));
        }
        static $globalSettings = null;
        if (is_null($globalSettings)) {
            //load global settings
            $globalSettings = HALOConfig::get('userpoint.rules', new HALOObject());
        }

        //merge settings
        $default = json_decode(json_encode($defaultSettings), true);
        $global = json_decode(json_encode($globalSettings), true);

        $settings = HALOUtilHelper::array_override_recursive($default, $global);
        return $settings;
    }

    /**
     * function to return userpoint karma
     * 
     * @param  string $point
     * @return HALOUIBuilder object $karma
     */
    public static function getUserpointKarma($point)
    {
        $levels = array();
        for ($i = 1; $i <= 5; $i++) {
            $levels[] = HALOConfig::get('userpoint.level.' . $i, 0);
        }
        $level = HALOUtilHelper::array_slot($levels, $point);

        $karma = HALOUIBuilder::getInstance('', 'karam', array('value' => $point, 'level' => $level))->fetch();
        return $karma;
    }
}
