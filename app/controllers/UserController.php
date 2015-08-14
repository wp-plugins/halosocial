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

class UserController extends BaseController {

    /**
     * User Model
     * @var User
     */
    protected $user;

    /**
     * Inject the models.
     * @param User $user
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Redirect users to specific locations after login
     *
     * @param HALOUserModel $user
     * @return string
     */
    private function getRedirectLogin($user = null)
    {
        $redLogin = HALOConfig::get('user.redirectLogin', 0);
        // Set default location as frontend
        $location = URL::to(get_permalink(halo_getPageId()));
        if ($redLogin == 0) {
            return $location;
        } elseif ($redLogin == 1 && $user) {
            return $user->getUrl();
        } elseif ($redLogin == 2) {
            return URL::to('?view=home&usec=member');
        }
        return $location;
    }

    /**
     * Redirect users to specific locations after logout
     *
     * @return string
     */
    private function getRedirectLogout()
    {
        $redLogout = HALOConfig::get('user.redirectLogout', 0);
        // Set default location as frontend
        $location = URL::to(get_permalink(halo_getPageId()));
        if ($redLogout == 0) {
            return $location;
        } elseif ($redLogout == 1) {
            return URL::to('?view=home&usec=member');
        }
        return $location;
    }

    /**
     * Users settings page
     *
     * @return View
     */
    public function getIndex()
    {
        //list($user,$redirect) = $this->user->checkAuthAndRedirect('user');
        //if($redirect){return $redirect;}
		$user = HALOUserModel::getUser(1);
        // Check if the user exists
        if (is_null($user))
        {
            return HALOError::abort();
        }

        return View::make('site/user/profile', compact('user'));    
	}

    /**
     * Display current user profile page
     *
     * @return View
     */
    public function getShow()
    {
		$user = HALOUserModel::getUser();
        // Check if the user exists
        if (is_null($user))
        {
            return HALOError::abort();
        }

        return View::make('site/user/profile', compact('user'));    
	}

    /**
     * Stores new user
     *
     */
    public function postIndex()
    {
        $this->user->username = Input::get( 'username' );
        $this->user->email = Input::get( 'email' );

        $password = Input::get( 'password' );
        $passwordConfirmation = Input::get( 'password_confirmation' );

        if(!empty($password)) {
            if($password === $passwordConfirmation) {
                $this->user->password = $password;
                // The password confirmation will be removed from model
                // before saving. This field will be used in Ardent's
                // auto validation.
                $this->user->password_confirmation = $passwordConfirmation;
            } else {
                // Redirect to the new user page
                return Redirect::to('user/create')
                    ->withInput(Input::except('password','password_confirmation'))
                    ->with('error', __halotext('Password does not match'));
            }
        } else {
            unset($this->user->password);
            unset($this->user->password_confirmation);
        }

        // Save if valid. Password field will be hashed before save
        $this->user->save();

        if ( $this->user->id )
        {
            // the data that will be passed into the mail view blade template
            $data = array(
                'name'  => $this->user->username);
            Mail::send("emails.welcome",$data,function($message){
            $message->to($this->user->email, 'halo.social')->subject('Welcome!');

        });
            // Redirect with success message, You may replace "__halotext(..." for your custom message.
            return Redirect::to('user/login')
                ->with( 'notice', __halotext('User account has been created') );
        }
        else
        {
            // Get validation errors (see Ardent package)
            $error = $this->user->errors()->all();

            return Redirect::to('?view=user&task=create')
                ->withInput(Input::except('password'))
                ->with( 'error', $error );
        }
    }

    /**
     * Displays the form for user creation
     *
     */
    public function getCreate()
    {
        return View::make('site/user/create');
    }


    /**
     * Displays the login form
     *
     */
    public function getLogin()
    {
        $user = HALOUserModel::getUser();
        if(!empty($user->id)){
            return Redirect::to('/');
        }

        return View::make('site/user/login');
    }

