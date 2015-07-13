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

class AjaxController extends BaseController
{

    /**
     * Initializer.
     *
     * @return \AdminController
     */
    public function __construct()
    {
        //mark this as ajax request so that we can treat differently in specific context
        JAXResponse::setAjax();

        parent::__construct();
        // Apply the admin auth filter for admin access
        //$this->beforeFilter('admin-auth');
    }

    /**
     * Entry point for all ajax call
     *
     * @return json object
     */
    public function call()
    {
        $controllerName = Input::get('com');

        $arr = explode(',', $controllerName);
        if (count($arr) == 2 && $arr[0] == 'admin') {
            //for admin ajax, need to apply filter for authentiacation
            $className = 'Admin' . ucfirst($arr[1]) . 'Controller';
        } elseif (count($arr) == 1) {
            //frontend ajax call
            $className = ucfirst($arr[0]) . 'Controller';

        } else {
            // invalid format
            return;
        }
        $func = Input::get('func');

        //check if controller and method found
        //add ajax prefix to function name
        $func = 'ajax' . ucfirst($func);
        if (!class_exists($className)) {
            return "unknown";
        }
        $controller = new $className();
        $args = $this->_parsePostData();

        $response = call_user_func_array(array($controller, $func), $args);

        return $response;

    }
    /**
     * _parsePostData Decode a data json string to Array
     *
     * @return array
     */
    private function _parsePostData()
    {
        $args = array();
        $argCount = 0;
        $data = Input::except(array('com', 'func', 'csrf_token'));

        // All POST data that are meant to be send to the function will
        // be appended by 'arg' keyword. Only pass this vars to the function
        foreach ($data as $key => $postData) {
            if (substr($key, 0, 3) == 'arg') {
                //if ( get_magic_quotes_gpc() ) {
                //$postData = stripslashes($postData);
                //}

                $postData = ($this->nl2brStrict(rawurldecode($postData)));
                $decoded = json_decode($postData);
                $arg = "";
                if (isset($decoded->_a_)) {
                    $arg = $decoded->_a_;
                } elseif (property_exists($decoded, '_f_')) {
                    //convert json object to post array data
                    $arg = json_decode(json_encode($decoded->_f_), true);
                } elseif (property_exists($decoded, '_d_')) {
                    $arg = $decoded->_d_;
                }

                $args[] = $arg;
                $argCount++;
            }
        }
        return $args;

    }
    /**
     * nl2brStrict replace "/\r\n|\n|\r/" to <br>
     *
     * @param  string $text string to replace
     * @return string       string after replace
     */
    private function nl2brStrict($text)
    {
        return preg_replace("/\r\n|\n|\r/", " <br />", $text);
    }
    /**
     * br2nl replace tag <br/>  to "\n"
     *
     * @param  string $text string to replace
     * @return string       string after replace
     */
    private function br2nl($text)
    {
        return str_replace(' <br />', "\n", $text);
    }

}
