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

class HALOOAuthAPI
{

    /**
     * @api: login via oauth
     * 
     * @param  array $data
     * @return bool
     */
    public static function login($data)
    {
        $default = array('consumer_id' => HALO_OAUTH_CONSUMER_FB,
            'uid' => '',
            'email' => '',
            'name' => '',
            'params' => '');
        $data = array_merge($default, $data);
        //trigger before album create event
        if (Event::fire('oauth.onBeforeLogin', array($data), true) === false) {
            //error occur, return
            return false;
        }
        //validate data input

        //check for the existing user_oauth record
        $userOauth = HALOUseroauthModel::where('consumer_id', '=', $data['consumer_id'])
            ->where('uid', '=', $data['uid'])
            ->get()    ->first();
        if (!$userOauth) {
                //this is the first time login, init user for this
                //1. find the user with the same email address
            $user = UserModel::getUserByEmail($data['email']);
            if (!$user) {
                    //create new user with random password
                $userData = array();
                $userData['password'] = HALOUtilHelper::generateRandomString();
                $userData['password_confirmation'] = $userData['password'];
                $userData['email'] = $data['email'];
                $userData['username'] = $data['name'];
                    //check if username is exists
                if (UserModel::getUserByUsername($userData['username'])) {
                        //create a unique username
                    $userData['username'] = Str::slug($userData['username']) . HALOUtilHelper::uniqidInt();
                }
                $userData['name'] = $data['name'];

                $user = UserModel::createNew($userData);
				
                if (!$user) {
                    var_dump(HALOResponse::getMessage());exit;
					
                    return false;
                }
            }
                //save a new user_oauth record
            $user = HALOUserModel::getUser($user->getId());
                //still Cannot create new user show error
            if (empty($user->id)) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Cannot create new user')));
                return false;

            }
            $userOauth = new HALOUseroauthModel(array('consumer_id' => $data['consumer_id'], 'uid' => $data['uid']));
            $user->oauth()->save($userOauth);
        } else {
            $user = $userOauth->user()->get()->first();
        }
            //then process the login with $userOauth
        if (!$user->login()) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Login failure')));
            return false;
        }
            //trigger after oauth  login event
        Event::fire('oauth.onAfterLogin', array($user));

        HALOResponse::setData('user', $user);
        return true;
    }

}
