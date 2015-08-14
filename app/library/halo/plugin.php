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
 
class HALOPlugin
{
    public static $inited = false;

    /**
     * perform plugin installation
     * 
     * @param  [type] $pkg_file
     */
    public static function install($pkg_file)
    {
        $pkg_parts = pathinfo(strtolower($pkg_file));
        $pkg_dir = $pkg_parts['dirname'] . '/' . $pkg_parts['filename'];

        //uncompress the package
        HALOArchive::extract($pkg_file, $pkg_dir);

        $plg = HALOPluginModel::getInstance(0, $pkg_dir);
        if ($plg) {
            //start the installation process
            $rtn = $plg->install();
			if(!$rtn->any()){
				HALOResponse::setData('plg', $plg);
			}
        } else {
            return HALOError::failed('Wrong plugin meta file format');
        }
        //cleanup tmp file
        self::cleanupInstallation($pkg_file);

        return $rtn;
    }

    /**
     * perform clean up after installation
     * 
     * @param  string $pkg_file
     */
    public static function cleanupInstallation($pkg_file)
    {
        $pkg_parts = pathinfo(strtolower($pkg_file));
        $pkg_dir = $pkg_parts['dirname'] . '/' . $pkg_parts['filename'];
        //clean archive folder
        if (File::exists($pkg_dir)) {
            File::deleteDirectory($pkg_dir);
        }
        //clean pkg file
        if (File::exists($pkg_file)) {
            File::delete($pkg_file);
        }
    }

    /**
     * load active plugins to subscriber chain
     */
    public static function loadPlugins()
    {
        if (!self::$inited) {
            HALOSubscriber::loadDefault();
            self::$inited = true;
        }
        $plugins = HALOPluginModel::getActivePlugins();
        foreach ($plugins as $plugin) {
			// var_dump($plugin->name . "\n");
            $subscriber = ucfirst($plugin->folder) . ucfirst($plugin->element);
			//include the plugin autoload file
			$includeFile = HALO_APP_PATH . '/plugins/' . $plugin->folder . '/' . $plugin->element . '/vendor/autoload.php';
			if(file_exists($includeFile)) {
				require_once($includeFile);
			}
            HALOSubscriber::load($subscriber);
        }
		// exit;
    }

}
