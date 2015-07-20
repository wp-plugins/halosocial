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

class HomeController extends BaseController
{

    protected $user;
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Returns all the blog posts.
     *
     * @return View
     */
    public function getIndex()
    {
        $user = HALOUserModel::getUser();

        $rtn = View::make('site/home/index', compact('user'));

        return $rtn;

    }

    /**
     * ajax handler to display user section content
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxDisplaySection($postData)
    {
        $section = isset($postData['usec']) ? $postData['usec'] : '';
        if (!$section) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Section')));
            return HALOResponse::sendResponse();
        }
        //forward to the callback to render the section content
        Event::fire('system.onDisplaySiteInfo', array($section, $postData));
        return HALOResponse::sendResponse();
    }

}
