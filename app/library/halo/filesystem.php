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
 
use Illuminate\Filesystem\Filesystem;

class HALOFilesystem extends Filesystem
{

    /**
     * Create a folder -- and all necessary parent folders.
     *
     * @param string A path to create from the base path.
     * @param int Directory permissions to set for folders created.
     * @return bool True if successful.
     */
    public function createDir($path = '', $mode = 0755)
    {
        // Initialize variables
        static $nested = 0;

        // Check if parent dir exists
        $parent = dirname($path);
        if (!$this->exists($parent)) {
            // Prevent infinite loops!
            $nested++;
            if (($nested > 20) || ($parent == $path)) {
                $nested--;
                return false;
            }

            // Create the parent directory
            if ($this->createDir($parent, $mode) !== true) {
                $nested--;
                return false;
            }
            // OK, parent directory has been created
            $nested--;
        }

        // Check if dir already exists
        if ($this->exists($path)) {
            return true;
        }
        //create the dir and empty index.php file for security

        // We need to get and explode the open_basedir paths
        $obd = ini_get('open_basedir');

        // If open_basedir is set we need to get the open_basedir that the path is in
        if ($obd != null) {
            if (HALO_PATH_ISWIN) {
                $obdSeparator = ";";
            } else {
                $obdSeparator = ":";
            }
            // Create the array of open_basedir paths
            $obdArray = explode($obdSeparator, $obd);
            $inBaseDir = false;
            // Iterate through open_basedir paths looking for a match
            foreach ($obdArray as $test) {
                $test = $this->cleanPath($test);
                if (strpos($path, $test) === 0) {
                    $obdpath = $test;
                    $inBaseDir = true;
                    break;
                }
            }
            if ($inBaseDir == false) {
                // Return false for JFolder::create because the path to be created is not in open_basedir
                return false;
            }
        }

        // First set umask
        $origmask = @umask(0);

        // Create the path
        if (!$ret = @mkdir($path, $mode)) {
            @umask($origmask);
            return false;
        }

        // Reset umask
        @umask($origmask);

        return $ret;
    }

    /**
     * write File
     * 
     * @param  string $path  
     * @param  string $contents
     * 
     */
    public function writeFile($path, $contents)
    {
        //create destination dir if the dir is exists
        $path_parts = pathinfo($path);
        if (!File::isDirectory($path_parts['dirname'])) {
            File::createDir($path_parts['dirname']);
        }
        return File::put($path, $contents);
    }

    /**
     * Function to copy files from src folder to dest folder
     *
     * @param    string    $src    Source folder
     * @param    string    $dst    Destination folder
     * @return    string    bool passed/failed
     */
    public function recurse_copy($src, $dst)
    {

        $dir = opendir($src);

        if (!File::createDir($dst)) {
            return false;
        }
        while (false !== ($file = readdir($dir))) {

            if (($file != '.') && ($file != '..')) {

                if (is_dir($src . '/' . $file)) {

                    File::recurse_copy($src . '/' . $file, $dst . '/' . $file);

                } else {

                    copy($src . '/' . $file, $dst . '/' . $file);

                }

            }

        }

        closedir($dir);

        return true;
    }

    /**
     * Function to strip additional / or \ in a path name
     *
     * @param    string    $path    The path to clean
     * @param    string    $ds        Directory separator (optional)
     * @return    string    The cleaned path
     */
    public function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = trim($path);

        if (empty($path)) {
            $path = HALO_ROOT_PATH;
        } else {
            // Remove double slashes and backslahses and convert all slashes and backslashes to DS
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    /**
     * Function to convert local file path to url path
     *
     * @param    string    $path    The path to clean
     * @param    string    $ds        Directory separator (optional)
     * @return    string    The cleaned path
     */
    public function toUrl($path)
    {
        $path = self::cleanPath($path);
        $rootPath = rtrim(self::cleanPath(HALO_ROOT_PATH), DIRECTORY_SEPARATOR);

        if (strpos($path, $rootPath) === 0) {
            //replace root path with root url
            $relPath = substr($path, strlen($rootPath));
            $relPath = self::cleanPath($relPath, '/');
            return HALO_ROOT_URL . $relPath;

        }
        return '';
    }

    /**
     * Function to convert url path to local file path
     *
     * @param    string    $url    The url
     * @param    string    $prefix
     * @return    string    the local directory path
     */
    public function fromUrl($url, $prefix = '')
    {
        if (strpos($url, HALO_ROOT_URL) === 0) {
            //replace root path with root url
            $count = 1;
            $path = str_replace(HALO_ROOT_URL, HALO_ROOT_PATH, $url, $count);
            return self::cleanPath($path);

        }
        return '';
    }

}
