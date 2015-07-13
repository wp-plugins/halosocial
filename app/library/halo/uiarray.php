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

class HALOUIBuilderArray extends ArrayObject
{

    /**
     * initialize a new UI object
     *
     * @return HALOUIBuilderArray new object
     *
     */
    public static function getInstance()
    {
        return new HALOUIBuilderArray();
    }

    /**
     * another initialize method with json object as parameter
     *
     * @param object $obj json object
     * @return HALOUIBuilder new object
     */
    public static function getInstanceFromJSON($obj)
    {
        $builderArray = new HALOUIBuilderArray();
        foreach ((array) ($obj) as $position => $element) {
            $ui = HALOUIBuilder::getInstanceFromJSON($element);
            $builderArray[$position] = $ui;
        }
        return $builderArray;
    }

    /**
     * add another HALOUIBuilder object as a child
     *
     * @param string position
     * @param HALOUIBuilder the child object to be added
     * @return HALOUIBuilder this object
     */
    public function add(HALOUIBuilder $ui)
    {
        $this->append($ui);
        return $this;
    }
    /**
     * 
     * @param  string $template 
     * @return string
     */
    public function fetch($template = 'ui/default')
    {
        $result = '';
        foreach ($this as $element) {
            if (is_object($element) && method_exists($element, 'fetch')) {
                $result .= $element->fetch($template);
            }
        }
        return $result;
    }

    /******** Magic function define ***************/
    /**
     * 
     * 
     * @param  string $func
     * @param  array $argv
     * @return mixed
     */
    public function __call($func, $argv)
    {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_') {
            throw new BadMethodCallException(__CLASS__ . '->' . $func);
        }
        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }
    /**
     * 
     * @return string 
     */
    public function __toString()
    {
        return $this->fetch();
    }

}
