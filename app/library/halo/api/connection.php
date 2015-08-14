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

class HALOConnectionAPI
{

    /**
     * add a friend request from $from user to $to user
     * 
     * @param object  $from
     * @param object  $to
     * @param bool $force
     */
    public static function addFriendRequest($from, $to, $force = false)
    {
        //trigger before event
        if (Event::fire('friend.onBeforeRequest', array($from, $to), true) === false) {
            //error occur, return
            return false;
        }

        //check if can make friend request
        if (!self::canSendFriendRequest($from, $to, $force)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Friend request is not allowed')));
            return false;

        }
        //check for pending friend requests
        /*
        if($from->connections()->where('from_id',$from->id)
        ->where('to_id',$to->id)
        ->where('role', HALO_CONNECTION_ROLE_FRIEND)
        ->where('status',HALO_CONNECTION_STATUS_REQUESTING)->first()){
        return true;    //skip it
        }
         */
        $from->connections()->sync(array($to->id => array('status' => HALO_CONNECTION_STATUS_REQUESTING,
            'role' => HALO_CONNECTION_ROLE_FRIEND, )), false);
        //trigger after event
        Event::fire('friend.onAfterRequest', array($from, $to));
        //on album added, add its reference as HALOResponse data
        return true;
    }

    /**
     * approve friend request from $from user to $to user
     * 
     * @param  object $from 
     * @param  object $to
     * @return bool
     */
    public static function approveFriendRequest($from, $to)
    {
        //trigger before event
        if (Event::fire('friend.onBeforeApprove', array($from, $to), true) === false) {
            //error occur, return
            return false;
        }

        //check if can make friend request
        if (!self::canApproveRequest($from, $to)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Friend request approval is not allowed')));
            return false;

        }
        $from->connections()->sync(array($to->id => array('status' => HALO_CONNECTION_STATUS_CONNECTED,
            'role' => HALO_CONNECTION_ROLE_FRIEND, )), false);
        //create two way connection
        $to->connections()->sync(array($from->id => array('status' => HALO_CONNECTION_STATUS_CONNECTED,
            'role' => HALO_CONNECTION_ROLE_FRIEND, )), false);

        //trigger after event
        Event::fire('friend.onAfterApprove', array($from, $to));
        //on album added, add its reference as HALOResponse data
        return true;
    }

    /**
     * reject friend request from $from user to $to user
     * 
     * @param  object $from
     * @param  object $to
     * @return bool
     */
    public static function rejectFriendRequest($from, $to)
    {
        //trigger before event
        if (Event::fire('friend.onBeforeReject', array($from, $to), true) === false) {
            //error occur, return
            return false;
        }

        //check if can make friend request
        if (!self::canApproveRequest($from, $to)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Friend request does not exists')));
            return false;

        }

        //create reject connection
        $to->connections()->sync(array($from->id => array('status' => HALO_CONNECTION_STATUS_REJECTED,
            'role' => HALO_CONNECTION_ROLE_FRIEND, )), false);

        //set follower relationship on reject
        HALOFollowAPI::follow($to, $from->id);

        //trigger after event
        Event::fire('friend.onAfterReject', array($from, $to));
        //on album added, add its reference as HALOResponse data
        return true;
    }

    /**
     * remove friendship between $from and $to
     * 
     * @param  object $from
     * @param  object $to
     * @return bool
     */
    public static function unFriend($from, $to)
    {
        //trigger before
        if (Event::fire('friend.onBeforeUnFriend', array($from, $to), true) === false) {
            //error occur, return
            return false;
        }

        //detach the friend relationship on both ways
        $to->friends()->detach($from->id);
        $from->friends()->detach($to->id);

        //trigger after event
        Event::fire('friend.onAfterUnFriend', array($from, $to));
        //on album added, add its reference as HALOResponse data
        return true;
    }

    /**
     * function to check permission that user can send friend request
     * 
     * @param  object  $from
     * @param  object  $to
     * @param  bool $force
     * @return bool
     */
    public static function canSendFriendRequest($from, $to, $force = false)
    {
        if ($from->id == $to->id) {
            return false;
        }
        //can not make friend reqeust to yourself
        if ($force) {
            $denniedStatus = array(HALO_CONNECTION_STATUS_CONNECTED, HALO_CONNECTION_STATUS_BLOCKED);
        } else {
            $denniedStatus = array(HALO_CONNECTION_STATUS_REQUESTING, HALO_CONNECTION_STATUS_CONNECTED, HALO_CONNECTION_STATUS_REJECTED, HALO_CONNECTION_STATUS_BLOCKED);
        }
        $connections = $from->connections(HALO_CONNECTION_ROLE_FRIEND)
                            ->whereIn('status', $denniedStatus)
                            ->where('to_id', $to->id)
            ->get();
        return (count($connections)) ? false : true;
    }

    /**
     * function to check permission that user can approve a friend request
     * 
     * @param  object $from
     * @param  object $to
     * @return bool
     */
    public static function canApproveRequest($from, $to)
    {
        if ($from->id == $to->id) {
            return false;
        }
            //can not make friend reqeust to yourself

        $connections = $from->connections(HALO_CONNECTION_ROLE_FRIEND)
                            ->where('status', HALO_CONNECTION_STATUS_REQUESTING)
                            ->where('to_id', $to->id)
            ->get();
        return (count($connections)) ? true : false;
    }

}
