<?php
use Illuminate\Routing\Controller;
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

class BaseController extends Controller
{

    /**
     * Initializer.
     *
     * @access   public
     * @return \BaseController
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
        HALOGATracking::$haloPageGroup = 'halo_' . lcfirst(str_replace('Controller', '', get_class($this)));
        HALOOutputHelper::storeViewMode();
		if(!HALOResponse::isAjax() && !HALOAuth::can('halo.go')) {
			$text1 = "LGduaXNzaW0gc2kgeWVrIGVzbmVjaWwgcydsYWljb1NvbGFI";
			$text2 = "LnJvdGFydHNpbmltZGEgcydldGlzIGVodCB0Y2F0bm9jIGVzYWVscA==";
			echo strrev(base64_decode($text1)) . ' ' . strrev(base64_decode($text2));
			exit;
		}
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * ajax task to show error message on client
     *
     * @param   string $error
     * @return JSON
     */
    public function ajaxShowError($error)
    {
        HALOResponse::addScriptCall('halo.util.setSystemError', $error);
        return HALOResponse::sendResponse();

    }
}
