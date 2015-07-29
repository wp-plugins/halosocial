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

class HALOFactory
{
    /**
     * Load multiple users at a same time to save up on the queries.
     *
     * @param    Array    $userIds    An array of user ids to be loaded.
     * @return    bool        True upon success
     */
    public static function loadUsers(array $userIds)
    {

    }

    /**
     * Retrieves a HALOUser object given the user id.
     *
     * @param    int        $id        A user id (optional)
     * @param    HALOUser    $obj    An existing user object (optional)
     *  @return    HALOUser    A GGUser object
     */
    public static function getUser($id = null, $obj = null)
    {

    }

    /**
     * Returns the current user requested
     *
     * @param
     * @return    object    Current HALOUser object
     */
    public static function getRequestUser()
    {
        //return CFactory::getUser($id);
    }

    /**
     * Load multiple post at a same time to save up on the queries.
     * 
     * @param    Array    $postIds    An array of post ids to be loaded.
     * @return    boolean        True upon success
     */
    public static function loadPosts(array $postIds)
    {

    }

    /**
     * Retrieves a HALOClassified object given the user id.
     * 
     * @param    int        $id        A user id (optional)
     * @param    HALOPost    $obj    An existing user object (optional)
     * @return    HALOPost    A GGPost object
     */
    public static function getPost($id = null, $obj = null)
    {

    }

    /**
     * Retrieves HALOConfig configuration object
     * @return    object    HALOConfig object
     */
    public static function getConfig()
    {
        //return HALOConfig::getInstance();
    }

}
