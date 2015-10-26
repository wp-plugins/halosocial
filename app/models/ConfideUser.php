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
 
use Carbon\Carbon;
use Robbo\Presenter\PresentableInterface;
use Zizaco\Confide\Confide;
use Zizaco\Confide\ConfideEloquentRepository;
use Zizaco\Confide\ConfideUser;
use Zizaco\Entrust\HasRole;

class User extends ConfideUser implements PresentableInterface
{
    use HasRole;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $toggleable = array('block');

    // calculation parameters
    private $_params = null;
    private $_points = 0;
    private $_init = false;
    private $_isOnline = false;
    private $_followerCount = 0;

    /**
     * Get presenter
     * 
     * @return Robbo\Presenter\Presenter\UserPresenter
     */
    public function getPresenter()
    {
        return new UserPresenter($this);
    }

    /**
     * Get user by username
     * 
     * @param string  $username
     * @return string
     */
    public function getUserByUsername($username)
    {
        return $this->where('username', '=', $username)->first();
    }

    /**
     * Get the date the user was created.
     *
     * @return string
     */
    public function joined()
    {
        return String::date(Carbon::createFromFormat('Y-n-j G:i:s', $this->created_at));
    }

    /**
     * Save roles inputted from multiselect
     * 
     * @param object  $inputRoles
     */
    public function saveRoles($inputRoles)
    {
        if (!empty($inputRoles)) {
            $this->roles()->sync($inputRoles);
        } else {
            $this->roles()->detach();
        }
    }

    /**
     * Returns user's current role ids only.
     * 
     * @return array|bool
     */
    public function currentRoleIds()
    {
        $roles = $this->roles;
        $roleIds = false;
        if (!empty($roles)) {
            $roleIds = array();
            foreach ($roles as &$role) {
                $roleIds[] = $role->id;
            }
        }
        return $roleIds;
    }

    /**
     * Redirect after auth.
     * If ifValid is set to true it will redirect a logged in user.
     * 
     * @param object $redirect
     * @param bool $ifValid
     * @return array
     */
    public static function checkAuthAndRedirect($redirect, $ifValid = false)
    {
        // Get the user information
        $user = Auth::user();
        $redirectTo = false;

        if (empty($user->id) && !$ifValid)// Not logged in redirect, set session.
        {
            Session::put('loginRedirect', $redirect);
            $redirectTo = Redirect::to('user/login')
                ->with('notice', __halotext('user/user.login_first'));
        } elseif (!empty($user->id) && $ifValid)// Valid user, we want to redirect.
        {
            $redirectTo = Redirect::to($redirect);
        }

        return array($user, $redirectTo);
    }

    /**
     * CurrentUser
     * 
     * @return object
     */
    public function currentUser()
    {
        return (new Confide(new ConfideEloquentRepository()))->user();
    }

    /**
     * Get profile 
     * 
     * @return Illuminate\Database\Query\Builder
     */
    public function getProfile()
    {

        $query = $this->belongsTo('HALOProfileModel', 'profile_id', 'id');

        if ($query->count() == 0) {
            //fallback to default profile_id setting if doesn't exist
            $query = HALOProfileModel::where('id', '=', HALOConfig::get('user.defaultProfile'));
        }

        return $query;
    }

    /**
     * Get profile fields
     * 
     * @return object
     */
    public function getProfileFields()
    {
        $profile = $this->getProfile()->first();
        return $profile ? $profile->getFieldValues($this->id) : $this->getProfileFieldValues();
    }

    /**
     * Get profile field values 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function getProfileFieldValues()
    {
        return $this->belongsToMany('HALOFieldModel', 'halo_user_fields_values', 'user_id', 'field_id')
                    ->withPivot('value', 'access')
                    ->withTimestamps();
    }

    /**
     * Return HALOParam object applied for this User
     * 
     * @return HALOParam
     */
    public function getParams()
    {

    }

    /**
     * Inititalize the user User object
     * return true if the user is a new
     */
    public function init($initObj = null)
    {

    }

    /**
     * True if this user is following the given userid
     * 
     * @param int $id
     * @return  bool
     */
    public function isFollowing($id = null)
    {
        return true;
    }

    /**
     * Return current user status. Apply filter if needed
     * 
     * @return    string    user status
     */
    public function getStatus($rawFormat = false)
    {
        return $this->status;
    }

    /**
     * Return the the name for display, either username of name based on backend config
     */
    public function getDisplayName()
    {
        return $this->username;
    }

    /**
     * Return path to the user thumb image. If avatar is not set, return default thumb
     * 
     * @param string  $size the thumb size (optional)
     * @return HALOPhotoHelper path to the user thumb image
     */
    public function getThumb($size = 'd')
    {
        $defaultThumb = "images/user_thumb_default.png";
        //init with default path
        $path = $defaultThumb;
        if (!empty($this->thumb)) {
            $path = $this->thumb;
        }
        return HALOPhotoHelper::getPathWithSizePostfix($path, $size);
    }

    /**
     * Return path to the user avatar image. If avatar is not set, return default avatar
     * 
     * @param string            $size the avatar size (optional)
     * @return HALOPhotoHelper    path to the user avatar image
     */
    public function getAvatar($size = 'd')
    {
        $defaultAvatar = "images/user_avatar_default.png";
        //init with default path
        $path = $defaultAvatar;
        if (!empty($this->avatar)) {
            $path = $this->avatar;
        }
        return HALOPhotoHelper::getPathWithSizePostfix($path, $size);
    }

    /**
     * Return path to the user cover image. If cover is not set, return default cover
     * 
     * @param  string           $size the cover size (optional)
     * @return HALOPhotoHelper    path to the user cover image
     */
    public function getCover($size = 'd')
    {
        $defaultCover = "images/user_cover_default.png";
        //init with default path
        $path = $defaultCover;
        if (!empty($this->cover)) {
            $path = $this->cover;
        }
        return HALOPhotoHelper::getPathWithSizePostfix($path, $size);
    }

    /**
     * Check if this user data is the current login user
     * 
     * @return boolean true if yes, false if no
     */
    public function isMine()
    {
        return true;
    }

    /**
     * Check if current user liked this user
     * @return boolean true if yes, false if no
     */
    public function isLiked()
    {
        return true;
    }

    /**
     * Return total number post of this users
     * 
     * @return integer the total number post of this user
     */
    public function getPostsCount()
    {
        return 20;
    }

    /**
     * Return total followers of this users
     * 
     * @return integer the total follower of this user
     */
    public function getFollowersCount()
    {
        return 10;
    }

    /**
     * Return user point
     * 
     * @return integer user point
     */
    public function getUserPoint()
    {
        return 15;
    }

    /**
     * Return like count
     * 
     * @return integer like count
     */
    public function getLikeCount()
    {
        return 125;
    }

    /**
     * Return view count
     * 
     * @return integer view count
     */
    public function getViewCount()
    {
        return 1225;
    }

    // permision define here
    /**
     * 
     * @return object
     */
    public function canEdit()
    {
        return (Auth::check() && (Auth::user()->hasRole('admin') || $this->isMine()));
    }

}
