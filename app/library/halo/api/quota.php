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

class HALOQuotaAPI
{

    /**
     * check if the current login user exceeded the number records quota on the target model
     * 
     * @param  array  $quotaKey 
     * @param  object target model  $target 
     * @param  int  $ownerKey 
     * @param  Closure  $condCb
     * @param  bool$silent
     * @return bool
     */
    public static function exceed($quotaKey, $target, $ownerKey, Closure $condCb = null, $silent = false)
    {
        //save new record to notification table
        $my = HALOUserModel::getUser();

        if (!$my) {
            if (!$silent) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
            }

            return true;

        }
        //get all 5 quota levels settings for this quotaKey
        $settings = self::getQuotaKeySetting($quotaKey, $my);

        if (empty($settings)) {
            // no quota settings for this quotaKey, so just return passed
            return false;
        }

        //there are 5 quota levels:
        $now = Carbon::now();
        //1. per minute
        if (isset($settings['i']) && $settings['i'] && is_numeric($settings['i'])) {
            $query = $target->where($ownerKey, '=', $my->id)
                ->where('created_at', '>=', $now->copy()->subMinutes(1));
            if (!is_null($condCb)) {
                call_user_func($condCb, $query);
            }

            $count = $query->count();
            if ($count >= $settings['i']) {
                if (!$silent) {
                    HALOResponse::addMessage(HALOError::failed(sprintf(__halotext('Quota exceeded: only %s %s per minute'), $settings['i'], lcfirst($settings['l']))));
                }

                return true;
            }
        }

        //2. per day
        if (isset($settings['d']) && $settings['d'] && is_numeric($settings['d'])) {
            $query = $target->where($ownerKey, '=', $my->id)
                ->where('created_at', '>=', $now->copy()->subDays(1));
            if (!is_null($condCb)) {
                call_user_func($condCb, $query);
            }

            $count = $query->count();
            if ($count >= $settings['d']) {
                if (!$silent) {
                    HALOResponse::addMessage(HALOError::failed(sprintf(__halotext('Quota exceeded: only %s %s per day'), $settings['d'], lcfirst($settings['l']))));
                }

                return true;
            }
        }

        //3. per month
        if (isset($settings['m']) && $settings['m'] && is_numeric($settings['m'])) {
            $query = $target->where($ownerKey, '=', $my->id)
                ->where('created_at', '>=', $now->copy()->subMonths(1));
            if (!is_null($condCb)) {
                call_user_func($condCb, $query);
            }

            $count = $query->count();
            if ($count >= $settings['m']) {
                if (!$silent) {
                    HALOResponse::addMessage(HALOError::failed(sprintf(__halotext('Quota exceeded: only %s %s per month'), $settings['m'], lcfirst($settings['l']))));
                }

                return true;
            }
        }

        //4. limit
        if (isset($settings['g']) && $settings['g'] && is_numeric($settings['i'])) {
            if (!is_null($condCb)) {
                call_user_func($condCb, $target);
            }

            $count = $target->count();
            if ($count >= $settings['g']) {
                if (!$silent) {
                    HALOResponse::addMessage(HALOError::failed(sprintf(__halotext('Limit Reached: only %s %s allowed'), $settings['g'], lcfirst($settings['l']))));
                }

                return true;
            }
        }

        //5. conditional

        return false;
    }

    /**
     * function to load quota setting of a user
     * 
     * @param  HALOUserModel object $user default = null --> load default quota settings
     * @return array($setting,$description);
     */
    public static function loadQuotaSettings(HALOUserModel $user = null)
    {
        static $defaultSettings = null;
        if (is_null($defaultSettings)) {
            //load default settings
            $defaultSettings = new HALOObject();
            Event::fire('quota.onLoadingSettings', array(&$defaultSettings));
        }

        static $globalSettings = null;
        if (is_null($globalSettings)) {
            //load global settings
            $globalSettings = HALOConfig::get('quota.default', new HALOObject());
        }

        //load user settings
        $userSettings = new HALOObject();
        if (!is_null($user)) {
            $userSettings = new HALOObject();
            Event::fire('quota.onLoadingUserSettings', array(&$userSettings, $user));
        }
        //merge settings
        $default = json_decode(json_encode($defaultSettings), true);
        $global = json_decode(json_encode($globalSettings), true);
        $userSet = json_decode(json_encode($userSettings), true);

        $settings = HALOUtilHelper::array_override_recursive($default, $global, $userSet);
        return $settings;
    }

    /**
     * return quota key setting of a specific user
     * 
     * @param  array $quotaKey 
     * @param  HALOUserModel object $user
     * @return array
     */
    public static function getQuotaKeySetting($quotaKey, $user)
    {

        $settings = self::loadQuotaSettings($user);
        $rtn = array();
        //quotaKey is in namespace format
        $parts = explode('.', $quotaKey);
        $rtn = $settings;
        foreach ($parts as $name) {
            if (isset($rtn[$name])) {

                $rtn = $rtn[$name];
            } else {
                $rtn = array();
            }
        }
        return $rtn;
    }

    /**
     *return minute per a quota unit setting.
     *formular: 24 * 60 / {quota per day}
     *
     * @param  array $quotaKey 
     * @param  HALOUserModel object $user
     * @return int 
    */
    public static function getMinutePerQuotaUnit($quotaKey, $user)
    {
        $minute = 0;
        $settings = self::getQuotaKeySetting($quotaKey, $user);
        if (isset($settings['d']) && $settings['d']) {
            $minute = floor((24 * 60) / (int) $settings['d']);
        }
        return $minute;
    }

    /**
     * return waiting second for next accept quota
     * 
     * @param  array $quotaKey
     * @param  object $target 
     * @param  int  $ownerKey
     * @return int
     */
    public static function getWaitingTime($quotaKey, $target, $ownerKey)
    {
        $my = HALOUserModel::getUser();
        $diffInSeconds = 0;
        $minute = self::getMinutePerQuotaUnit($quotaKey, $my);
        $lastInsert = $target->where($ownerKey, '=', $my->id)
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();
        if ($lastInsert) {
            $diffInSeconds = Carbon::now()->diffInSeconds($lastInsert->created_at->addMinutes($minute), false);
        }
        if ($diffInSeconds < 0) {
            return 0;
        }
        return $diffInSeconds;
    }
}
