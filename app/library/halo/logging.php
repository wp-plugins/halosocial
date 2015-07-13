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
 
class HALOLogging
{
    protected static $stack = array();

    /**
     * start timer log
     * 
     * @param  string $name
     * @param  string $msg
     * @return mixed
     */
    public static function start($name, $msg = '')
    {
        if (HALOConfig::get('global.enableDebug')) {
            $hash = md5($name);
            if (!isset(self::$stack[$hash])) {
                self::$stack[$hash] = array();
            }
            array_push(self::$stack[$hash], microtime(true));
            self::info("(start) $name: $msg");
        }
    }

    /**
     * stop timer log
     * 
     * @param  string $name
     * @param  string $msg
     * @return mixed
     */
    public static function stop($name, $msg = '')
    {
        if (HALOConfig::get('global.enableDebug')) {
            $hash = md5($name);
            if (isset(self::$stack[$hash])) {
                $start = array_pop(self::$stack[$hash]);
                $stop = microtime(true);
                self::info("(stop) $name: (duration: " . round(($stop - $start) * 1000) . ") $msg");
            }
        }
    }
    /**
     * 
     * @param  string $msg
     * @return string
     */
    public static function info($msg = '')
    {
        Log::info("$msg");
    }
}
