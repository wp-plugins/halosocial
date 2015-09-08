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

class UserEventHandler
{

	/**
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        //user events
        $events->listen('user.onLoadShortInfo', 'UserEventHandler@onLoadUserInfo');
        $events->listen('user.onLoadInfoCounters', function ($users) {
            HALOModelHelper::loadRelationCounter($users, array('followers', 'friends', 'photos', 'videos', 'groups', 'shops', 'posts'));
            $users->load('friends');
            $users->load('friendRequests');
        });
        $events->listen('user.onDisplayUserInfo', 'UserEventHandler@onDisplayUserInfo');

        //login user
        $events->listen('user.onLogin', 'UserEventHandler@onLoginUser');
        //logout user
        $events->listen('user.onLogout', 'UserEventHandler@onLogoutUser');
        //changed password
        $events->listen('user.onChangedPassword', 'UserEventHandler@onChangedPassword');

        //system event
        $events->listen('system.onLoadShareBoxUI', 'UserEventHandler@onLoadShareBoxUI');

        $events->listen('system.onLoadPendingActions', 'UserEventHandler@onLoadPendingActions');

        //configuration settings
        $events->listen('config.loadSettings', 'UserEventHandler@onLoadConfigurationSettings');

        //label settings
        $events->listen('label.loadGroup', 'UserEventHandler@onLoadLabelGroup');
		
		//avatar changed
		$events->listen('photo.onAfterChangingAvatar', 'UserEventHandler@onChangeAvatar');

		//check missing avatar
		$events->listen('system.frontendNotice', 'UserEventHandler@frontendNotice');
    }

    /**
     * load user short info
     * 
     * @param  array $user 
     * @return bool
     */
    public function onLoadUserInfo($user)
    {
        //Follower count
		if(HALOAuth::can('feature.follow')) {
			$followerFilters = HALOFilter::getFilterByName('follower.listing.*');
			$followersCount = $user->getRelationCounter('followers');
			$blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Followers'), 'actions' => '', 'zone' => 'halo-followers-wrapper', 
																			'filters' => $followerFilters, 'onChange' => "halo.user.refreshSection('follower','" . $user->id . "')",
																			'content' => HALOResponse::getZoneContent('halo-followers-wrapper')));
			$shortInfo = HALOObject::getInstance(array('url' => $user->getUrl(array('usec'=>'follower')), 'class' => '', 'title' => __halotext('Follower count')
				, 'value' => __halontext('%s Follower', '%s Followers', $followersCount)
				, 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.user.displaySection('follower','" . $user->id . "')"
				, 'name' => 'follower'));
			$user->insertShortInfo($shortInfo);
		}
        //Friend count
		if(HALOAuth::can('feature.friend')) {
			$friendsCount = $user->getRelationCounter('friends');
			$blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Friends'), 'actions' => '', 'zone' => 'halo-friends-wrapper', 'content' => ''));
			$shortInfo = HALOObject::getInstance(array('url' => $user->getUrl(array('usec'=>'friend')), 'class' => '', 'title' => __halotext('Friend count')
				, 'value' => __halontext('%s Friend', '%s Friends', $friendsCount)
				, 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.user.displaySection('friend','" . $user->id . "')"
				, 'name' => 'friend'));
			$user->insertShortInfo($shortInfo);
		}
        //album count
        $albumsCount = $user->getRelationCounter('albums');
        $blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Albums'), 'actions' => '', 'zone' => 'halo-albums-wrapper', 'class' => '', 'content' => ''));
        $shortInfo = HALOObject::getInstance(array('url' => $user->getUrl(array('usec'=>'album')), 'class' => '', 'title' => __halotext('Photo count')
            , 'value' => __halontext('%s Album', '%s Albums', $albumsCount)
            , 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.user.displaySection('album','" . $user->id . "')"
            , 'name' => 'album'));
		$user->insertShortInfo($shortInfo);
        //photo count
        $photosCount = $user->getRelationCounter('photos');
        $blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Photos'), 'actions' => '', 'zone' => 'halo-photos-wrapper', 'class' => 'halo-gallery-popup', 'content' => ''));
        $shortInfo = HALOObject::getInstance(array('url' => $user->getUrl(array('usec'=>'photo')), 'class' => '', 'title' => __halotext('Photo count')
            , 'value' => __halontext('%s Photo', '%s Photos', $photosCount)
            , 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.user.displaySection('photo','" . $user->id . "')"
            , 'name' => 'photo'));
		$user->insertShortInfo($shortInfo);
        //video count
        $videosCount = $user->getRelationCounter('videos');
        $blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Videos'), 'actions' => '', 'zone' => 'halo-videos-wrapper', 'content' => ''));
        $shortInfo = HALOObject::getInstance(array('url' => $user->getUrl(array('usec'=>'video')), 'class' => '', 'title' => __halotext('Video count')
            , 'value' => __halontext('%s Video', '%s Videos', $videosCount)
            , 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.user.displaySection('video','" . $user->id . "')"
            , 'name' => 'video'));
		$user->insertShortInfo($shortInfo);
        return true;
    }

    /**
     * Handler user.onlogin event
     * 
     * @param  array $user 
     */
    public function onLoginUser($user = null)
    {
        //notify to user's friend about status changed
        $friends = $user ? $user->getFriends() : array();
        $receivers = array();
        foreach ($friends as $friend) {
            $receivers[] = $friend->id;
        }
        if (!empty($receivers)) {
            PushClient::broadcast($receivers, 'online', $user->id);
        }
        // HALOOnlineuserModel::online($user);
    }

    /**
     * Handler user.Logout event
     * 
     * @param  array $user 
     */
    public function onLogoutUser($user = null)
    {
        //notify to user's friend about status changed
        $friends = $user ? $user->getFriends() : array();
        $receivers = array();
        foreach ($friends as $friend) {
            $receivers[] = $friend->id;
        }
        if (!empty($receivers)) {
            PushClient::broadcast($receivers, 'online', (0 - $user->id));
        }
        // HALOOnlineuserModel::offline($user);
    }

    /**
     * Handler user.onChangePassword event
     * 
     * @param  array $user 
     */
    public function onChangedPassword($user = null)
    {
        //notify to user's friend about status changed
        $emailData = array();

        $emailData['to'] = $user->getEmail();
        $emailData['subject'] = __halotext('Your account password has been changed');
        $emailData['plain_msg'] = HALOUIBuilder::getInstance('', 'email.changePasswordNotif_plain', array('toName' => $user->getDisplayName()))->fetch();
        $emailData['html_msg'] = HALOUIBuilder::getInstance('', 'email.changePasswordNotif_html', array('toName' => $user->getDisplayName()))->fetch();
        $emailData['template'] = 'emails/layout';

        HALOMailqueueAPI::add($emailData);
    }

    /**
     * Handler to display user info fora spectific section
     * 
     * @param  string $section 
     * @param  array $data    
     */
    public function onDisplayUserInfo($section, $data)
    {
        switch ($section) {
            case 'follower':
                $this->ajaxListFollowers($data);
                break;
            case 'friend':
                $this->ajaxListFriends($data);
                break;
            case 'photo':
                $this->ajaxListPhotos($data);
                break;
            case 'album':
                $this->ajaxListAlbums($data);
                break;
            case 'video':
                $this->ajaxListVideos($data);
                break;

        }
    }

    /**
     * Ajax handler to list a user's followers
     * 
     * @param  array $postData 
     */
    private function ajaxListFollowers($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);

        $userId = Input::get('userid');

        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user ID')));
        }
		//follower.listing.type
		HALOFollowerModel::setupFilters();
        $listingFilters = HALOFilter::getFilterByName('follower.listing.*');

		//follower or following?
        foreach ($listingFilters as $filter) {
            //setup default sorting
            if ($filter->name == 'follower.listing.type') {
                $fValues = Input::get('filters');
                if (isset($fValues[$filter->id]) && 'following' === $fValues[$filter->id]) {
					//following mode
					$query = $user->following();
					$followMode = 'following';
                } else {
					// default value is followers
					$query = $user->followers();
					$followMode = 'follower';
				}
            }
        }
		
        //setup filters
        //configure filters value
		HALOFollowerModel::configureFilters($listingFilters, $query);

        $users = HALOPagination::getData($query);
		
        //init users
        $userIds = array();
        foreach ($users as $u) {
			if($followMode == 'following') {
				$userIds[] = $u->followable_id;
			} else {
				$userIds[] = $u->follower_id;
			}
        }
        $cachedUsers = HALOModel::getCachedModel('user', $userIds);

        $friendListHtml = HALOUIBuilder::getInstance('', 'user.list', array('users' => HALOUtilHelper::lazyLoadArray($cachedUsers, array('photos', 'videos')), 'zone' => 'halo-followers-wrapper'))->fetch();

        HALOResponse::setZoneContent('halo-followers-wrapper', $friendListHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $users->links('ui.pagination_ajax')->__toString();

        HALOResponse::setZonePagination('halo-followers-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-followers-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Followers')), HALOUIBuilder::getPaginationText($users));

		//update page title
        HALOResponse::addZoneScript('halo-followers-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title', $user));
    }

    /**
     * Ajax handler to list a user's friends
     * 
     * @param  array $postData 
     */
    private function ajaxListFriends($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);

        $userId = Input::get('userid');

        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user ID')));
        }

        $users = HALOPagination::getData($user->friends());

        //init users
        $userIds = array();
        foreach ($users as $u) {
            $userIds[] = $u->id;
        }
        $cachedUsers = HALOModel::getCachedModel('user', $userIds);

        $friendListHtml = HALOUIBuilder::getInstance('', 'user.list', array('users' => HALOUtilHelper::lazyLoadArray($cachedUsers, array('photos', 'videos')), 'zone' => 'halo-friends-wrapper'))->fetch();

        HALOResponse::setZoneContent('halo-friends-wrapper', $friendListHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $users->links('ui.pagination_ajax')->__toString();

        HALOResponse::setZonePagination('halo-friends-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-friends-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Friends')), HALOUIBuilder::getPaginationText($users));

		//update page title
        HALOResponse::addZoneScript('halo-friends-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title', $user));
    }

    /**
     * Ajax handler to list a user's photos
     * 
     * @param  array $postData 
     */
    private function ajaxListPhotos($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);

        $userId = Input::get('userid');

        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user ID')));
        }

        $photos = HALOPagination::getData($user->photos()->orderBy('created_at', 'desc'));

        //init users

        $photoListHtml = HALOUIBuilder::getInstance('', 'photo.list', array('photos' => $photos))->fetch();

        HALOResponse::setZoneContent('halo-photos-wrapper', $photoListHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $photos->links('ui.pagination_auto')->__toString();

        HALOResponse::setZonePagination('halo-photos-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-photos-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Photos')), HALOUIBuilder::getPaginationText($photos));

		//update page title
        HALOResponse::addZoneScript('halo-photos-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title', $user));
    }

    /**
     * Ajax handler to list a user's album
     * 
     * @param  array $postData 
     */
    private function ajaxListAlbums($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);

        $userId = Input::get('userid');

        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user ID')));
        }

        $albums = HALOPagination::getData($user->albums()->orderBy('created_at', 'desc'));

        //init users

        $photoListHtml = HALOUIBuilder::getInstance('', 'photo.albums_view', array('albums' => $albums))->fetch();

        HALOResponse::setZoneContent('halo-albums-wrapper', $photoListHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $albums->links('ui.pagination_auto')->__toString();

        HALOResponse::setZonePagination('halo-albums-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-albums-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Albums')), HALOUIBuilder::getPaginationText($albums));

		//update page title
        HALOResponse::addZoneScript('halo-albums-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title', $user));
    }

    /**
     * Ajax handler to list a user's videos
     * 
     * @param  array $postData 
     */
    private function ajaxListVideos($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);

        $userId = Input::get('userid');

        $user = HALOUserModel::getUser($userId);

        // Check if the user exists
        if (is_null($user)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid user ID')));
        }

        $videos = HALOPagination::getData($user->videos());

        //init users

        $videoListHtml = HALOUIBuilder::getInstance('', 'video_list', array('videos' => $videos))->fetch();

        HALOResponse::setZoneContent('halo-videos-wrapper', $videoListHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $videos->links('ui.pagination_auto')->__toString();

        HALOResponse::setZonePagination('halo-videos-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-videos-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Videos')), HALOUIBuilder::getPaginationText($videos));

		//update page title
        HALOResponse::addZoneScript('halo-videos-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title', $user));
    }

    /**
     * Function to load sharebc UI for user's profile page
     * 
     * @param  object $contentUIs 
     * @param  string $context    
     * @param  int $target_id  
     */
    public function onLoadShareBoxUI($contentUIs, $context, $target_id)
    {
        if ($context == 'profile') {
            $contentUIs->status = HALOStatus::getStatusUI($context, $target_id);
            $contentUIs->photo = HALOStatus::getPhotoUI($context, $target_id);
            $contentUIs->video = HALOStatus::getVideoUI($context, $target_id);
            //$contentUIs->post = HALOPostAPI::getSharePostUI($context,$target_id);
        }
    }

    /**
     * Load configuration setting UI for User
     * 
     * @param  array $settings        
     */
    public function onLoadConfigurationSettings($settings)
    {
        $config = new stdClass();
        $config->title = "Users";
        $config->icon = "";
        $config->builder = HALOUIBuilder::getInstance('user_cfg', 'config.group', array('name' => 'user_cfg'));

        //users section
        $section = HALOUIBuilder::getInstance('', 'config.section', array('title' => __halotext('Settings')));

        //default profile
        $options = DB::table('halo_profiles')->distinct()
                                           ->where('type', '=', 'user')
                                           ->select('name as title', 'id as value')->get();

        $section->addUI('user.haloregistration', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.haloregistration',
					'title' => __halotext('Use HaloSocial registration form'),
					'helptext' => __halotext('Use HaloSocial registration form instead of WordPress form'),
					'options' => array(
						HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No'))),
					),
					'value' => HALOConfig::get('user.haloregistration', 1))))
                ->addUI('user.confirmEmail', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.confirmEmail',
					'title' => __halotext('Confirm new user email'),
					'helptext' => __halotext('A confirmation email will be sent to user when a new user is created'),
					'options' => array(
						HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No'))),
					),
					'value' => HALOConfig::get('user.confirmEmail', 1))))
				->addUI('user.usehaloavatar', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.usehaloavatar',
					'title' => __halotext('Use HaloSocial avatar as default'),
					'helptext' => __halotext("Enable this option to force WordPress use Halo's user avatar as user avatar"),
					'options' => array(HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No'))),
					),
					'value' => HALOConfig::get('user.usehaloavatar', 1))))
				->addUI('user.defaultProfile', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.defaultProfile',
					'title' => __halotext('Default User Profile'),
					'helptext' => __halotext('Default User Profile for new register users'),
					'value' => HALOConfig::get('user.defaultProfile'),
					'options' => $options)))
				->addUI('user.requireAvatar', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.requireAvatar',
					'title' => __halotext('Check for default avatars'),
					'value' => HALOConfig::get('user.requireAvatar', 0),
					'options' => array(
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext("Don't check"))),
						HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Warn user'))),
						HALOObject::getInstance(array('value' => 2, 'title' => __halotext('Warn and redirect'))),
					))))
				->addUI('user.requireProfile', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.requireProfile',
					'title' => __halotext('Check for missing required fields'),
					'value' => HALOConfig::get('user.requireProfile', 0),
					'options' => array(
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext("Don't check"))),
						HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Warn user'))),
						HALOObject::getInstance(array('value' => 2, 'title' => __halotext('Warn and redirect'))),
					))))
                ->addUI('user.redirectLogin', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.redirectLogin',
                    'title' => __halotext('Redirect users to specific locations after login'),
                    'value' => HALOConfig::get('user.redirectLogin', 0),
                    'options' => array(
                        HALOObject::getInstance(array('value' => 0, 'title' => __halotext("Frontpage"))),
                        HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Profile page'))),
                        HALOObject::getInstance(array('value' => 2, 'title' => __halotext('Members page'))),
                    ))))
                ->addUI('user.redirectLogout', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.redirectLogout',
                    'title' => __halotext('Redirect users to specific locations after logout'),
                    'value' => HALOConfig::get('user.redirectLogout', 0),
                    'options' => array(
                        HALOObject::getInstance(array('value' => 0, 'title' => __halotext("Frontpage"))),
                        HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Members page'))),
                    ))))
                ->addUI('user.registerUrl', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.registerUrl',
                    'title' => __halotext('Redirect all the register links to Halosocial register link'),
                    'value' => HALOConfig::get('user.registerUrl', 0),
                    'helptext' => __halotext('Redirect all the links of Wordpress registration to Halosocial registration'),
                    'options' => array(
                        HALOObject::getInstance(array('value' => 0, 'title' => __halotext("No"))),
                        HALOObject::getInstance(array('value' => URL::to('?view=user&task=register'), 'title' => __halotext('Yes')))
                    ))))
				->addUI('user.defaultDisplayLimit', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'user.defaultDisplayLimit',
					'title' => __halotext('User Display Limit'),
					'helptext' => __halotext('Number of users displayed per request'),
					'value' => HALOConfig::get('user.defaultDisplayLimit'))))
				->addUI('user.suggestFriendsOnly', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.suggestFriendsOnly',
					'title' => __halotext('Tag Friends Only'),
					'helptext' => __halotext('For user tagging, configure this option to show only friends or all users as suggestion'),
					'options' => array(HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
						HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No'))),
					),
					'value' => HALOConfig::get('user.suggestFriendsOnly'))))
        ;
        $config->builder->addUI('section@array', $section);

		//Labels settings section
		$singleLabelGroups = HALOLabelGroupModel::where('group_type', HALOLabelGroupModel::GROUP_TYPE_SINGLE)->get();
		$allLabelGroups = HALOLabelGroupModel::all();
		$singleOptions = HALOUtilHelper::collection2options($singleLabelGroups, 'group_code', 'name');
		$allOptions = HALOUtilHelper::collection2options($allLabelGroups, 'group_code', 'name');
		$zone = 'halo-lbl-new' . HALOUtilHelper::uniqidInt();
        $section = HALOUIBuilder::getInstance('user.label', 'config.section', array('title' => __halotext('Label Settings')))
						->addUI('user.label.status', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.label.status',
									'title' => __halotext('User Status Label'),
									'helptext' => __halotext('Configure label used for status'),
									'value' => HALOConfig::get('user.label.status'), 'onChange' => 'halo.label.listLabelsInGroup(this.value, \''. $zone . '\', \'user.label.new\')',
									'options' => $singleOptions)));

		//label for new user
		if(HALOConfig::get('user.label.status')) {
			$newLabelGroup = HALOLabelGroupModel::where('group_code', HALOConfig::get('user.label.status'))->first();
			$newLabels = $newLabelGroup? $newLabelGroup->labels:array();
		} else {
			$newLabels = array();
		}
		$newLabelOptions = HALOUtilHelper::collection2options($newLabels, 'label_code', 'name');
		$newLabel = HALOUIBuilder::getInstance('', 'content', array('zone' => $zone))
						->addUI('user.label.new', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.label.new',
									'title' => __halotext('Label for New Users'),
									'helptext' => __halotext('Status label that will be automatically be assigned for a new user'),
									'value' => HALOConfig::get('user.label.new'),
									'options' => $newLabelOptions)));
		$section->addUI('user.label.new.wrapper', $newLabel);
		
		$section->addUI('user.label.badge', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'user.label.badge',
									'title' => __halotext('User Badge Labels'),
									'placeholder' => __halotext('Configure labels used for badges'),
									'value' => HALOConfig::get('user.label.badge'),
									'options' => $allOptions)))
						// ->addUI('user.label.status', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'user.label.status',
									// 'title' => __halotext('Status Label Group Code'),
									// 'placeholder' => __halotext('Enter Label Group Code for Status Label'),
									// 'value' => HALOConfig::get('user.label.status'))))
						// ->addUI('user.label.badge', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'user.label.badge',
									// 'title' => __halotext('Badge Label Group Code'),
									// 'placeholder' => __halotext('Enter Label Group Code for Badge Label'),
									// 'value' => HALOConfig::get('user.label.badge'))))
        ;
        $config->builder->addUI('section@array', $section);

        // User Validation Settings
        $numOptions = array(HALOObject::getInstance(array('value' => 0, 'title' => __halotext('--- Default ---'))));
        for ($i = 1; $i <= 50; $i++) {
            $numOptions[] = HALOObject::getInstance(array('value' => $i, 'title' => $i));
        }

        $section = HALOUIBuilder::getInstance('', 'config.section', array('title' => __halotext('Validation Settings')));
        $section->addUI('user.displayName.minlen', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.displayName.minlen',
            'title' => __halotext('The minumum length of Display Name'),
            'value' => HALOConfig::get('user.displayName.minlen', 0),
            'options' => $numOptions
        )));
        $section->addUI('user.displayName.maxlen', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'user.displayName.maxlen',
            'title' => __halotext('The maximum length of  Display Name'),
            'value' => HALOConfig::get('user.displayName.maxlen', 0),
            'options' => $numOptions
        )));
        $section->addUI('user.displayName.required', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'user.displayName.required',
            'title' => __halotext('Display Name is required'),
            'helptext' => __halotext("Enable this option to force WordPress use Halo's user avatar as user avatar"),
            'options' => array(HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))), HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No')))),
            'value' => HALOConfig::get('user.displayName.required', 0)
        )));

        $config->builder->addUI('section@array', $section);

        $settings->config['user'] = $config;

    }

    /**
     * Load label group assigned to user
     * 
     * @param  string $context 
     * @param  object $rtn     
     */
    public function onLoadLabelGroup($context, $rtn)
    {
                                //only process for HALOShopModel target
        if ($context == 'user') {
			$codes = array_merge((array)HALOConfig::get('user.label.status'), (array)HALOConfig::get('user.label.badge'));
			if(!empty($codes)){
				$groups = HALOLabelGroupModel::whereIn('group_code', $codes)->get();
				if (count($groups)) {
					//try to merge label groups
					if (isset($rtn->labelGroups) && method_exists($rtn->labelGroups, 'merge')) {
						$rtn->labelGroups->merge($groups);
					} else {
						$rtn->labelGroups = $groups;
					}
				}
			}
        }
    }

    /**
     * Load post pending actions
     * 
     * @param  array $data 
     * @return bool       
     */
    public function onLoadPendingActions($data)
    {
        $my = HALOUserModel::getUser();
        if ($my) {
            $actions = array();
            $actionCount = 0;

                                    //check for waiting friend request
            $action = new stdClass();
            $requestCount = $my->pendingFriendRequests->count();
            if ($requestCount) {
                $action->title = __halontext('%s pending friend request', '%s pending friend requests', $requestCount);
                                        //get user status filter
                $link = Cache::rememberForever('user.friendrequest.link', function () {
                    $filters = HALOFilter::getFilterByName('user.listing.type');
                    if (count($filters)) {
                        $statusFilter = $filters->first();
                        return URL::to('?view=home&usec=member&' . $statusFilter->getInputName() . '=pending');
                    } else {
                        return URL::to('?view=home&usec=member&');

                    }
                });
                $action->link = $link;
                $actionCount += $requestCount;
                $actions[] = $action;

                $data->actions = array_merge((isset($data->actions) ? $data->actions : array()), $actions);
                $data->actionCount = isset($data->actionCount) ? $data->actionCount + $actionCount : $actionCount;
            }
        }
        return true;
    }

	public function onChangeAvatar($target, $photo){
		if($target->getContext() == 'user') {
			$target->user->changeAvatar();
		}
	}
	
	public function frontendNotice(){
		$my = HALOUserModel::getUser();
		if($my){
			$currUrl = trim(Request::url());
			//check for avatar
			$checkAvatar = HALOConfig::get('user.requireAvatar', 0);
			$checkProfile = HALOConfig::get('user.requireProfile', 0);
			if($checkAvatar) {
				if(!$my->hasAvatar()) {
					$editAvatarUrl = trim($my->getUrl());
                    $editAvatarUrlHtml = '<a href="javascript:void()" class="" onclick="halo.photo.changeAvatar(\'user\',\'' . $my->user_id . '\')">' . __halotext('change your avatar') . '</a>';
						if($checkAvatar == 2) {
                            echo HALOUIBuilder::getInstance('', 'alert', array('message' => sprintf(__halotext("You haven't updated your avatar yet. Please %s here"), $editAvatarUrlHtml),
                                                                        'type' => 'danger'))->fetch();
                            if($currUrl != $editAvatarUrl) {
							echo "<script> location.href = '" . $editAvatarUrl . "';</script>";
                            }
						}
                        if ($checkAvatar == 1) {
                            echo HALOUIBuilder::getInstance('', 'alert', array('message' => sprintf(__halotext("You haven't updated your avatar yet. Please %s here"), $editAvatarUrlHtml),
                                                                        'type' => 'warning'))->fetch();
                        }
				}
			} else if($checkProfile) {
			//check for required fields
				if(!$my->isProfileCompleted()) {
					$editProfileUrl = trim($my->getEditUrl());
                    $editProfileUrlHtml = '<a href="' . $editProfileUrl .'" class="">' . __halotext('complete your profile') . '</a>';
						if($checkProfile == 2) {
                            echo HALOUIBuilder::getInstance('', 'alert', array('message' => sprintf(__halotext("You haven't completed your profile yet. Please %s here"), $editProfileUrlHtml),
                                                                        'type' => 'danger'))->fetch();
                            if ($currUrl != $editProfileUrl) {
							    echo "<script> location.href = '" . $editProfileUrl . "';</script>";
                            }
						}
                        if ($checkProfile == 1) {
					        echo HALOUIBuilder::getInstance('', 'alert', array('message' => sprintf(__halotext("You haven't completed your profile yet. Please %s here"), $editProfileUrlHtml),
																		'type' => 'warning'))->fetch();
                        }
				}
			}			
		}
	}
}
