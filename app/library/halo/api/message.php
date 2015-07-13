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

class HALOMessageAPI
{
    protected static $_data = array();

    /*
    @api: add a new message
    @data:     message=>
    params=>
     */
    /**
     *  @api: add a new message
     *  
     * @param array $data
     * @param object $conv
     * @return  bool
     */
    public static function add($data, $conv)
    {
        $default = array('message' => '',
            'params' => '');
        $data = array_merge($default, $data);

        //trigger before adding message event
        if (Event::fire('message.onBeforeAdding', array($data, $conv), true) === false) {
            //error occur, return
            return false;
        }

        $message = new HALOMessageModel();

        //actor is the current user
        $my = HALOUserModel::getUser();
        $message->actor_id = $my->id;
        $message->conv_id = $conv->id;

        //validate data
        if ($message->bindData($data)->validate()->fails()) {

            $msg = $message->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        } else {
            $message->save();

            //mark the conv as updated
            $conv->touch();
        }
        //trigger event on comment submitted
        Event::fire('message.onAfterAdding', array($message, $conv));
        //on activity added, add its reference as HALOResponse data
        HALOResponse::setData('message', $message);
        return true;

        //trigger event
        Event::fire('message.create', array($message));

    }

}
