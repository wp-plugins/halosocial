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
 
use Illuminate\Support\MessageBag;

class HALOError
{
	/**
	 * get Errors
	 * 
	 * @return string
	 */
    public static function getErrors()
    {
        // return Session::get('errors');
		$error = Session::get('errors');
		if($error) return $error;
		return HALOResponse::getMessage();

    }
    /**
     * get Message Bag
     * 
     * @return object
     */
    public static function getMessageBag()
    {
        return new MessageBag();
    }
    /**
     * 
     * @return return empty message bag
     */
    public static function passed()
    {
        return HALOError::getMessageBag();//return empty message bag
    }
    /**
     * 
     * @param  string $msg
     * @param  mixed $messageBag
     * @return object
     */
    public static function failed($msg, $messageBag = null)
    {
        $mb = HALOError::getMessageBag();
        $mb->add('error', $msg);
        if ($messageBag) {
            $mb->merge($messageBag->getMessages());
        }
        return $mb;
    }
	
	public static function message($name, $msg) {
        $mb = HALOError::getMessageBag();
        $mb->add($name, $msg);
		return $mb;
	}
	
	public static function abort($code = 404, $msg = 'Not Found') {
		$title = $msg;
		$messages = HALOError::getMessageBag()->add('message', $code . ' ' . $msg);
		return View::make('site/error', compact('title', 'messages'));
	}
}
