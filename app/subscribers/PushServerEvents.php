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

class PushServerEventHandler
{

	/**
	 * 
	 * @param  array $events          
	 */
    public function subscribe($events)
    {
        //push service for new message created
        $events->listen('message.onAfterAdding', 'PushServerEventHandler@onMessageCreate');

        //push service for new notification created
        $events->listen('notification.onAfterAdding', 'PushServerEventHandler@onNotificationCreate');

    }

    /**
     * Event handler to create a new activities
     * 
     * @param  string $message
     * @param  mixed $conv        
     */
    public function onMessageCreate($message, $conv)
    {
        //get the attenders list
        $attenders = $message->conversation->attenders;
        $attenders = explode(',', $attenders);
        //send push command to all attenders
        PushClient::broadcast($attenders, 'message');
    }

    /**
     * Event handler on new notification added for push service
     * 
     * @param  string $notification 
     * @return bool
     */
    public function onNotificationCreate($notification)
    {
        //prepare data

        $receivers = $notification->receivers()->get();
        $receiverIds = array();
        foreach ($notification->receivers as $receiver) {
            $receiverIds[] = $receiver->user_id;
        }
        //force receivers perform updating
        PushClient::broadcast($receiverIds, 'notification');
        return true;
    }

}
