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

class HALOSubscriber
{
    static $loaded = array();
    
    /**
     * Load a list of subscribers to chain
     * 
     * @param  array $subscribers 
     */
    public static function load($subscribers = null)
    {
        if (is_null($subscribers)) {
            //load all
        } else {
            $subscribers = (array) $subscribers;
        }
        foreach ($subscribers as $sub) {
            if (!isset(self::$loaded[$sub])) {
                $subName = ucfirst($sub) . 'EventHandler';
                if (class_exists($subName)) {
                    $subscriber = new $subName();
                    Event::subscribe($subscriber);
                    self::$loaded[$sub] = true;
                }
            }
        }
    }

    /**
     * Load default subscribers
     */
    public static function loadDefault()
    {
        //load core subscribers
        HALOSubscriber::load(array('system'));
        HALOSubscriber::load(array('stream', 'user', 'photo', 'pushServer', 'notification',
            'activity', 'userpoint', 'role', 'log', 'ban', 'filter'));

        //load query log subscriber
        //HALOSubscriber::load(array('queryLog'));

    }
}
