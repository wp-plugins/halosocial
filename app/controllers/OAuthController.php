<?php
use OAuth2\OAuth2;
use OAuth2\Token_Access;
use OAuth2\Exception as OAuth2_Exception;

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

class OAuthController extends BaseController
{
    /**
     * Laravel application
     *
     * @var Illuminate\Foundation\Application
    */
    public static $app;
    /**
     * Create a new ConfideUser instance.
     */
    public function __construct()
    {
        if ( ! static::$app )
            static::$app = app();
    }
    public function getIndex($provider)
    {
        $provider = OAuth2::provider($provider, array(
            'id' => '601044619917585',
            'secret' => 'ee33f5b397f0e8143d24de8210526bcf'
        ));

        if ( ! isset($_GET['code']))
        {
            // By sending no options it'll come back here
            return $provider->authorize();
        }
        else
        {
            // Howzit?
            try
            {
                $params = $provider->access($_GET['code']);

                $token = new Token_Access(array(
                    'access_token' => $params->access_token
                ));
                $user = $provider->get_user_info($token);

                //retrieve the userModel
                $userModel= new User();
                $userModel->getTable();
                $userModel->email=$user["email"];
                $userModel->username=$user["nickname"];
                if ( ! static::$app )
                    static::$app = app();
                if(static::$app['confide.repository']->userExists($userModel)){
                return Redirect::to('/');
                }
                else{
                return Redirect::to('/oauth/signup/'.$userModel->email.'/'.$token)->with('linkedUser',$userModel);}
            }

            catch (OAuth2_Exception $e)
            {
                show_error('That didnt work: '.$e);
            }
        }
    }

    public function createOAuthUser()
    {
        
    }
    public function getSignup($email,$token)
    {
        $user=Session::get('linkedUser');
        if($token&&$user!=null)
            Session::set($token,$user);
        if($user==null&&$token);
        {
            $user=Session::get($token);
        }
        return View::make('site/oauth/signup',compact('user'));
    }
}

