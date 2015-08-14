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

class HALOMailqueueAPI
{

    /**
     * add new notificaiton record to database
     * 
     * @param array $data
     *'to' => to email address
     *'subject' => email subject
     *'plain_msg' => plain msg
     * 'html_msg' => html msg
     * 'template' => template
     */
    public static function add($data)
    {
        $mailqueue = array();
        $emailContent = new HALOMailqueueModel();
        $emailContent->bindData($data);
        //prefill data
        $emailContent->status = HALO_MAILQUEUE_PENDING;
        $emailContent->scheduled = Carbon::now();
        $emailContent->source_str = 'system_0';
        if (!$emailContent->validate()->fails()) {
            $emailContent->save();
        } else {
            HALOResponse::addMessage($emailContent->getValidator()->messages());
        }
    }
}
