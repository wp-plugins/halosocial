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
 
class HALOArchive
{
    /**
     * @param    string    The name of the archive file
     * @param    string    Directory to unpack into
     * @return    bool    True for success
     */
    public static function extract($archivename, $extractdir)
    {
        $untar = false;
        $result = false;
        $ext = pathinfo(strtolower($archivename), PATHINFO_EXTENSION);
        // check if a tar is embedded...gzip/bzip2 can just be plain files!
        if ($ext == 'tar') {
            $untar = true;
        }

        switch ($ext) {
            case 'zip':
                $adapter = &HALOArchive::getAdapter('zip');
                if ($adapter) {
                    $result = $adapter->extract($archivename, $extractdir);
                }
                break;
            case 'tar':
                $adapter = &HALOArchive::getAdapter('tar');
                if ($adapter) {
                    $result = $adapter->extract($archivename, $extractdir);
                }
                break;
            /*
            case 'tgz'  :
            $untar = true;    // This format is a tarball gzip'd
            case 'gz'   :    // This may just be an individual file (e.g. sql script)
            case 'gzip' :
            $adapter =& HALOArchive::getAdapter('gzip');
            if ($adapter)
            {
            $config =& JFactory::getConfig();
            $tmpfname = $config->getValue('config.tmp_path').DS.uniqid('gzip');
            $gzresult = $adapter->extract($archivename, $tmpfname);
            if (JError::isError($gzresult))
            {
            @unlink($tmpfname);
            return false;
            }
            if($untar)
            {
            // Try to untar the file
            $tadapter =& HALOArchive::getAdapter('tar');
            if ($tadapter) {
            $result = $tadapter->extract($tmpfname, $extractdir);
            }
            }
            else
            {
            $path = JPath::clean($extractdir);
            JFolder::create($path);
            $result = JFile::copy($tmpfname,$path.DS.JFile::stripExt(JFile::getName(strtolower($archivename))));
            }
            @unlink($tmpfname);
            }
            break;
            case 'tbz2' :
            $untar = true; // This format is a tarball bzip2'd
            case 'bz2'  :    // This may just be an individual file (e.g. sql script)
            case 'bzip2':
            $adapter =& HALOArchive::getAdapter('bzip2');
            if ($adapter)
            {
            $config =& JFactory::getConfig();
            $tmpfname = $config->getValue('config.tmp_path').DS.uniqid('bzip2');
            $bzresult = $adapter->extract($archivename, $tmpfname);
            if (JError::isError($bzresult))
            {
            @unlink($tmpfname);
            return false;
            }
            if ($untar)
            {
            // Try to untar the file
            $tadapter =& HALOArchive::getAdapter('tar');
            if ($tadapter) {
            $result = $tadapter->extract($tmpfname, $extractdir);
            }
            }
            else
            {
            $path = JPath::clean($extractdir);
            JFolder::create($path);
            $result = JFile::copy($tmpfname,$path.DS.JFile::stripExt(JFile::getName(strtolower($archivename))));
            }
            @unlink($tmpfname);
            }
            break;
             */
            default:
                return false;
                break;
        }

        if (!$result) {
            return false;
        }
        return true;
    }
    /**
     * get Adapter
     * 
     * @param  string $type 
     * @return object
     */
    public static function &getAdapter($type)
    {
        static $adapters;

        if (!isset($adapters)) {
            $adapters = array();
        }

        if (!isset($adapters[$type])) {
            // Try to load the adapter object
            $class = 'HALOArchive' . ucfirst($type);

            if (!class_exists($class)) {
                return false;
            }

            $adapters[$type] = new $class();
        }
        return $adapters[$type];
    }

}
