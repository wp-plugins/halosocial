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

class HALOUserModel extends HALOModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'halo_users';

    // calculation parameters
    
    public static $resourceCbs = array();
    public $timestamps = true;
    protected static $instances = array();
    protected $toggleable = array('block', 'confirmed', 'user_role');
    protected $sluggable = array(
        'build_from' => 'display_name',
        'save_to' => 'slug',
    );
    protected $bookmarkIds = null;
    private $_params = null;
    private $_points = 0;
    private $_init = false;
    private $_isOnline = false;
    private $_followerCount = 0;

    private $_notifSettings = null;

    private $_rejectedIds = null;

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * UserModel, HALOUserModel: one to one
     *  
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('UserModel', 'user_id', HALO_USER_ID_COL);
    }

    /**
     * HALOUserModel, HALOPhotoModel: one to one (avatar)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function avatar()
    {
        return $this->belongsTo('HALOPhotoModel', 'avatar_id');
    }

    /**
     * HALOUserModel, HALOPhotoModel: one to one (cover)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function cover()
    {
        return $this->belongsTo('HALOPhotoModel', 'cover_id');
    }

    /**
     * HALOProfileModel, HALOUserModel: one to one
     * 
     * @return Illuminate\Database\Query\Builder
     */
    public function getProfile()
    {

        $query = $this->belongsTo('HALOProfileModel', 'profile_id', 'id');

        if (empty($this->profile_id) || $query->count() == 0) {
            //fallback to default profile_id setting if doesn't exist
            $query = HALOProfileModel::where('id', '=', HALOProfileModel::getDefaultProfileId('user'));
        }

        return $query;
    }

    /**
     * HALOFieldModel, HALOUserModel: many to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function getProfileFieldValues()
    {
        return $this->belongsToMany('HALOFieldModel', 'halo_user_fields_values', 'user_id', 'field_id')
                    ->withPivot('value', 'access', 'params')
                    ->withTimestamps();
    }

    /**
     * HALOProfileModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    /**
     * The bookmark relationship
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function bookmarks()
    {
        return $this->hasMany('HALOBookmarkModel', 'user_id');
    }

    /**
     * HALOProfileModel, HALOFollowerModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function followers()
    {
        return $this->morphMany('HALOFollowerModel', 'followable');
    }

    /**
     * HALOProfileModel, HALOFollowerModel: polymorphic, list of users that this user is following
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function following()
    {
        return $this->hasMany('HALOFollowerModel', 'follower_id')->where('followable_type', 'HALOUserModel');
    }

    /**
     * HALOUserModel, HALOTagModel: polymorphic one to one
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphOne
     */
    public function tag()
    {
        return $this->morphOne('HALOTagModel', 'taggable');
    }

    /**
     * HALOCommentModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function comments()
    {
        return $this->hasMany('HALOCommentModel', 'actor_id');
    }

    /**
     * HALOActivityModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function activities()
    {
        return $this->hasMany('HALOActivityModel', 'actor_id');
    }

    /**
     * HALONotificationreceiverModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function notifications()
    {
        return $this->hasMany('HALONotificationreceiverModel', 'user_id');
    }

    /**
     * HALOConvattenderModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function conversations()
    {
        return $this->hasMany('HALOConvattenderModel', 'attender_id');
    }

    /**
     * HALOPhotoModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function photos()
    {
        return $this->hasMany('HALOPhotoModel', 'owner_id')->where('halo_photos.status', '>', 0);
    }

    /**
     * HALOPhotoModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Query\Builder
     */
    public function photosCount()
    {
        return $this->photos()->where('halo_photos.status', '>', 0)
                    ->selectRaw('owner_id, count(*) as count')->groupBy('owner_id');
    }

    /**
     * HALOAlbumModel, HALOUserModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function albums()
    {
        return $this->hasMany('HALOAlbumModel', 'owner_id');
    }

    /**
     * HALOVideoModel, HALOUserModel: one to many
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function videos()
    {
        return $this->hasMany('HALOVideoModel', 'owner_id');
    }

    /**
     * HALOUserModel, HALOUserModel: many to many - friends
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function friends()
    {
        return $this->belongsToMany('HALOUserModel', 'halo_connections', 'from_id', 'to_id')
                    ->where('role', '=', HALO_CONNECTION_ROLE_FRIEND)
                    ->where('status', '=', HALO_CONNECTION_STATUS_CONNECTED)
                    ->withPivot('params')
                    ->withTimestamps();
    }

    /**
     * HALOUserModel, HALOUserModel: many to many - friends, list users sent friend request from this user
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function friendRequests()
    {
        return $this->belongsToMany('HALOUserModel', 'halo_connections', 'from_id', 'to_id')
                    ->where('role', '=', HALO_CONNECTION_ROLE_FRIEND)
                    ->where('status', '=', HALO_CONNECTION_STATUS_REQUESTING)
                    ->withPivot('params')
                    ->withTimestamps();
    }

    /**
     * HALOUserModel, HALOUserModel: many to many - friends, list users sent friend request to this user
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function pendingFriendRequests()
    {
        //get reject or block user id
        if (is_null($this->_rejectedIds)) {
            $rejectedIds = $this->rejectedFriendRequests->lists('user_id');
            if (empty($rejectedIds)) {
                $rejectedIds = array(-1);// never matched list
            }
            $this->_rejectedIds = $rejectedIds;
        }
        return $this->belongsToMany('HALOUserModel', 'halo_connections', 'to_id', 'from_id')
                    ->where('role', '=', HALO_CONNECTION_ROLE_FRIEND)
                    ->where('status', '=', HALO_CONNECTION_STATUS_REQUESTING)
                    ->whereNotIn('from_id', $this->_rejectedIds)
            ->withPivot('params')
            ->withTimestamps();
    }
       
    /**
     * HALOUserModel, HALOUserModel: many to many - friends, list users that this user rejected friend request
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function rejectedFriendRequests()
    {
        return $this->belongsToMany('HALOUserModel', 'halo_connections', 'from_id', 'to_id')
                    ->where('role', '=', HALO_CONNECTION_ROLE_FRIEND)
                    ->where('status', '=', HALO_CONNECTION_STATUS_REJECTED)
                    ->withPivot('params')
                    ->withTimestamps();
    }

    /**
     * HALOPhotoModel, HALOUserModel: one to many
     * 
     * @return int
     */
    public function friendsCount()
    {
        return $this->getRelationCounter('friends');
    }

    /**
     * HALOUserModel, HALOUserModel: many to many - connections
     * 
     * @param  string $role   
     * @param  string $status 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany         
     */
    public function connections($role = null, $status = null)
    {
        $query = $this->belongsToMany('HALOUserModel', 'halo_connections', 'from_id', 'to_id');
        if (!is_null($role)) {
            $query = $query->where('role', '=', $role);
        }
        if (!is_null($status)) {
            $query = $query->where('status', '=', $status);

        }
        return $query->withPivot('status', 'role', 'params')
                     ->withTimestamps();
    }

    /**
     * HALOUserModel, HALOUseroauth: one to one - oauth
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function oauth()
    {
        return $this->hasOne('HALOUseroauthModel', 'user_id');
    }

    /**
     * HALOUserModel, HALOLabelModle: poly many to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphToMany
     */
    public function labels()
    {
        return $this->morphToMany('HALOLabelModel', 'labelable', 'halo_labelables', 'labelable_id', 'label_id');
    }

    /**
     * HALOLocationModel, HALOUserModel: one to one
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function location()
    {
        return $this->belongsTo('HALOLocationModel', 'location_id');
    }

    /**
     * HALOPostModel, HALOReportModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphToMany
     */
    public function reports()
    {
        return $this->morphMany('HALOReportModel', 'reportable');
    }

    /**
     * HALOUserModel, HALOPostModel: one to one
     * 
     * @return HALOPostModel
     */
    public function owner() 
    {   
        return $this->belongsTo('HALOUserModel', 'id', 'id');
    }
        //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Sync User and HALOUser, if HALOUser record doesn't exists and User exists, init HALOUser with default values
     * 
     * @return true
     */
    public static function sync()
    {
            //@todo: target for caching
        return Cache::remember('halo_sync_user', 5, function () {
                //need to sync users table to halo_users table.
            $t = new UserModel();
            $newUsers = UserModel::leftjoin('halo_users', HALO_USER_TABLE . '.' . HALO_USER_ID_COL, '=', 'halo_users.user_id')
                    ->whereNull('halo_users.id')
            ->select(HALO_USER_TABLE . '.' . HALO_USER_ID_COL)->lists(HALO_USER_ID_COL);
            if (!empty($newUsers)) {
                    //create new halo_users record for these users
                $records = array();
                foreach ($newUsers as $newUser) {
                    $records[] = array('user_id' => $newUser, 'id' => $newUser, 'created_at' => new DateTime, 'updated_at' => new DateTime);    //add additional default value
                }
                HALOUserModel::insert($records);
            }

            return true;
        });
    }

        /**
     * Init a list of users and store to the static class cache
     * 
     * @return array    if the user is a new
     */
    public static function init(array $Ids, $cachable = true)
    {
            //we will store loaded user to cache to reduce the number of query
        if (!empty(self::$instances) && $cachable) {
            $loadedIds = array_keys(self::$instances);
            $newIds = array_diff($Ids, $loadedIds);
        } else {
            $newIds = $Ids;
        }

            //if $newIds is empty, do not need to load from database, get from class cache
        if (!empty($newIds)) {
                //sync data first
            self::sync();
            $users = HALOUserModel::with('user', 'avatar', 'tag', 'oauth')
                    ->whereIn('user_id', $newIds)        ->get();
                    //load additional data here
                    //$users->load('user','avatar','cover');
                    //add to the cache
            foreach ($users as $user) {
                self::$instances[$user->user_id] = $user;
            }
        }
        $users = array_intersect_key(self::$instances, array_flip($Ids));
                //make sure the output array order is as the same as the input, otherwise sorting function will not work
        $arr = array();
        foreach ($Ids as $id) {
            if (isset($users[$id])) {
                $arr[$id] = $users[$id];
            }
        }

        return $arr;
    }
       
    /**
     * Function to cache an ussers ollection/array to class cache
     * 
     * @param  string $collection  
     * @param  array $relationAr
     */
    public static function cacheUsers($collection, $relationArr)
    {
                //check for valid input parameter
        if (!is_a($collection, 'Illuminate\Database\Eloquent\Collection')) {
            if (is_a($collection, 'Illuminate\Database\Eloquent\Model')) {
                        //for single model, convert it to array
                $collection = array($collection);
            } else {
                        //invalid input, just skip it
                return;
            }
        }

        $relations = array();
        $relationArr = (array) $relationArr;
        foreach ($relationArr as $relationStr) {
            $relationStr = trim($relationStr);
            if (!empty($relationStr)) {
                $relationLevels = explode('.', $relationStr);
                $relations[] = $relationLevels;
            }
        }
        $obj = null;
        foreach ($relations as $relation) {
            foreach ($collection as $item) {
                        //foreach item, access to the leaf relation and check for existing
                $obj = $item;
                $level = $relation[0];
                if ($level) {
                            //if the leaf relation and is a HALOUserModel instance
                    $obj = $obj->$level;
                    if (!isset($relation[1])) {
                        if (is_a($obj, 'HALOUserModel')) {
                            if (!isset(self::$instances[$obj->user_id])) {
                                self::$instances[$obj->user_id] = $obj;
                            }
                        }
                    } else {
                                //continue access to the leaf relation
                        $subRelation = implode('.', array_slice($relation, 1));
                        HALOUserModel::cacheUsers($obj, $subRelation);
                    }
                }
            }
        }
    }

    /**
     * Get a halouser instance
     * 
     * @param  int $userId
     * @return bool        
     */
    public static function getUser($userId = null)
    {
        /* rule:  $userId === null -> get the current login user
        $userId === 0
        $userId is interger --> get HaloUser with the Id
         */

        if ($userId === null) {
                    //get the current login user Id
            $userId = UserModel::getCurrentUserId();
        }
                //do not init public user
        if (empty($userId)) {
            return null;
        }
		
		if(is_a($userId, 'HALOUserModel')) {
			$userId = $userId->id;
		}

        $user = self::init((array) $userId);        //always return as array
        if (isset($user[$userId])) {
            return $user[$userId];
        }

        return null;
    }

    /**
     * Get current UserId 
     * 
     * @return UserModel
     */
    public static function getCurrentUserId()
    {
        global $app;
        if (isset($app['api_user_session'])) {
            $session = UserSession::where('token', '=', $app['api_user_session'])->first();

            if (isset($session)) {
                return $session->getUser()->first()->id;
            }
            return null;
        }

        return UserModel::getCurrentUserId();
    }

    /**
     * Return a list of users by using input options
     * 
     * @param  array  $options 
     * @return array         
     */
    public static function queryUsers($options = array())
    {
        $default = array('limit' => HALO_USER_LIMIT_DISPLAY,
            'orderBy' => 'id',
            'orderDir' => 'asc',
            'filters' => array()
        );
        $options = array_merge($default, $options);
        $query = HALOUserModel::orderBy($options['orderBy'], $options['orderDir'])
                    ->take($options['limit']);
		if(isset($options['avatarOnly']) && $options['avatarOnly']) {
			$query->whereNotNull('avatar_id');
		}
                    //apply filters
        $query = HALOFilter::applyFilters($query, $options['filters']);

        $userIds = $query->lists('id');

        return self::init($userIds);

    }

    /**
     * Get validation rules for this table
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('username' => 'required', 'email' => 'required');

    }

    /**
     * Get a list of users that readly for autocomlete
     * 
     * @param  string $term         
     * @param  array  $filterValues 
     * @return array
     */
    public static function getSearch($term, $filterValues)
    {
        if (HALOConfig::get('user.suggestFriendsOnly') && !HALOAuth::hasRole('admin')) {
            $query = HALOUserModel::getUser();
            if (!$query) {
                return array();
            }
            $query = $query->friends();
        } else {
            $query = new HALOUserModel();
        }
        self::applyNameSearchApplyFilter($query, $term, null);

                    //process filter settings
        if (is_array($filterValues)) {
            foreach ($filterValues as $key => $value) {
                if ($filterValues[$key] == '') {
                    unset($filterValues[$key]);
                }
            }
            if (!empty($filterValues)) {
                $filters = HALOFilter::getFilterByIds(array_keys($filterValues));
                foreach ($filters as $key => $filter) {
                    $filter->applyFilter($query, $filterValues[$filter->id]);
                }
            }
        }

        $users = $query->get();

                //for performance risk, we load all the users by using init function
        $user_ids = array();

        foreach ($users as $user) {
            $user_ids[] = $user->id;
        }

        HALOUserModel::init($user_ids);

        $list = array();
                //prepare user data
        foreach ($user_ids as $user_id) {
            $user = HALOUserModel::getUser($user_id);
            if (!is_null($user)) {
                $obj = new stdClass();
                $obj->image = $user->getAvatar(HALO_PHOTO_LIST_SIZE, HALO_PHOTO_LIST_SIZE);
                $obj->name = $user->getDisplayName();
                $obj->id = $user->user_id;
                $list[] = $obj;
            }
        }
        return $list;
    }

    /**
     * Get profile  fields 
     * 
     * @return object
     */
    public function getProfileFields()
    {
        $profile = $this->getProfile()->first();
        return $profile ? $profile->getFieldValues($this->id) : $this->getProfileFieldValues();
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
     *
     * @return string
     */
    public function getDisplayName()
    {
		$my = HALOUserModel::getUser();
		if($my && $my->id == $this->id) {
			//return __halotext('You');
		}
		//make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        if ($user->user) {
            return $user->user->getDisplayName();
        } else {
			//user mapping is not existing. This is an invalid case so we silently delete this user
			$user->delete();
            return '';
        }
    }

    /**
     * Get title 
     * 
     * @return HALOOutputHelper
     */
    public function getTitle()
    {
        return sprintf(__halotext('%s Profile'),$this->getDisplayName());
    }
	
    /**
     * Return meta tag value of this object
     * 
     * @param  string $type meta tag type
     * @return string meta tag value
     */
	public function getMetaTags($type){
		$that = $this;
		return $this->localCache($type,function () use ($that, $type){
			$meta = '';
			//for searching page meta tags
			$metaTags = HALOFilter::getFilterMetaTags($type);
			if(!empty($metaTags)){
				//add prefix and postfix meta tags
				//prefix
				if(in_array($type, array('title', 'ogtitle', 'description', 'ogdescription'))){
					$prefix = HALOOutputHelper::getSearchMetaPrefix();
					if($prefix){
						$metaTags[-1] = $prefix;
					}
				}
				//postfix
				if(in_array($type, array('title'))){
					if($postfix = HALOOutputHelper::getSearchMetaPostfix()){
						$metaTags[10000] = $postfix;
					}
				}
				//owner 
				if(in_array($type, array('description', 'ogdescription'))){
					$metaTags[] = sprintf(__halotext("created by '%s'"), $that->getDisplayName());
				}
				ksort($metaTags);
				return trim(strip_tags(implode(' ', $metaTags)));
			}
			switch($type){
				case 'title':
				case 'ogtitle':
					$titles =  array($that->getTitle());
					$meta = implode(' | ', $titles);
					break;
				case 'cover':
					$meta =  $that->getCover(HALO_OG_IMAGE_WIDTH, HALO_OG_IMAGE_RATIO);
					break;
				case 'description':
				case 'ogdescription':
					$descriptions = array(sprintf(__halotext('Registered date: %s'), $that->created_at->toDateString()));
					$descriptions[] = __halotext('Total posts: %s', $that->getRelationCounter('posts'));
					$descriptions[] = __halotext('Total shops: %s', $that->getRelationCounter('shops'));
					$descriptions[] = sprintf(__halotext('Join %s now to get connected with %s'), HALOAssetHelper::getPageTitle(), $that->getDisplayName());
					$meta = implode('.', $descriptions);
					$meta = trim(strip_tags($meta));
					break;
				case 'keywords':
					//for now, use title as keywords
					$meta = $that->getMetaTags('title');
					//tokenize $meta
					$meta = HALOUtilHelper::tokenize($meta,',');
					break;
				default:
					
			}
			return $meta;
		});
	}
	
    /**
     * Return the the name with link for display, either username of name based on backend config
     *
     * @return string
     */
    public function getDisplayLink($class = '', $brief = true)
    {
		$my = HALOUserModel::getUser();
		if($my && $my->id == $this->id) {
			//return __halotext('You');
		}
        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . ($brief ? $this->getBriefDataAttribute() : '') . 'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Get UserName 
     * 
     * @return string
     */
    public function getUserName()
    {
            //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        if ($user->user) {
            return $user->user->getUserName();
        } else {
            return '';
        }
    }

    /**
     * Get Url 
     * 
     * @param  array  $params 
     * @return string
     */
    public function getUrl(array $params = array())
    {
        if (!empty($this->user_id)) {
            $pString = '';
            if (!empty($params)) {
                $pString = '&' . http_build_query($params);
            }
            return URL::to('?view=user&task=profile&uid=' . $this->user_id . $this->getSlugParam() . $pString);
        }
        return '';
    }

    /**
     * Get email 
     * 
     * @return stirng
     */
    public function getEmail()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        if ($user->user) {
            return $user->user->getEmail();
        } else {
            return '';
        }
    }

    /**
     * Return path to the user thumb image. If avatar is not set, return default thumb
     * 
     * @param string  $size the thumb size (optional)
     * @return string path to the user thumb image
     */
    public function getThumb($size = HALO_PHOTO_THUMB_SIZE)
    {
        return $this->getAvatar($size);
    }

    /**
     * Return path to the user avatar image. If avatar is not set, return default avatar
     * 
     * @param string  $size the avatar size (optional)
     * @return HALOPhotoHelper path to the user avatar image
     */
    public function getAvatar($size = HALO_PHOTO_AVATAR_SIZE)
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        //@rule: online status is displayed with user's avatar or thumb, so whenever getting avatar/thumb, queue this user for online checking
        if (HALOConfig::get('global.showOnline', 1)) {
            HALOOnlineuserModel::checkOnline($this);
        }

        if ($user->avatar_id && $user->avatar) {
            $path = $user->avatar->getPhotoURL();
            return HALOPhotoHelper::getCropPhotoURL($path, $size, $size, $user->getParams('avatarZoom', 100), $user->getParams('avatarTop', 0), $user->getParams('avatarLeft', 0), $user->getParams('avatarWidth', 0));
        } else {
            //check for social avatar
            if (!is_null($fb_uid = $this->getParams('fb_uid', null))) {
                return 'https://graph.facebook.com/' . $fb_uid . '/picture?height=' . $size . '&width=' . $size;
            } else {
                if ($this->oauth) {
                    if ($this->oauth->consumer_id == HALO_OAUTH_CONSUMER_FB) {
                        //fb user
                        $this->setParams('fb_uid', $this->oauth->uid);
                        $this->save();
                        return 'https://graph.facebook.com/' . $this->oauth->uid . '/picture?height=' . $size . '&width=' . $size;
                    } else {
                        //try to get gravatar
                        $hash = md5(strtolower(trim($this->getEmail())));
                        return 'http://www.gravatar.com/avatar/' . $hash . '?s=' . $size;
                    }
                }
            }
            //return blank avatar
            $path = HALOPhotoHelper::getDefaultImagePath('avatar', $this);
            return HALOPhotoHelper::getResizePhotoURL($path, $size, $size);
        }
    }

    /**
     * Return path to the user cover image. If cover is not set, return default cover
     * 
     * @param   string $size the cover size (optional)
     * @return  HALOPhotoHelper  path to the user cover image
     */
    public function getCover($size = HALO_PHOTO_COVER_SIZE, $ratio = 0.375)
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        if ($user->cover_id && $user->cover) {
            $path = $user->cover->getPhotoURL();
            return HALOPhotoHelper::getCropPhotoURL($path, $size, round($size * $ratio), $user->getParams('coverZoom', 100), $user->getParams('coverTop', 0), $user->getParams('coverLeft', 0), $this->getParams('coverWidth', 0));
        } else {
            $path = HALOPhotoHelper::getDefaultImagePath('cover', $this);
            return HALOPhotoHelper::getResizePhotoURL($path, $size, round($size * $ratio));
        }
    }

    /**
     * Check if this user data is the current login user
     * 
     * @return boolean true if yes, false if no
     */
    public function isMine()
    {
        $my = HALOUserModel::getUser();
        return ($my && $my->id == $this->id);
    }

    /**
     * Check if current user liked this user
     * 
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
        return HALOPostModel::where('creator_id', $this->user_id)->count();
    }

    /**
     * Return total followers of this users
     * 
     * @return integer the total follower of this user
     */
    public function getFollowersCount()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        return count($user->followers);
    }

    /**
     * Return array of friends for this users
     *
     * @return HALOUserModel
     */
    public function getFriends()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        return $user->friends;
    }

    /**
     * Return friends ids of this user
     * 
     * @return array
     */
    public function getFriendIds()
    {
        $ids = array();
        foreach ($this->getFriends() as $friend) {
            $ids[] = $friend->user_id;
        }
        return $ids;
    }

    /**
     * Return array of followers for this user
     * 
     * @return  HALOUserModel
     */
    public function getFollowers()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        return $user->followers;
    }


    /**
     * Return follower ids of this user 
     * 
     * @return array
     */
    public function getFollowerIds()
    {
        $ids = array();
        foreach ($this->getFollowers() as $follower) {
            $ids[] = $follower->follower_id;
        }
        return $ids;
    }

    /**
     * Return array dof users that is followed by this user
     * 
     * @return HALOUserModel
     */
    public function getFollowing()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        return $user->following;
    }

    /**
     * Return following ids 
     * 
     * @return array
     */
    public function getFollowingIds()
    {
        $ids = array();
        foreach ($this->getFollowing() as $following) {
            $ids[] = $following->followable_id;
        }
        return $ids;
    }
    /**
     * Return whether user is online
     *
     * @return bool
     */
    public function isOnline()
    {

        return (boolean) rand(0, 1);
    }

    /**
     * Return user point
     * 
     * @return integer user point
     */
    public function getUserPoint()
    {
        return (int) $this->point_count;
    }

    /**
     * Return view count
     * 
     * @return integer view count
     * @override the HALOModel getViewCount to update the view of other's profile only
     */
    public function getViewCount($update = false)
    {
        if ($this->isMine()) {
            return HALOViewcounterModel::getCounter($this, false);
        } else {
            return HALOViewcounterModel::getCounter($this, $update);
        }
    }

    // permision define here
    /**
     * 
     * @return HALOAuth
     */
    public function canEdit()
    {
        return HALOAuth::can('user.edit', $this);
    }

    /**
     * canViewBehalfPost check permission to view post on behalf posts of different user
     *
     * @param  HALOUserModel $user
     * @return bool
     * @author Phuong Ngo <phuongngo@halo.vn>
     */
    public function canViewBehalfPost($user)
    {
        return true;
    }

    /**
     * Check if user can view on behalf post
     * 
     * @param  string $user 
     * @return bool
     */
    public function canViewOnBehalfPost($user)
    {
        if ($user && HALOAuth::can('post.delegate') && $this->id = $user->id) {
            return true;
        }
        if (HALOAuth::can('view.backend')) {

        }
        return false;
    }


    /**
     * Function to reset  the promote cache action
     * 
     * @return int
     */
    public function addedPromote()
    {
        Cache::forget('user.promoteLimit.' . $this->id);
    }

    /**
     * Function to check if user bookmared a post
     * 
     * @param  int  $postId 
     * @return boolean         
     */
    public function isBookmarked($postId)
    {
        $curUser = HALOUserModel::getUser();
        if (is_null($this->bookmarkIds)) {
            $this->bookmarkIds = $this->bookmarks->lists('post_id');
            $this->bookmarkIds = is_null($this->bookmarkIds) ? array() : $this->bookmarkIds;
        }
        return in_array($postId, $this->bookmarkIds);
    }

    /**
     * Login with this user
     * 
     * @return object
     */
    public function login()
    {
        return $this->user->login();
    }

    /**
     * Attempt login user
     * 
     * @param  string $credential
     * @return UserModel
     */
    public static function logAttempt($credential)
    {
        return UserModel::logAttempt($credential);
    }

    /**
     * Verify password for this user
     * 
     * @param  string $password 
     * @return string           
     */
    public function verifyPassword($password)
    {
        return $this->user->verifyPassword($password);
    }

    /**
     * Logout the current user
     * 
     * @return UserModel
     */
    public static function logout()
    {
        UserModel::logout();
    }

    /**
     * Logout the current user
     * 
     * @return bool
     */
    public function forceLogout()
    {
        $this->user->forceLogout();
    }

    /**
     * Return default album id for this
     * 
     * @return int
     */
    public function getDefaultAlbumId()
    {
        return $this->getDefaultAlbum()->id;
    }

    /**
     * Return default album for this user
     * 
     * @return HALOAlbumModel
     */
    public function getDefaultAlbum()
    {
        $album_id = $this->getParams('defaultAlbumId', 0);
        if ($album_id) {
            $album = HALOAlbumModel::find($album_id);
        } else {
            //create new album
            $album = new HALOAlbumModel();
            $album->name = $this->getDisplayName() . '\'s Album';
            $album->published = 1;
            $album->save();
            $this->setParams('defaultAlbumId', $album->id);
            $this->save();
        }
        return $album;
    }

    /**
     * Return a list of user's album
     * 
     * @return object
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Return brief information builder for this user
     * 
     * @return Illuminate\Database\Query\builder
     */
    public function getBriefBuilder()
    {
        $builder = HALOUIBuilder::getInstance('', 'brief.user', array('user' => $this, 'zone' => 'brief_' . $this->getContext() . '_' . $this->id));
        return $builder;
    }

    /**
     * Load notification settting of this user
     * 
     * @return HALOObjectd
     */
    protected function loadNotificationSettings()
    {
        if (is_null($this->_notifSettings)) {
            $this->_notifSettings = HALOObject::getInstance(HALONotificationAPI::loadNotificationSettings($this));
        }
    }
 
    /**
     * Return setting value of a notification
     * 
     * @param  string  $notification notification name in the HALOObject namespace format
     * @param  integer $default      
     * @return object                
     */
    public function getNotificationSettings($notification, $default = 0)
    {
        $this->loadNotificationSettings();
        return $this->_notifSettings->getNsValue($notification, $default);
    }

    /**
     * Set notification settings 
     * 
     * @param string  $notification 
     * @param string  $val          
     */
    public function setNotificationSettings($notification, $val)
    {
        $this->loadNotificationSettings();
        $this->_notifSettings->setNsValue($notification, $val);
        //update user params
        $this->setParams('notif', $this->_notifSettings);
    }

    /**
     * Return an array of accepted notification for this user
     * 
     * @param  int $type 
     * @return array
     */
    public function getNotificationList($type = HALO_NOTIF_TYPE_A)
    {
        $rtn = array();
        $this->loadNotificationSettings();
        $settings = json_decode(json_encode($this->_notifSettings), true);
        foreach ($settings as $groupName => $group) {
            if (is_array($group)) {
                foreach ($group as $notifName => $values) {
                    $e = isset($values['e']) ? $values['e'] : 0;
                    $i = isset($values['i']) ? $values['i'] : 0;
                    if (((int) $e & $type) || (int) $i&($type >> 1)) {
                        $rtn[] = $groupName . '.' . $notifName;
                    }
                }
            }
        }
        return $rtn;

    }

    /**
     * Return true if $user friend with this user model
     * 
     * @param  string  $user 
     * @return boolean       
     */
    public function isFriend($user = null)
    {
        if (is_null($user)) {
            $user = HALOUserModel::getUser();
        }
        if (!is_a($user, 'HALOUserModel')) {
            return false;
        }

        //make sure user is init from cached
        return (boolean) $this->friends->find($user->id, false);
    }

    /**
     * Return true if $user has pending friend request with this user model
     * 
     * @param  HALOUserModel  $user 
     * @return boolean 
     */
    public function isRequestingFriend($user = null)
    {
        if (is_null($user)) {
            $user = HALOUserModel::getUser();
        }
        if (!is_a($user, 'HALOUserModel')) {
            return false;
        }

        return (boolean) $this->friendRequests->find($user->id, false);
    }

    /**
     * Return true if $user sent friend request to list user model
     * 
     * @param  HALOUserModel  $user 
     * @return boolean       
     */
    public function isRequestedFriend($user = null)
    {
        if (is_null($user)) {
            $user = HALOUserModel::getUser();
        }
        if (!is_a($user, 'HALOUserModel')) {
            return false;
        }

        return (boolean) $this->pendingFriendRequests->find($user->id, false);
    }

    /**
     * Return a list of short infomation about this user
     * 
     * @return array
     */
    public function getShortInfo()
    {
        if ($this->id) {
            //make sure user is init from cached
            $user = HALOUserModel::getUser($this->user_id);
			if(!is_null($user->_shortInfo)) return $user->_shortInfo;
            Event::fire('user.onLoadShortInfo', array(&$user));
            return isset($user->_shortInfo) ? $user->_shortInfo : array();
        }
        return new HALOQueue();
    }

    /**
     * Display filter handler for showing search text input of a column
     * 
     * @param  HALOParams $params 
     * @param  string   $uiType 
     * @return string           
     */
    public static function displayNameSearchDisplayFilter(HALOParams $params, $uiType)
    {
        //$params->set('uiType','form.filter_text');
        $params->set('uiType', 'filter.text');
        return '';
    }

    /**
     * Allpy filter handler to apply query condition statement on a ciolumm
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  HALOUtilHelper $value  
     * @param  int $params 
     * @return bool         
     */
    public static function applyNameSearchApplyFilter(&$query, $value, $params)
    {
        if (!empty($value)) {
            try {
                $query = HALOUtilHelper::joinUserTable($query, array(HALO_USER_DISPLAY_NAME_COL => 'name', HALO_USER_ID_COL => 'id'));
                $query = $query->whereRaw(HALOUtilHelper::getTextSearchCondition(HALO_USER_DISPLAY_NAME_COL, $value));
                return true;

            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply filter handler to search user's friends only
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  HALOUtilHelper $value  
     * @param  strign $params 
     * @return bool         
     */
    public static function getFriendsApplyFilter(&$query, $value, $params)
    {
        if (!empty($value)) {
            try {
                $query = $query->whereRaw(HALOUtilHelper::getTextSearchCondition(HALO_USER_DISPLAY_NAME_COL, $value));
                return true;

            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Display filter handler for showing sort order selection
     * 
     * @param  HALOParams $params 
     * @param  string   $uiType 
     * @return array           
     */
    public static function displaySortByDisplayFilter(HALOParams $params, $uiType)
    {
        //$params->set('uiType','form.filter_tree_sort');
        $params->set('uiType', 'filter.sort');
        $options = array();
        //sort options
        $options['sort'] = array(HALOObject::getInstance(array('name' => __halotext('Name'), 'value' => HALO_USER_DISPLAY_NAME_COL))//sort by user name
            , HALOObject::getInstance(array('name' => __halotext('Registration Date'), 'value' => 'created_at'))//sort by registration date
            //,HALOObject::getInstance(array('name'=>__halotext('Friend Count'),'value'=>'friend_count'))                            //sort by registration date
            , HALOObject::getInstance(array('name' => __halotext('User Point'), 'value' => 'point_count'))//sort by registration date
        );
        $options['dir'] = array(HALOObject::getInstance(array('name' => __halotext('ASC'), 'value' => 'asc'))//asc direction
            , HALOObject::getInstance(array('name' => __halotext('DESC'), 'value' => 'desc'))//desc direction
        );
        return $options;
    }

    /**
     * Apply filter handler to apply query condition statement on a column
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  array $value  
     * @param  string $params 
     * @return bool         
     */
    public static function applySortByApplyFilter(&$query, $value, $params)
    {
        //$value is an array of ('sort', 'dir')
        if (is_string($value)) {
            $values = explode(',', $value);
        } else if (is_array($value)) {
            $values = $value;
        }
        try {
            $defaultSort = 'created_at';
            $defaultDir = 'desc';

            $sortValues = array(HALO_USER_DISPLAY_NAME_COL, 'created_at', 'point_count');
            $dirValues = array('asc', 'desc');

            $sort = array_intersect($values, $sortValues);
            $sort = empty($sort) ? $defaultSort : array_shift($sort);

            $dir = array_intersect($values, $dirValues);
            $dir = empty($dir) ? $defaultDir : array_shift($dir);

            $query = HALOUtilHelper::joinUserTable($query, array(HALO_USER_DISPLAY_NAME_COL => 'name', HALO_USER_ID_COL => 'id'));
            $query = $query->orderBy($sort, $dir)
                           ->orderBy($defaultSort, $defaultDir);
            return true;

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Display filter to display list of post by status 
     * 
     * @param  string $params 
     * @param  string $uiType 
     * @return array         
     */
    public static function getUserTypeDisplayFilter($params, &$uiType)
    {
        //for filter display in profile page
        $my = HALOUserModel::getUser();
        $options = array();
        $options = array(HALOObject::getInstance(array('name' => __halotext('Pending friend request'), 'value' => 'pending'))
            , HALOObject::getInstance(array('name' => __halotext('Moderator'), 'value' => 'mod'))
        );
        if ($my) {
            $options[] = HALOObject::getInstance(array('name' => __halotext('Friends'), 'value' => 'friend'));
        }
        //$uiType = 'form.filter_tree_chechbox'; //user filter_tree_radio UI for this filter
        $uiType = 'filter.multiple_select';//user filter_tree_radio UI for this filter
        return $options;
    }

    /**
     * Apply filter by pót status 
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  array $value  
     * @param  string $params 
     * @return bool 
     */
    public static function getUserTypeApplyFilter(&$query, $value, $params)
    {
        if (is_string($value)) {
            $values = explode(',', $value);
        } else if (is_array($value)) {
            $values = $value;
        }
        if (empty($values)) {
            //nothing to apply on the value
            return true;
        }

        try {
            $query = $query->where(function ($q) use ($values) {
                $my = HALOUserModel::getUser();
                if (!$my) {
                    return $q;
                }

                foreach ($values as $val) {
                    if ($val == 'pending') {
                        //show only pending friend request users
                        $pendingRequestUserIds = $my->pendingFriendRequests->lists('id');
                        $q->orWhere(function ($q) use ($pendingRequestUserIds) {
                            if (empty($pendingRequestUserIds)) {
                                $pendingRequestUserIds = array(-1);//return never matched array list
                            }
                            $q->whereIn('halo_users.id', $pendingRequestUserIds);
                            return $q;
                        });
                    }
                    if ($val == 'mod') {
                        //show only mod
                        //get mod ids
                        $modUsers = HALOAuth::getUsersByRole('mod');
                        if ($modUsers && count($modUsers)) {
                            $modUserIds = $modUsers->lists('id');
                            $q->orWhere(function ($q) use ($modUserIds) {
                                if (empty($modUserIds)) {
                                    $modUserIds = array(-1);//return never matched array list
                                }
                                $q->whereIn('halo_users.id', $modUserIds);
                                return $q;
                            });
                        }
                    }
                    if ($val == 'friend') {
                        //show only friends
                        $friendIds = $my ? $my->getFriendIds() : array(-1);
                        if (empty($friendIds)) {
                            $friendIds = array(-1);
                        }
                        $q->orWhere(function ($q) use ($friendIds) {
                            $q->whereIn('halo_users.id', $friendIds);
                            return $q;
                        });
                    }
                }
                return $q;
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;

    }

    /**
     * Return total number of active users
     * 
     * @return int
     */
    public static function getTotalUsersCounter()
    {
        return HALOUserModel::count();
    }

    /**
     * Function to create new user by using input data
     * 
     * @param   $params 
     * @return  HALOUserModel|null
     */
    public static function createNew($params)
    {
        Event::fire('user.onBeforeCreate', array($params));
        $user = UserModel::createNew($params);
        //trigger event
        if ($user) {
            //force to sync user
            Cache::forget('halo_sync_user');
            Event::fire('user.onAfterCreate', array($user));
            return HALOUserModel::getUser($user->getId());
        } else {
            return null;
        }
    }

    /**
     * Return profile Id for this user 
     * 
     * @return int
     */
    public function getProfileId()
    {
        return is_null($this->profile_id) ? HALO_PROFILE_DEFAULT_USER_ID : $this->profile_id;
    }

    /**
     * Function to delete this user model and all its data
     * 
     * @return bool
     */
    public function delete()
    {
        $userId = $this->id;
        Event::fire('user.onBeforeDelete', array($this));
        $rtn = true;
        //start delete user data
        //#1 delete user's photos
        $this->photos()->delete();
        //#1 delete user's photos
        $this->albums()->delete();
        //#2 delete user's videos
        $this->videos()->delete();
        //#3 delete user's comments
        $this->comments()->delete();
        //#4 delete user's activities
        $this->activities()->delete();
        //#5 delete user's conversation
        $this->conversations()->delete();
        //#6 delete user's posts
        //$this->posts()->delete();
        //#7 delete user's notification
        $this->notifications()->delete();
        //#8 delete like
        $this->likes()->delete();
        //#9 delete user tagging
        $this->tag()->delete();

        //#10 delete user's connection
        $this->connections()->detach();
        $this->getProfileFieldValues()->detach();

        //#11 delete oauth
        $this->oauth()->delete();
		
        $user = $this->user;
		//only delete cms user if mapping exist
		if($user) {
			$user->delete();
		}
        //delete this model instance
        parent::delete();
        if ($rtn) {
            Event::fire('user.onAfterDelete', array($userId));
        }

        return $rtn;
    }

    /**
     * Return new state of a toggleable field
     * 
     * @param  string $field 
     * @return  object        
     */
    public function toggleState($field)
    {
        //toggle is only for int field and in toggleable list
		switch($field) {
			case 'confirmed':
				$val = intval($this->user->getConfirmState());
				$val++;
				$states = $this->getStates($field);
				$num = count($states);
				if ($num > 0) {
					$val = $val % $num;
				}
				$this->user->setConfirmState($val);
				$this->user->save();
				return $this->user->getConfirmState();
			case 'block':
				$val = intval($this->user->isBlocked());
				$val++;
				$states = $this->getStates($field);
				$num = count($states);
				if ($num > 0) {
					$val = $val % $num;
				}
				$this->user->setBlock($val);
				return intval($this->user->isBlocked());
			default:
				return parent::toggleState($field);
		}
    }

    /**
     * Return current fielt state value
     * 
     * @param  string $field 
     * @return string        
     */
    public function getFieldState($field)
    {
        if ($this->user) {
			switch($field){
				case 'confirmed':
					return $this->user->getConfirmState();
				case 'block':
					return intval($this->user->isBlocked());
				default:
					return parent::getFieldState($field);
			}
            
        } else {
            return '';
        }
    }

    /**
     * Set current field state value
     * @param string $field [description]
     * @param string $value [description]
     */
    public function setFieldState($field, $value)
    {
        if ($this->user) {
			switch($field) {
				case 'confirmed':
					return $this->user->setConfirmState($value);
				case 'block':
					return $this->user->setBlock();
				default:
					return parent::setFieldState($field, $value);
			}
            
        } else {
            return '';
        }
    }

    /**
     * Return toggleable state a field 
     * 
     * @param  string $field 
     * @return array
     */
    public function getStates($field)
    {
        //by default only enable/disable state provided. For additional states, need to override this method
        if ($field == 'block') {
            return array(0 => array('title' => __halotext('Active'),
                'icon' => 'check-circle text-success'),
                1 => array('title' => __halotext('Blocked'),
                    'icon' => 'times-circle text-danger')

            );
        } else if ($field == 'confirmed') {
            return array(0 => array('title' => __halotext('No'),
                'icon' => 'times-circle text-danger'),
                1 => array('title' => __halotext('Yes'),
                    'icon' => 'check-circle text-success')

            );
        } else if ($field == 'user_role') {
            return array(0 => array('title' => __halotext('No'),
                'icon' => 'times-circle text-danger'),
                1 => array('title' => __halotext('Yes'),
                    'icon' => 'check-circle text-success')

            );
        }
    }

    /**
     * Function to set user's resource callback function
     * 
     * @param string $method 
     * @param string $func   
     */
    public static function setResourceCb($method, $func)
    {
        self::$resourceCbs[$method] = $func;
    }

    /**
     * Function to get user's resrouce callback function
     * @param  object $method 
     * @return object        
     */
    public static function getResourceCb($method)
    {
        static $loaded = null;
        //trigger event if resourceCbs is not loaded
        if (is_null($loaded)) {
            Event::fire('user.onGetResourceCb', array());
        }
        return isset(self::$resourceCbs[$method]) ? self::$resourceCbs[$method] : null;
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  object  $method
     * @param  object  $parameters
     * @return array
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, array('increment', 'decrement'))) {
            return call_user_func_array(array($this, $method), $parameters);
        }

        $cb = HALOUserModel::getResourceCb($method);
        if (!is_null($cb)) {
            return call_user_func_array($cb, array_merge(array($this), $parameters));
        }

        $query = $this->newQuery();

        return call_user_func_array(array($query, $method), $parameters);
    }

    /**
     * Return array of moderator Id
     * 
     * @return HALOUserModel
     */
    public static function getModIds()
    {
        return HALOUserModel::where('user_role', 1)->lists('id');
    }

    /**
     * Set slug string this model
     * 
     * @param string $slug 
     */
    public function setSlug($slug = null)
    {
        if (is_null($slug)) {
            //generate slug from display name
            $slug = Str::slug($this->getDisplayName());
        }
        return parent::setSlug($slug);
    }

    /**
     * Get use's custom filter 
     * 
     * @param  int $filterId 
     * @return Illuminate\Database\Query\Builder
     */
    public function getCustomFilters($filterId)
    {

        $query = $this->hasMany('HALOCustomFilterModel', 'creator_id')
                      ->where('filter_id', $filterId);
        return $query->get();
    }

    /**
     * Get edit link url for this user
     * 
     * @param  array  $contextParams 
     * @return string           
     */
    public function getEditUrl($contextParams = array())
    {
        return URL::to('?view=user&task=edit&uid=' . $this->id);
    }

    /**
     * getNotificationTargetAction ajax response a target url
     *
     * @return bool
     * @author Phuong Ngo <fuongit@gmail.com>
     */
    public function getNotificationTargetAction()
    {
        HALOResponse::redirect($this->getUrl());
        return true;
    }

    /**
     * Get Confirmation code 
     * 
     * @return string
     */
    public function getConfirmationCode()
    {
        //make sure user is init from cached
        $user = HALOUserModel::getUser($this->user_id);
        if ($user->user) {
            return $user->user->getConfirmationCode();
        } else {
            return '';
        }
    }

	/*
		apply default filter for listing
	*/
	public static function configureFilters(&$listingFilters, &$query) {
        foreach ($listingFilters as $filter) {
            //setup default sorting
            if ($filter->name == 'user.listing.sort') {
                $fValues = Input::get('filters');
                if (!isset($fValues[$filter->id])) {
                    $filter->value = array('default', 'default');
					if(!is_null($query)) {
						$filter->applyFilter($query, array('default', 'default')); 
					}
                }
            }
            //for other filter, get from http request or session data
        }
	}
	
	/*
		check and install filter for user
	*/
	public static function setupFilters() {
		$filters = array(
						array(
							'name'      	=> 'user.listing.profiletype',
							'type' 	        => 'core', 
							'description' 	=> 'Filter users by profile type',
							'on_display_handler' 	=> 'HALOUserModel::getMemberProfileType', 
							'on_apply_handler' 		=> 'HALOUserModel::getMemberProfileType', 
							'params' 		=> '{"title":"Profile Type"}',

							'published' => 1,

							'created_at' => new DateTime,
							'updated_at' => new DateTime,
						)
					);
		
		HALOFilter::insertNewFilters($filters);
		return true;
	}
	
    /**
     * Display filter to display list of post by status 
     * 
     * @param  string $params 
     * @param  string $uiType 
     * @return array         
     */
    public static function getMemberProfileTypeDisplayFilter($params, &$uiType)
    {
		$profiles = HALOProfileModel::getProfileListOption('user',false);
		$options = array();	//do not show this filter
		if(count($profiles) > 1) {
			foreach($profiles as $profile) {
				$options[] = HALOObject::getInstance(array('name' => $profile['title'], 'value' => $profile['value']));
			}
		}
		// var_dump($options);
        $uiType = 'filter.multiple_select';//user filter_tree_radio UI for this filter
        return $options;
    }

    /**
     * Apply filter by post status 
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  array $value  
     * @param  string $params 
     * @return bool 
     */
    public static function getMemberProfileTypeApplyFilter(&$query, $value, $params)
    {
        if (is_string($value)) {
            $values = explode(',', $value);
        } else if (is_array($value)) {
            $values = $value;
        }
        if (empty($values)) {
            //nothing to apply on the value
            return true;
        }

        try {
			$defaultId = HALOProfileModel::getDefaultProfileId('user');
			if(in_array($defaultId, $values)) {
				$query = $query->where(function($q) use($values) {
					$q->whereIn('profile_id', $values)
						->orWhereNull('profile_id');
					return $q;
				});
			} else {
				$query = $query->whereIn('profile_id', $values);
			}
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Check if user profile has been completly updated
     * 
     * @return bool 
     */
    public function isProfileCompleted() {
		$key = 'user.isProfileCompleted.' . $this->user_id;
		$that = $this;
		return Cache::remember($key, 24 * 60, function() use($that) {
			$fields = $that->getProfileFields()->get();
			foreach($fields as $field) {
				$haloField = $field->toHALOField();
				if($haloField->isRequired() && $haloField->getReadableUI() === '') {
					return false;
				};
			}
			return true;
		});
	}
	
    /**
     * Check if this user has avatar uploaded
     * 
     * @return bool 
     */
    public function hasAvatar() {
		return (boolean) $this->avatar_id || (boolean) $this->oauth;
	}
	
}
