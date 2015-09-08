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

class HALOMentionAPI
{

    /**
     * @api: check if the message has mention data
     * 
     * @param  string $message
     * @param  mixed $target 
     * @return bool
     */
    public static function process($message, $target)
    {
        $mentionList = self::parseMessage($message);
        if (!empty($mentionList)) {
            //trigger onMention event
            Event::fire('mention.onMention', array($mentionList, $target));
            //treat mention users as the follower of the target
        }
        return true;
    }

    /**
     * function to parse on a message to get a list of mentioned target
     * 
     * @param  string
     * @return array
     */
    public static function parseMessage($message)
    {
        //search for user tagging
        $pattern = '#@\[([0-9]+) ([\w| ]+)\]#u';
        $matches = array();
        $occur = preg_match_all($pattern, $message, $matches);
        $userIds = array();

        if ($occur) {
            $searches = array();
            $replaces = array();
            for ($i = 0; $i < $occur; $i++) {
                $userIds[] = $matches[1][$i];
                $searches[$matches[1][$i]] = $matches[0][$i];
                $replaces[$matches[1][$i]] = $matches[2][$i];
            }
        }
        //take only valid user_id
        if (!empty($userIds)) {
            $userIds = HALOUserModel::whereIn('user_id', $userIds)->lists('user_id');
        }

        return $userIds;
    }

}