    /**
     * Attempt to do login
     *
     */
    public function postLogin()
    {
        $input = array(
            'email'    => Input::get( 'email' ), // May be the username too
            'username' => Input::get( 'email' ), // May be the username too
            'password' => Input::get( 'password' ),
            'remember' => Input::get( 'remember' ),
        );

        // If you wish to only allow login from confirmed users, call logAttempt
        // with the second parameter as true.
        // logAttempt will check if the 'email' perhaps is the username.
        // Check that the user is confirmed.
        if ($user = HALOUserModel::logAttempt( $input, true ) )
        {
            return Redirect::to($this->getRedirectLogin($user));
        }
        else
        {
			$error = HALOResponse::getMessage();
			
			$msg = $error->first('error');
			$err_code = 1;
			if(strpos($msg, 'blocked')) {
				$err_code = 2;
			} else if(strpos($msg, 'actived')) {
				$err_code = 3;
			}
            return Redirect::to('?view=user&task=login&err='. $err_code);
        }
    }

    /**
     * Displays the login form
     *
     */
    public function getResend_code()
    {
        $user = HALOUserModel::getUser();
        if(!empty($user->id)){
            return Redirect::to('/');
        }

        return View::make('site/user/resend');
    }

    /**
     * Attempt to do login
     *
     */
    public function postResend_code()
    {
        $input = array(
            'email'    => Input::get( 'email' ), // May be the username too
        );
		
		$email = Input::get('email');
		if(empty($email)) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Please enter your email.')));
			return View::make('site/user/resend');
		}
		
