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

class UserModel extends HALOModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	protected $fillable = array('user_nicename','user_email','user_url','user_registered','user_activation_key','user_status','display_name');
	protected $primaryKey = 'ID';

	public $timestamps = false;
	
	public function getValidateRule(){
		return array('name' => 'required',
						'username' => 'required|unique:users,user_login',
						'email' => 'required|email|unique:users,user_email',
						'display_name' => 'required',
						'password'=>'required|confirmed');	
	
	}
	//////////////////////////////////// Define Relationships /////////////////////////
	/*
		UserModel, HALOUserModel: one to one
	*/
	public function halouser(){
		return $this->hasOne('HALOUserModel','user_id','ID');
	}
	//////////////////////////////////// .end Define Relationships ////////////////////
		
	/*
		return username of cms user
	*/
	public function getUserName(){
		return $this->user_login;
	}

	/*
		set username of cms user
	*/
	public function setUserName($val){
		$this->user_login = $val;
	}

	/*
		return email address of cms user
	*/
	public function getEmail(){
		return $this->user_email;
	}

	/*
		return display name of cms user
	*/
    public function getDisplayName() {
		return $this->display_name;
	}

    /**
     * Get first name of current user
     * 
     * @return string
     */
    public function getFirstName() {
        $currentUser = get_userdata($this->getId());
        return isset($currentUser->user_firstname) ? $currentUser->user_firstname : '';
    }

    /**
     * Get last name of current user
     * 
     * @return string
     */
    public function getLastName() {
        $currentUser = get_userdata($this->getId());
        return isset($currentUser->user_lastname) ? $currentUser->user_lastname : '';
    }
	
	/*
		set display name of cms user
	*/
    public function setDisplayName($val) {
		$this->display_name = $val;
	}
	
	/*
		return id of cms user
	*/
    public function getId() {
		return $this->ID;
	}	

	/*
		return user id of current cms user
	*/
    public static function getCurrentUserId() {
		static $current_user = null;
		if(defined('HALO_CRONTASK_DEFAULT_USER_ID')) {	//crontask mode, shortcut it
			return HALOConfig::get('contask.defaultUser', 1);
		}

		if(is_null($current_user)){
			$current_user = wp_get_current_user();
		}
		return $current_user->ID;
	}	
	
	/*
		return current cms user
	*/
    public static function getCurrentUser() {
		$user_id = self::getCurrentUserId();
		return UserModel::remember(5)->find($user_id);
	}	
	
	/*
		update cms user information
	*/
	
	public function amend(){
	    return true;
	}

	/*
		wp login action hook
	*/
	public static function wp_login_action($user_login, $user) {
		$blocked = (boolean)get_user_option( 'halo_blocked_user', $user->ID);
		$confirmed = get_user_option( 'halo_confirmed_user', $user->ID);
		
		if($confirmed === false) {
			//default is confirmed
			$confirmed = 1;
		}
		
		global $__haloUserFlag;
		$__haloUserFlag = 1;
		if($blocked) {
			wp_clear_auth_cookie();
			HALOResponse::addMessage(HALOError::failed(__halotext('Your account has been blocked. Please contact the administrator for more information.')));
		} else if(!$confirmed) {
			wp_clear_auth_cookie();
			HALOResponse::addMessage(HALOError::failed(sprintf(__halotext('Your account has not yet been activated. Please check your email to get the activation link or <a href="%s">Resend the activatation email</a>'), URL::to('?view=user&task=resend_code'))));				
		}
	}
	/*
		attempt login user
	*/
	public static function logAttempt($credential){
		
		$creds['user_login'] = $credential['username'];
		$creds['user_password'] = $credential['password'];
		$creds['remember'] = (boolean) $credential['remember'];
		//extra hook for login checking
		$model = new UserModel();
		add_action( 'wp_login', array($model,'wp_login_action'), 1, 2);
		
		$user = wp_signon( $creds, false );
		$message = HALOResponse::getMessage();

		if($message && !$message->isEmpty()){
			return false;		
		}
		if ( is_wp_error($user) ) {
			HALOResponse::addMessage(HALOError::failed($user->get_error_message()));
			return false;
		}
        $haloUser = HALOUserModel::getUser($user->ID);
		Event::fire('user.onLogin',array($haloUser));
		
		return $haloUser->id ? $haloUser : false;
	}

	/*
		verify password for this user
	*/
	public function verifyPassword($password){
		return wp_check_password($password, $this->user_pass);
	}
	
	/*
		login with current user
	*/
	public function login(){
		wp_set_current_user( $this->getId(), $this->getUserName() );
		wp_set_auth_cookie( $this->getId() );
		do_action( 'wp_login', $this->getUserName(), $this );		
		
		Event::fire('user.onLogin',array(HALOUserModel::getUser($this->getId())));
		return true;
	}
	
	/*
		logout current user
	*/
	public static function logout(){
		
		Event::fire('user.onLogout',array(HALOUserModel::getUser()));
		wp_logout();
	}
	
	/*
		create New user from data
	*/
	public static function createNew($data){
	
		if(!isset($data['username']) || !isset($data['email'])){
			HALOResponse::addMessage(HALOError::failed(__halotext('Invalid input data')));
			return false;
		}
		$user_email= $data['email'];
		$user_name = $data['username'];

		$user_id = username_exists( $user_name );

		if ( !$user_id and email_exists($user_email) == false ) {
			if(isset($data['password']) && !empty($data['password'])) {
				$password = $data['password'];
			} else {
				//oauth user, create random password
				$password = wp_generate_password( $length=12, $include_standard_special_chars=false );			
			}
			$user_id = wp_create_user( $user_name, $password, $user_email );
		} else {
			$msg = $user_id?__halotext('Sorry, that Username is already taken'):'';
			$msg .= email_exists($user_email)?__halotext('Email is not available'):'';
			HALOResponse::addMessage(HALOError::failed(__halotext('Could not create new user. ') . $msg));
			return false;
		}
		
		//update display name
		if(isset($data['name']) && $data['name']) {
			wp_update_user(array('ID' => $user_id, 'display_name' => $data['name'], 'user_nicename' => $data['name']));
		}
	
		$user = UserModel::find($user_id);
		//clean cache
		Cache::forget('halo_sync_user');
		return $user;
	}

	/*
		function to change user's password
	*/
	public function changePassword($data){
		$rules = $this->getValidateRule();
		$validator = Validator::make(
			$data,
			array('password'=>$rules['password'])
		);
		if($validator->fails()){
			$error = $validator->messages();
			HALOResponse::addMessage($error);
			return false;
		} else {
			$this->user_pass = wp_hash_password($data['password']);
			// wp_set_password($data['password'], $this->getId());
			if(!$this->save()){
				HALOResponse::addMessage(HALOError::failed(__halotext('Could not save user data')));
				return false;
			}
			HALOResponse::addMessage(HALOError::passed(__halotext('Your password has been changed')));
			$this->login();
			wp_signon(array('user_login' => $this->getUserName(), 'user_password' => $data['password']));			
			// HALOResponse::refresh();
			//trigger event
			Event::fire('user.onChangedPassword',array($this->halouser));
		}
		return true;
	}

	/*
		reset password
	*/
	public static function resetPassword($input){
		return Confide::resetPassword($input);
	}
	
	/*
		return admin user Ids
	*/
	public static function getAdminIds(){
		return get_users(array('role'=>'administrator','fields'=>'ID'));
	}
	
	/*
		return mod user Ids
	*/
	public static function getModIds(){
		return get_users(array('role'=>'administrator','fields'=>'ID'));
	}

	/*
		join a HALOUserModel with UserModel
	*/
	public static function joinUserTable($userModel){
		return $userModel->leftJoin('users', 'halo_users.id', '=', 'users.ID');
	}

	/*
		find user by email
	*/
	public static function getUserByEmail($email){
		$user = UserModel::where('user_email','=',$email)
						->get()->first();
		return $user;
	}	
    /**
     * Get user by username
     * 
     * @param string  $username
     * @return string
     */
    public static function getUserByUsername($username)
    {
        return UserModel::where('user_login', '=', $username)->first();
    }

	/*
		send email to reset password
	*/
	public static function forgotPassword($email){
		//return Confide::forgotPassword($email);
	}
	
	public static function getRegisterPage(){
		return Redirect::to(site_url('/wp-login.php?action=register&redirect_to=' . get_permalink()));
	}
	
	public static function canRegister() {
		return get_option('users_can_register');
	}
	
	public static function getRegisterLink() {
		if(HALOConfig::get('user.haloregistration', 1)) {
			return URL::to('?view=user&task=register');
		} else {
			return site_url('/wp-login.php?action=register&redirect_to=' . get_permalink());
		}
	}

    /**
     * Returns the URL that allows the user to retrieve the lost password
     *
     * @return string
     */
    public static function getForgotLink()
    {
        return wp_lostpassword_url(get_permalink());
    }
	
	/*
		function to block a user
	*/
	public function blockUser() {
		$this->setBlock(1);
	}
	
	/*
		function to unblock a user
	*/
	public function unblockUser() {
		$this->setBlock(0);
	}
	
	/*
		function to set block option
	*/
	public function setBlock($val) {
		update_user_option( $this->getId(), 'halo_blocked_user', $val );
		return $val;
	}
	/*
		check if this user is blocked
	*/
	public function isBlocked() {
		$blocked = get_user_option('halo_blocked_user', $this->getId());
		return (boolean) $blocked;
	}

	/* 
		return account confirm state
	*/
	public function getConfirmState(){
		$confirmed = get_user_option('halo_confirmed_user', $this->getId());
		if($confirmed === false) {
			//default is confirmed
			$confirmed = 1;
		}
		return $confirmed;
	}

	/* 
		set account confirm state
	*/
	public function setConfirmState($val){
		update_user_option( $this->getId(), 'halo_confirmed_user', $val );
		return $val;
	}
	
	public function generateConfirmCode() {
		$random_hash = substr(md5(uniqid(rand(), true)), 16, 16); // 16 characters long
		
		update_user_option( $this->getId(), 'halo_user_confirm_code', $random_hash );
	}
	
	public function getConfirmCode() {
		return get_user_option('halo_user_confirm_code', $this->getId());
	}
	
	public function getConfirmationUrl() {
		return URL::to('?view=user&task=activate&act_token=' . $this->getConfirmCode(). '&uid=' . $this->getId());
	}
	
	public function sendConfirmationEmail() {
		$this->generateConfirmCode();
        $this->setConfirmState(0);
		$data = array('site'  => bloginfo('name'), 'url' => $this->getConfirmationUrl(), 'username' => $this->getUsername());
		$user = $this;
		Mail::send("emails.activate",$data,function($message) use ($user){
			$message->to($user->getEmail(), 'halo.social')->subject(__halotext('Welcome!'));
		});	
	}
	
	public function sendWelcomeEmail() {
		$data = array('name'  => $this->getUsername());
		$user = $this;
		Mail::send("emails.welcome",$data,function($message) use ($user){
			$message->to($user->getEmail(), 'halo.social')->subject(__halotext('Welcome!'));
		});
	}
	
	public function changeAvatar() {
		delete_user_option($this->getId(), 'halo_user_avatar_urls');
	}
}
