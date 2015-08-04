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

class HALOParams
{
    public $params = null;
    /**
     * Construction function
     * 
     * @param    string    $paramStr    the param in string format
     * @param    string    $type        format type
     * @return   string    The photo path with size postfix
     */
    public function HALOParams($paramStr = '', $type = 'json')
    {
        if ($type == 'json') {
            if (empty($paramStr) || $paramStr == '""') {
                $paramStr = '{}';
            }
            $this->params = json_decode($paramStr);
			if(is_null($this->params)) {
				$this->params = json_decode('{}');
			}
        } elseif ($type == 'query') {
            $paramStr = str_replace(',', '&', $paramStr);
            parse_str($paramStr, $args);
            $this->params = HALOObject::getInstance($args);
        } elseif (is_array($paramStr) && $type == 'array') {
            $this->params = HALOObject::getInstance($paramStr);

        }

    }
    /**
     * get Instance
     * 
     * @param  string $paramStr
     * @param  string $type
     * @return HALOParams
     */
    public static function getInstance($paramStr = '', $type = 'json')
    {
        return new HALOParams($paramStr, $type);
    }
    /**
     *
     * 
     * @param  mixed $key 
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!isset($this->params->$key) || $this->params->$key === '') {
            return $default;
        } else {
            return $this->params->$key;
        }
    }
    /**
     *
     * 
     * @param mixed $key
     * @param HALOParams
     */
    public function set($key, $value)
    {
        $this->params->$key = $value;
        return $this;
    }
    /**
     *
     * 
     * @param  mixed $key
     * @return HALOParams
     */
    public function clear($key)
    {
        if (isset($this->params->$key)) {
            unset($this->params->$key);
        }
        return $this;
    }
    /**
     *
     * 
     * @return string
     */
    public function toString()
    {
        return json_encode($this->params);
    }
    /**
     *
     * 
     * @param  string $seprator
     * @return string
     */
    public function toQuery($seprator = '&')
    {
        $queryArray = array();
        if (is_object($this->params)) {
            foreach (get_object_vars($this->params) as $key => $value) {
                $queryArray[] = "$key=$value";
            }
        }
        return implode($queryArray, $seprator);
    }
}
