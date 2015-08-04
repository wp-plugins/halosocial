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

class HALOObject extends stdClass
{
	/**
	 * 
	 * @param mixed $clone
	 */
    public function HALOObject($clone = null)
    {
        if (is_object($clone)) {
            foreach (get_object_vars($clone) as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    /**
     * get Instance
     * 
     * @param  mixed $d
     * @return object
     */
    public static function getInstance($d)
    {
        return new HALOObject(self::fromArray($d));
    }
    /**
     * converte array to object
     * 
     * @param  mixed $
     * @return object
     */
    public static function fromArray($d)
    {
        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return (object) array_map('HALOObject::fromArray', $d);
        } else {
            // Return object
            return $d;
        }
    }

    /**
     * set object property by giving namespace
     * 
     * @param string $namespace
     * @param array  $value
     */
    public function setNsArray($namespace, array $value)
    {
        $this->setNsValue($namespace, HALOObject::getInstance($value));
    }

    /**
     * set object property by giving namespace
     * 
     * @param string $namespace
     * @param object
     */
    public function setNsValue($namespace, $value)
    {
        $parts = explode('.', $namespace);
        $obj = &$this;
        foreach ($parts as $name) {
            if (!isset($obj->$name)) {
                $obj->$name = new HALOObject();
            }
            $obj = &$obj->$name;
        }
        $obj = $value;
    }

    /**
     * get object property value by giving namespace
     * 
     * @param  string $namespace
     * @param  mixed $default 
     * @return object 
     */
    public function getNsValue($namespace, $default = null)
    {
        $parts = explode('.', $namespace);
        $obj = &$this;
        foreach ($parts as $name) {
            if (isset($obj->$name)) {
                $obj = &$obj->$name;
            } else {
                return $default;
            }
        }
        $value = $obj;
        return $value;

    }

}
