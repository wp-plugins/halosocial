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

class AdminDashboardController extends AdminController {

	/**
	 * Admin dashboard
	 *
	 */
	public function getIndex()
	{
        return View::make('admin/welcome');
        //return View::make('admin/dashboard');
	}

    /**
     * Admin welcome
     * @return View
     */
    public function getWelcome()
    {
        return View::make('admin/welcome');
    }

    /**
     * Ajax function to update welcome content
     * 
     * @return mixed
     */
    public function ajaxUpdateWelcome()
    {
        $requestUrl = 'https://docs.google.com/uc?export=download&id=';
        $ids = array(
            'halo.welcome.getstarted' => '0B1n8wAlIE7_wbFpHVzJXRE1RbXM',
            'halo.welcome.pricing' => '0B1n8wAlIE7_wVXRhbkpXQWNtWUk'
        );
        foreach ($ids as $key => $id) {
            $content = Cache::remember(md5($key), 7 * 24 * 60, function() use ($requestUrl, $id) {
                $rtn = '';
                $content = @file_get_contents($requestUrl . $id);
                if ($content) {
                    $rtn = $content;
                }
                return $rtn;
            });
            HALOResponse::updateZone($content);
        }

        // HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'module.halo_getstarted', array('zone' => 'halo.welcome.getstarted'))->fetch(null, 'halo.welcome.getstarted'));
        // HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'module.halo_pricing', array('zone' => 'halo.welcome.pricing'))->fetch(null, 'halo.welcome.pricing'));
        return HALOResponse::sendResponse();
    }

    /**
     * Subscribe NewsLetter
     * 
     * @param  array $postData
     * @return mixed
     */
    public function ajaxSubscribeNewsLetter($postData)
    {
        $validator = Validator::make($postData, array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email'
        ));

        if ($validator->fails()) {
            HALOResponse::addMessage($validator->messages());
            return HALOResponse::sendResponse();
        }

        $data = array('email' => $postData['email'], 'name' => $postData['first_name'] . ' ' . $postData['last_name']);
        $sendy = new HALOSendyHelper();
        try {
            $rtn = $sendy->subscribe($data);
            if ($rtn['status'] === true) {
                HALOResponse::enqueueMessage(__halotext('You subscribed successfully, thank you!'));
                HALOResponse::removeZone('halo.welcome.newslettersub');
            } else {
                HALOResponse::addMessage(HALOError::failed(__halotext($rtn['message'])));
            }
        } catch (Exception $e) {
            //TODO
            HALOResponse::addMessage(HALOError::failed(__halotext('Oops! Please try again')));
        }
        return HALOResponse::sendResponse();
    }
	
}