		$user = UserModel::getUserByEmail($email);
		//check user exist
		if(!$user) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Email does not exist.')));
			return View::make('site/user/resend');		
		}
		//user must not activated
		if($user->getConfirmState()) {
			HALOResponse::addMessage(HALOError::failed(__halotext('The account has been activated.')));
			return View::make('site/user/resend');
		}
		
		$user->generateConfirmCode();
		$user->sendConfirmationEmail();
		HALOResponse::addMessage(HALOError::failed(__halotext('An email with an activation link has been sent to your email. You must follow the link in the email to activate your account.')));
		return View::make('site/user/resend');
    }
	
	/**
     * Activate account
     *
     */	
	public function getActivate() {
		$token = Input::get('act_token','');
		$userId = Input::get('uid', '');
		if(empty($userId) || empty($token)) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid activation link.')));
			return View::make('site/user/resend');		
		}
		$user = HALOUserModel::getUser($userId);
		//user must exists
		if(!$user) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid activation link.')));
			return View::make('site/user/resend');		
		}
		//matching token
		if($user->user->getConfirmCode() != $token) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid activation link.')));
			return View::make('site/user/resend');		
		}
		
		$user->user->setConfirmState(1);
		return Redirect::to('?view=user&task=login&code=6');

	}

    /**
     * Displays the register form
     *
     */
    public function getRegister()
    {
		//registration function must be enabled
		if(!UserModel::canRegister()) {
			return Redirect::to('/');
		}
        $user = HALOUserModel::getUser();
        if(!empty($user->id)){
            return Redirect::to('/');
        }
		$data = array();
        return View::make('site/user/register', compact('data'));
    }


    /**
     * Attempt to create new account
     *
     */
    public function postRegister()
    {
		//registration function must be enabled
		if(!UserModel::canRegister()) {
			return Redirect::to('/');
		}
        $input = array(
            'email'    => Input::get( 'email' ), 
            'username' => Input::get( 'username' ), 
            'name' => Input::get( 'username' ), 
            'display_name' => Input::get( 'display_name' ), 
            'password' => Input::get( 'password' ),
            'password_confirmation' => Input::get( 'password_confirmation' )
        );

		//verify input
		$model = new UserModel();
		$validator = Validator::make($input,$model->getValidateRule());
        if ($validator->fails()) {
            $msg = $validator->messages();
			HALOResponse::addMessage(HALOError::failed(__halotext('Could not create new user. Please check your account information'), $msg));
			return View::make('site/user/register', compact('input'));
        }

		//validate uesr profiles
		$user = new HALOUserModel();
        $user->profile_id = Input::get('profile_id');
		$user->id = 0;
		
		$rules = $user->getValidateRule();
		
		$profileFields = $user->getProfileFields()->get();

		foreach($profileFields as $field){
		
			$rules = array_merge($rules,$field->toHALOField()->getValidateValueRule());
		}
		// var_dump($rules);exit;
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes())
        {
			$user = HALOUserModel::createNew($input);
			if($user){
				//send activate email
				if(HALOConfig::get('user.confirmEmail', 1)) {
					$user->user->sendConfirmationEmail();
					$redirect = '?view=user&task=login&code=4';
				} else {
					//redirect to login page with message
					$user->user->sendWelcomeEmail();
					//automatically login user
					$user->user->login();
					$redirect = '?view=user&task=login&code=5';
				}
				
				//save user profile fields
				$user->profile_id = Input::get('profile_id');
				$user->save();
				$profile = $user->getProfile()->first();
				$profile->saveFieldValues($user,Input::get('field',array()),Input::get('access',array()));
				return Url::to($redirect);
			} else {
				//get error bag message from HALOResponse
				$err = HALOResponse::getMessage();
				return View::make('site/user/register', compact('input'));
			}
        } else {		
            $msg = $validator->messages();
			HALOResponse::addMessage(HALOError::failed(__halotext('Could not create new user. Please check your profile information.'), $msg));
			return View::make('site/user/register', compact('input'));
		}		
    }

    /**
     * Validate new user
     *
     */
	public function ajaxValidateNewUser($email, $username) {
		$data = array('email' => $email, 'username' => $username);
		$model = new UserModel();
		$rules = array('email' => $model->getValidateRule()['email'], 'username' => $model->getValidateRule()['username']);
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            $msg = $validator->messages();
            HALOResponse::addMessage($msg);
        }
		return HALOResponse::sendResponse();
	}

    /**
     * Displays the ajax login form
     *
     */
    public function ajaxShowLogin($message = '')
    {
			//display ajax login if in desktop
			if(Agent::isMobile() || Agent::isTablet()){
				HALOResponse::redirect(URL::to('?view=user&task=login'),$message);
				return HALOResponse::sendResponse();
			} else {
				//display login page if in mobile
				return HALOResponse::login($message);
			}
    }
		
	/*
		Display login toggle content
	*/
	public function ajaxShowLoginToggle(){
		$builder = HALOUIBuilder::getInstance('ajaxlogin','ajaxlogin',array('name'=>'loginForm','msg'=>''));
		$content = 	$builder->fetch();
		$builder = HALOUIBuilder::getInstance('','div_wrapper',array('html'=>HALOUIBuilder::getInstance('ajaxlogin','ajaxlogin',array('name'=>'loginForm','msg'=>''))->fetch()
																																,'class'=>'halo-login-loading'
																																,'zone'=>'halo-login-list'
																															));
		HALOResponse::updateZone($builder->fetch());
		return HALOResponse::sendResponse();		
	}

    /**
     * Attempt to do ajax login
     *
     */
    public function ajaxLogin($postData)
    {
        $input = array(
            'email'    => $postData['email'], // May be the username too
            'username' => $postData['email'], // May be the username too
            'password' => $postData['password'],
            'remember' => $postData['remember'],
        );

        // If you wish to only allow login from confirmed users, call logAttempt
        // with the second parameter as true.
        // logAttempt will check if the 'email' perhaps is the username.
        // Check that the user is confirmed.
        if ( HALOUserModel::logAttempt( $input, true ) )
        {
            $r = Session::get('loginRedirect');
            if (!empty($r))
            {
                Session::forget('loginRedirect');
                return HALOResponse::redirect($r)->sendResponse();
            }
			//refresh the page
			return HALOResponse::refresh()->sendResponse();
        }
        else
        {
            // Check if there was too many login attempts
            //$err_msg = __halotext('Wrong username or password');
			return HALOResponse::sendResponse();
 			
        }
    }

	/*
		ajax handler to display user section content
	*/
	public function ajaxDisplaySection($postData){
		$section = isset($postData['usec'])?$postData['usec']:'';
		if(!$section){
			HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Section')));
			return HALOResponse::sendResponse();
		}
		//forward to the callback to render the section content
		Event::fire('user.onDisplayUserInfo',array($section,$postData));
		return HALOResponse::sendResponse();
	}

	/*
		ajax handler to show change user password
	*/
	public function ajaxShowChangePassword(){
		$user = HALOUserModel::getUser();

		if(!$user){
			HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
			return HALOResponse::sendResponse();
		}
		
		$builder = HALOUIBuilder::getInstance('','form.form',array('name'=>'popupForm'))
					->addUI('oldPassword', HALOUIBuilder::getInstance('','form.password',array('name'=>'old_pass','title'=>__halotext('Current Password'),'placeholder'=>__halotext('Your current password'),'value'=>'','validation'=>'required')))
					->addUI('newPassword', HALOUIBuilder::getInstance('','form.password',array('name'=>'password','title'=>__halotext('New Password'),'placeholder'=>__halotext('Your new password'),'value'=>'','validation'=>'required')))
					->addUI('newPasswordConfirm', HALOUIBuilder::getInstance('','form.password',array('name'=>'password_confirmation','title'=>__halotext('Retype New Password'),'placeholder'=>__halotext('Retype new password'),'value'=>'','validation'=>'required')))
					;
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>__halotext('Save'),"onclick"=>"halo.user.changePassword()","icon"=>"check"));
		$title = __halotext('Change your password');
		HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();
	}
	
	/*
		ajax handler to save new password
	*/
	public function ajaxChangePassword($postData){
		$user = HALOUserModel::getUser();
		if(!$user){
			HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
			return HALOResponse::sendResponse();
		}
		//1 make sure old password is correct
		if(!$user->verifyPassword($postData['old_pass'])){
			$error = HALOError::getMessageBag();
			$error->add('old_pass',__halotext('Incorrect Password'));
			HALOResponse::addMessage($error);
			return HALOResponse::sendResponse();
		}
		//2 validate new password
		if($user->user->changePassword($postData)) {
            HALOResponse::addScriptCall('halo.user.updateHaloMyId', 0);
            HALOResponse::redirect(URL::to('?view=home&usec=stream'), __halotext('Your password has been changed successfully, please login to continue.'));
		}
		return HALOResponse::sendResponse();
	}

	/*
		ajax handler to show change user profile popup
	*/
	public function ajaxShowChangeProfile(){
		$user = HALOUserModel::getUser();

		if(!$user){
			HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
			return HALOResponse::sendResponse();
		}
		
		$builder = HALOUIBuilder::getInstance('','form.form',array('name'=>'popupForm'))
					->addUI('profile_id',HALOUIBuilder::getInstance('','form.select',array('name'=>'profile_id','id'=>'profile_id','value'=> $user->getProfileId(),
															'title'=>__halotext('Select User Profile Type'),
															'options'=>HALOProfileModel::getProfileListOption('user',true)
															)))
					;
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>__halotext('Save'),"onclick"=>"halo.user.changeProfile()","icon"=>"check"));
		$title = __halotext('Change your profile type');
		HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();
	}

	/*
		ajax handler to save new password
	*/
	public function ajaxChangeProfile($postData){
		$user = HALOUserModel::getUser();
		if(!$user){
			HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
			return HALOResponse::sendResponse();
		}
		//#1 validate profile_id
		$profileId = $postData['profile_id'];
		
		if(!HALOProfileModel::isExists('user',$profileId)){
			$error = HALOError::getMessageBag();
			$error->add('profile_id',__halotext('Invalid profile'));
			HALOResponse::addMessage($error);
			return HALOResponse::sendResponse();			
		}
		$user->profile_id = $profileId;
		if($user->save()){
			$title = __halotext('Change your profile type');
			$actionDone = HALOPopupHelper::getAction(array("name"=>__halotext('Done'),"onclick"=>"halo.util.reload()","icon"=>"check"));
			HALOResponse::addScriptCall('halo.popup.reset')
						->addScriptCall('halo.popup.setFormTitle', $title )
						->addScriptCall('halo.popup.setFormContent', __halotext('Your profile type has been changed') )
						->addScriptCall('halo.popup.addFormAction', $actionDone )
						->addScriptCall('halo.popup.addFormActionCancel')
						->addScriptCall('halo.popup.showForm' );
		} else {
			HALOResponse::addMessage(HALOError::failed(__halotext('Could not save user data')));
		}		
		return HALOResponse::sendResponse();
	}
	
    /**
     * Attempt to confirm account with code
     *
     * @param  string  $code
     */
    public function getConfirm( $code )
    {
        if ( Confide::confirm( $code ) )
        {
            return Redirect::to('?view=user&task=login')
                ->with( 'notice', __halotext('User has been confirmed') );
        }
        else
        {
            return Redirect::to('?view=user&task=login')
                ->with( 'error', __halotext('Incorrect confirmation code') );
        }
    }

    /**
     * Displays the forgot password form
     *
     */
    public function getForgot()
    {
        return Redirect::to(UserModel::getForgotLink());
    }

    /**
     * Attempt to reset password with given email
     *
     */
    public function postForgot()
    {
			$email = Input::get( 'email' );
			if( UserModel::forgotPassword($email) )
			{
				return Redirect::to('?view=user&task=forgot')
						->with( 'info', sprintf(__halotext('A reset password email has been sent to your email address: %s. Please check your email inbox for the mail and then follow the reset password URL.'),$email ));
			}
			else
			{
				return Redirect::to('?view=user&task=forgot')
						->withInput()
						->with( 'error', __halotext('There is no account for this email. Please check again.') );
			}
    }

    /**
     * Shows the change password form with the given token
     *
     */
    public function getReset( $token )
    {

        return View::make('site/user/reset')
            ->with('act_token',$token);
    }


    /**
     * Attempt change password of the user
     *
     */
    public function postReset()
    {
        $input = array(
            'act_token'=>Input::get( 'act_token' ),
            'password'=>Input::get( 'password' ),
            'password_confirmation'=>Input::get( 'password_confirmation' ),
        );

        // By passing an array with the token, password and confirmation
        if( UserModel::resetPassword( $input ) )
        {
            return Redirect::to('?view=user&task=login')
            ->with( 'notice', __halotext('Your password has been reset') );
        }
        else
        {
            return Redirect::to('?view=user&task=reset&uid='.$input['act_token'])
                ->withInput()
                ->with( 'error', __halotext('Password mismatch') );
        }
    }

    /**
     * Log the user out of the application.
     *
     */
    public function getLogout()
    {
        HALOUserModel::logout();

        return Redirect::to($this->getRedirectLogout());
    }

    /**
     * Get user's profile
     * @param $username
     * @return mixed
     */
    public function getProfile($userId)
    {
		$user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user))
        {
			return HALOError::abort();
        }
			
        return View::make('site/user/profile', compact('user'));
    }

    /**
     * Get user's profile
     * @param $username
     * @return mixed
     */
    public function getList(){
		
		$title = __halotext("View Users");

		$users = new HALOUserModel();
		
		$usersList = HALOPagination::getData($users);

		//init users
		$userIds = array();
		foreach($usersList as $user){
			$userIds[] = $user->id;
		}
		$cachedUsers = HALOModel::getCachedModel('user',$userIds);
		
		$usersListHtml = HALOUIBuilder::getInstance('','user.list',array('users'=>$cachedUsers))->fetch();		
		
        return View::make('site/user/list', compact('title','users','usersList', 'usersListHtml'));
		
	}
    /**
     * Get edit profile form
     * @param $username
     * @return mixed
     */
    public function getEdit($userId)
    {
		$user = HALOUserModel::getUser($userId);
		
        // Check if the user exists
        if (is_null($user))
        {
            return HALOError::abort();
        }
		
		//check permission
		if(!HALOAuth::can('user.edit',$user)){
			return Redirect::to('?app=site&view=user&task=profile&uid='.$userId)->with('error',__halotext('You are not allowed to edit this profile'));
		}
		
		$profileFields = $user->getProfileFields()->get();
        return View::make('site/user/edit', compact('user','profileFields'));
    }

    /**
     * submit edit profile form
     * @param $username
     * @return mixed
     */
    public function postEdit($userId)
    {
		$user = HALOUserModel::getUser($userId);
		
        // Check if the user exists
        if (is_null($user))
        {
            return HALOError::abort();
        }
		
		//check permission
		if(!$user->canEdit()){
			return HALOError::abort();
		}

        // Validate the inputs
		$rules = $user->getValidateRule();
		
		$profileFields = $user->getProfileFields()->get();

		foreach($profileFields as $field){
		
			$rules = array_merge($rules,$field->toHALOField()->getValidateValueRule());
		}
		
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->passes())
        {
			//rule: only display_name is editable
			if(($display_name = Input::get('display_name',null))){
				$user->user->setDisplayName($display_name);
			}
            // Save if valid. Password field will be hashed before save
            $user->user->amend();
			// Save system user
            $user->user->save();
			//save user profile fields
			$profile = $user->getProfile()->first();
			$profile->saveFieldValues($user,Input::get('field',array()),Input::get('access',array()));
            return Redirect::to('?view=user&task=edit&uid=' . $user->id)->with('success', __halotext('The user was edited successfully.'));
        } else {		
			//flash old input data
			Input::flash();

            return Redirect::to('?view=user&task=edit&uid=' . $user->id)->withErrors($validator);
		}
			
    }

	/**
	* Oauth login entry
	*
	* @return void
	*/

	public function getOauthLogin($consumer) {
		// get data from input
		$consumer = strtolower($consumer);
		$socialSettings = HALOUtilHelper::getSocialSettings();
		$oauthOptions = $socialSettings->getNsValue('social.'.$consumer.'.oauthOptions');
		if($oauthOptions && isset($oauthOptions->handler)){
			return call_user_func_array($oauthOptions->handler, array());
		} else {
			return Redirect::to('?view=user&task=login')->with('error', __halotext('Unknown OAuth Provider.'));
		}

	}
	
	/**
	* Facebook login
	*
	* @return void
	*/

	public static function getFacebookLogin() {

		// get data from input
		$code = Input::get( 'code' );

		// get fb service
		$fb = OAuthEx::consumer( 'Facebook' );

		// check if code is valid

		// if code is provided get user data and sign in
		if ( !empty( $code ) ) {
			try{
				// This was a callback request from facebook, get the token
				$token = $fb->requestAccessToken( $code );
			} catch(\Exception $e){
				return Redirect::to('?view=user&task=login')->with('error', __halotext('Login failure.'));
			}

			// Send a request with it
			$result = json_decode( $fb->request( '/me' ), true );

			//@todo: on invalid $result format checking
			
			$data = array();
			$data['uid'] = $result['id'];
			$data['consumer_id'] = HALO_OAUTH_CONSUMER_GG;
			$data['email'] = $result['email'];
			$data['name'] = $result['name'];
			if(!HALOOAuthAPI::login($data)){
				$message = HALOResponse::getMessage();
				return Redirect::to('?view=user&task=login')->with('error', __halotext('Login failure.'))
															->withErrors($message);
			}
			//redirect to the profile page
			$user = HALOResponse::getData('user');
			if($user){
				//redirect to user profile page
				return Redirect::to('?view=user&task=profile&uid='.$user->id)->with('success', __halotext('Login success'));				
			}

		}
		// if not ask for permission first
		else {
			$error = Input::get( 'error' );
			if(!empty($error)){
				//login fail or permission denied
				$errorMessage = Input::get('error_description');
				return Redirect::to('?view=user&task=login')->with('error', $errorMessage);
			}
			// get fb authorization
			$url = $fb->getAuthorizationUri();
			// return to facebook login url
			return Redirect::to( (string)$url );
		}

	}
	
	/**
	* Google login
	*
	* @return void
	*/
	public static function getGoogleLogin() {

		// get data from input
		$code = Input::get( 'code' );
		// get google service
		$googleService = OAuthEx::consumer( 'Google' );

		// check if code is valid

		// if code is provided get user data and sign in
		if ( !empty( $code ) ) {

			// This was a callback request from google, get the token
			try {
				$token = $googleService->requestAccessToken( $code );
			} catch(\Exception $e){
				return Redirect::to('?view=user&task=login')->with('error', __halotext('Login failure.'));
			}

			// Send a request with it
			$result = json_decode( $googleService->request( 'https://www.googleapis.com/oauth2/v1/userinfo' ), true );

			//@todo: on invalid $result format checking
			$data = array();
			$data['uid'] = $result['id'];
			$data['consumer_id'] = HALO_OAUTH_CONSUMER_GG;
			$data['email'] = $result['email'];
			$data['name'] = $result['name'];
			if(!HALOOAuthAPI::login($data)){
				$message = HALOResponse::getMessage();
				return Redirect::to('?view=user&task=login')->with('error', __halotext('Login failure.'))
															->withErrors($message);
			}
			//redirect to the profile page
			$user = HALOResponse::getData('user');
			if($user){
				//redirect to user profile page
				return Redirect::to('?view=user&task=profile&uid='.$user->id)->with('success', __halotext('Login success'));				
			}

		}
		// if not ask for permission first
		else {
			$error = Input::get( 'error' );			
			if(!empty($error)){
				//login fail or permission denied
				return Redirect::to('?view=user&task=login')->with('error', $error);
			}
			// get googleService authorization
			$url = $googleService->getAuthorizationUri();
			// return to facebook login url
			return Redirect::to( (string)$url );
		}
	}	
	
	/*
		ajax handler to delete user
	*/
	public function ajaxDeleteMe($postData){
		$my = HALOUserModel::getUser();
		if(!$my){
			HALOResponse::addMessage(HALOError::failed(__halotext('Login required.')));
			return HALOResponse::sendResponse();
		}

		$title = __halotext('Delete User');
		//define the deleting state
		if(!isset($postData['confirm'])){
			//warning
			$builder = HALOUIBuilder::getInstance('editProfile','form.form',array('name'=>'popupForm'))
						->addUI('confirm', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'confirm','value'=>'')));
			$content = 	$builder->fetch();
			HALOResponse::addScriptCall('halo.popup.reset')
					->addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setMessage', __halotext('Are you sure you want to delete your account? The account will be permanently deleted with no option for recovery') , 'error')
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', '{"name": "'.__halotext('Yes').'","onclick": "halo.user.deleteMe()","href": "javascript:void(0);"}')
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
					
			return HALOResponse::sendResponse();
		} else {
			if($postData['confirm'] == 'OK'){
				// process account delete
				if($my->delete()){
					$message = __halotext('Your account has been permanently deleted');
				} else {
					$message = __halotext('An error occur while deleting your account');
				}
				HALOResponse::addScriptCall('halo.popup.reset')
						->addScriptCall('halo.popup.setFormTitle', $title )
						->addScriptCall('halo.popup.setMessage', $message , 'error', true)
						->addScriptCall('halo.popup.addFormAction', '{"name": "'.__halotext('Done').'","onclick": "halo.util.redirect(\'' . URL::to('/') .'\')","href": "javascript:void(0);"}')
						->addScriptCall('halo.popup.addFormActionCancel')
						->addScriptCall('halo.popup.showForm' );
						
				return HALOResponse::sendResponse();
			} else {
				$builder = HALOUIBuilder::getInstance('editProfile','form.form',array('name'=>'popupForm'))
							->addUI('confirm', HALOUIBuilder::getInstance('','form.text',array('name'=>'confirm','title'=>__halotext('Type "OK" to confirm delete your account'),'placeholder'=>'','value'=>'')));
				$content = 	$builder->fetch();
				HALOResponse::addScriptCall('halo.popup.reset')
						->addScriptCall('halo.popup.setFormTitle', $title )
						->addScriptCall('halo.popup.setFormContent', $content )
						->addScriptCall('halo.popup.addFormAction', '{"name": "'.__halotext('Delete').'","onclick": "halo.user.deleteMe()","href": "javascript:void(0);"}')
						->addScriptCall('halo.popup.addFormActionCancel')
						->addScriptCall('halo.popup.showForm' );
						
				return HALOResponse::sendResponse();
			}
		}
		return HALOResponse::sendResponse();;

	}
	
    /**
     * ajax handler to update profile fields
     * @param  int $profileId
     * @return JSON
     */
    public function ajaxChangeProfileType($profileId)
    {
        //test post_profile_id
        if (empty($profileId) || !HALOProfileModel::isExists('user', $profileId)) {
            //update zone
            $error = HALOError::getMessageBag()->add('profile_id', __halotext('Unknow Profile'));
            HALOResponse::addMessage($error);
            return HALOResponse::sendResponse();
        }

        $user = new HALOUserModel();
        $user->profile_id = $profileId;
        $user->id = 0;//dummy user setting

        $profile = $user->getProfile();
        $profileFields = $user->getProfileFields()->get();
        //binding with old input data
        $user->bindData(Input::old());
        $profileFields->each(function ($field) {
            $field->toHALOField()->bindData(Input::old());
        });

        $builder = HALOUIBuilder::getInstance('', 'inline_profile_edit', array('zone' => 'user_profile_edit', 'profileFields' => $profileFields));

        //update zone
        HALOResponse::updateZone($builder->fetch());

        return HALOResponse::sendResponse();

    }
}
