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

//define all application level constans here

define('HALO_COMMENT_LIMIT_DISPLAY', HALOConfig::get('global.commentDisplayLimit'));
define('HALO_ACTIVITY_LIMIT_DISPLAY', HALOConfig::get('global.activityDisplayLimit'));
define('HALO_NOTIFICATION_LIMIT_DISPLAY', HALOConfig::get('global.notificationDisplayLimit'));
define('HALO_USER_LIMIT_DISPLAY', HALOConfig::get('user.defaultDisplayLimit'));

define('HALO_PHOTO_AVATAR_SIZE', HALOConfig::get('photo.avatarSize', 80));
define('HALO_PHOTO_THUMB_SIZE', HALOConfig::get('photo.thumbSize', 40));
define('HALO_PHOTO_SMALL_SIZE', HALOConfig::get('photo.smallSize', 40));
define('HALO_PHOTO_LIST_SIZE', HALOConfig::get('photo.listSize', 24));
define('HALO_PHOTO_COVER_SIZE', HALOConfig::get('photo.coverSize', 1024));
define('HALO_PHOTO_SMALLCOVER_SIZE', HALOConfig::get('photo.coverSmall', 320));

define('HALO_OG_IMAGE_WIDTH', 1200);
define('HALO_OG_IMAGE_HEIGHT', 630);
define('HALO_OG_IMAGE_RATIO', HALO_OG_IMAGE_HEIGHT / HALO_OG_IMAGE_WIDTH);


define('HALO_UNIT_TITLE_IND', 0);
define('HALO_UNIT_RATE_IND', 1);

define('HALO_MEDIA_TEMPORARY_FILE_LIFETIME', 2);//2 hours

// notification constants
define('HALO_NOTIF_TYPE_E', 1);
define('HALO_NOTIF_TYPE_I', 2);
define('HALO_NOTIF_TYPE_A', 3);

// connection constants
define('HALO_CONNECTION_ROLE_FRIEND', 0);
define('HALO_CONNECTION_STATUS_REQUESTING', 0);
define('HALO_CONNECTION_STATUS_CONNECTED', 1);
define('HALO_CONNECTION_STATUS_REJECTED', 2);
define('HALO_CONNECTION_STATUS_BLOCKED', 3);

// oauth constants
define('HALO_OAUTH_CONSUMER_FB', 0);
define('HALO_OAUTH_CONSUMER_GG', 1);

// mailqueue status constants
define('HALO_MAILQUEUE_PENDING', 0);
define('HALO_MAILQUEUE_SENT', 1);
define('HALO_MAILQUEUE_BLOCKED', 2);

//grouping
define('HALO_GROUPING_PERIOD_HOURS', 3);

// online users status constants
define('HALO_ONLINE_USER_ACTIVE', 0);
define('HALO_ONLINE_USER_IDLE', 1);
define('HALO_ONLINE_IDLE_TIMER', 10);//10 minutes

define('HALO_PROFILE_DEFAULT_USER_ID', 10);
define('HALO_PROFILE_DEFAULT_CATEGORY_ID', 11);

define('HALO_CONTENT_UPDATE_MODE', 1);
define('HALO_CONTENT_INSERT_MODE', 2);

define('HALO_LOCALE_DOMAIN', 'haloDomain');

if (!function_exists('__halotext')) {
    function __halotext($msg, $domain = HALO_LOCALE_DOMAIN)
    {
        if ($domain == HALO_LOCALE_DOMAIN) {
            return gettext($msg);
        } else {
            return dcgettext($domain, $msg, LC_ALL);
        }
        //
    }
}

if (!function_exists('__halontext')) {
    function __halontext($msg1, $msg2, $n, $domain = HALO_LOCALE_DOMAIN)
    {
        if ($domain == HALO_LOCALE_DOMAIN) {
            return sprintf(ngettext($msg1, $msg2, $n), $n);
        } else {
            return sprintf(dcngettext($domain, $msg1, $msg2, $n, LC_ALL), $n);
        }
        //
    }
}

/* for finfo extension disabled */
if(!function_exists('finfo_buffer')) {
	define('FILEINFO_MIME_TYPE', 1);
	
	function finfo_open($options = '', $magic_file = '') {
		return null;
	}

	function finfo_close($info) {
		return true;
	}

	function finfo_file($finfo , $filename = null, int $options = null, $context = null ) {
		$mime = 'application/octet-stream';
		//prefer to detect image file type
		if(function_exists('getimagesize')) {
			$size = getimagesize($filename);
			if($size && $size['mime']) {
				$mime = $size['mime'];
			}
		}
		return $mime;
	}

	function finfo_buffer($finfo , $string = null, $options = null, $context = NULL ){
		$mime = 'application/octet-stream';
		if(is_string($string)) {
			if(file_exists($string)) {
				$mime = 'text/plain';
			}
		} else if(function_exists('getimagesizefromstring')) {
			$size = getimagesizefromstring($string);
			if($size && $size['mime']) {
				$mime = $size['mime'];
			}
		}
		return $mime;
	}
}

/* system init set up */

/****** temporary solution ***********/
function configLocale()
{
	$langStr = HALOAssetHelper::getLanguageInfo();
	putenv("LANG=" . $langStr);
	setlocale(LC_ALL, $langStr . ".utf8");
	Cache::rememberForever('locale_cache', function() {
		bindtextdomain(HALO_LOCALE_DOMAIN, app_path() . DIRECTORY_SEPARATOR . 'locale/nocache'); //force to clear cache
		return 1;
	});

	bindtextdomain(HALO_LOCALE_DOMAIN, app_path() . DIRECTORY_SEPARATOR . 'locale');
}

function extendValidator()
{
    Validator::extend('minval', function ($field, $value, $parameters) {
        return isset($parameters[0]) && (float) $value >= (float) $parameters[0];
    });
    Validator::replacer('minval', function ($message, $attribute, $rule, $parameters) {
        return sprintf(__halotext('The %s must be greater then %s.'), $attribute, $parameters[0]);
    });
    Validator::extend('maxval', function ($field, $value, $parameters) {
        return isset($parameters[0]) && (float) $value <= (float) $parameters[0];
    });
    Validator::replacer('maxval', function ($message, $attribute, $rule, $parameters) {
        return sprintf(__halotext('The %s must be smaller then %s.'), $attribute, $parameters[0]);
    });

    // Validate email list
    // example: $emails = 'john@abc.com,ken@abc.com,henry@abc.com'
    Validator::extend('emails', function ($field, $value, $parameters) {
        // if (empty(trim($value))) {return false;}
        $emails = explode(',', $value);
        foreach ($emails as $email) {
            $valiator = Validator::make(
                array('email' => $email),
                array('email' => 'required|email')
            );
            if ($valiator->fails()) {
                return false;
            }
        }
        return true;
    });
    Validator::replacer('emails', function ($message, $attribute, $rule, $parameters) {
        return sprintf(__halotext("The %s email list format is invalid."), $attribute);
    });

    Validator::extend('timezone', function ($field, $value, $parameters) {
		$arr = HALOUtilHelper::getTimeZoneArray();
		return in_array($value, $arr);
    });
    Validator::replacer('timezone', function ($message, $attribute, $rule, $parameters) {
        return sprintf(__halotext('The %s must be an valid timezone.'), $attribute);
    });

	
}

function halo_init_enviroment()
{
    configLocale();
    extendValidator();
}

halo_init_enviroment();

/****** temporary solution ***********/
class SystemEventHandler
{

	/**
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        //crontask for photo
        $events->listen('system.onRunningCron', 'SystemEventHandler@onRunningCronPhoto');
        //crontask for file
        $events->listen('system.onRunningCron', 'SystemEventHandler@onRunningCronFile');
        //crontask for email
        $events->listen('system.onRunningCron', 'SystemEventHandler@onRunningCronEmail');
        //crontask for onlineusers
        $events->listen('system.onRunningCron', 'SystemEventHandler@onRunningCronOnlineuser');

        //crontask for labels
        $events->listen('system.onRunningCron', 'SystemEventHandler@onRunningCronLabel');

        //social share settings
        $events->listen('social.onLoadSettings', 'SystemEventHandler@onLoadSocialSetttings');

        //content rendered
        $events->listen('system.onContentRendered', 'SystemEventHandler@onContentRendered');

        //ajax response
        $events->listen('system.onAjaxResponse', 'SystemEventHandler@onAjaxResponse');

        //system info
        $events->listen('system.onLoadShortInfo', 'SystemEventHandler@onLoadSiteInfo');
        $events->listen('system.onDisplaySiteInfo', 'SystemEventHandler@onDisplaySiteInfo');

        $events->listen('system.onDisplayPendingActions', 'SystemEventHandler@onDisplayPendingActions');

        //module events
        $events->listen('module.onLoadModule', 'SystemEventHandler@onLoadModule');

        $events->listen('report.onAfterAdding', 'SystemEventHandler@onCreateReport');

        //event handler for getting notification settings
        $events->listen('notification.onLoadingSettings', 'SystemEventHandler@onNotificationLoading');

        //event handler for notification rendering
        $events->listen('notification.onRender', 'SystemEventHandler@onNotificationRender');

        //configuration settings
        $events->listen('config.loadSettings', 'SystemEventHandler@onLoadConfigurationSettings');
		
		//permission source trigger
		$events->listen('auth.getPermissionSource', function($permission) {
			$corePermission = array('backend.view', 'site.view', 'user.view', 'user.edit', 'user.delete', 'comment.view', 'comment.edit',
									'comment.create', 'comment.delete', 'activity.create', 'activity.edit', 'activity.delete',
									'user.changeProfile', 'review.create', 'review.edit', 'review.approve', 'review.delete');
			if(in_array($permission->name, $corePermission)) {
				$permission->setSourceName('Halo Core');
			}
		});
    }

    /**
     * execute photo crontask
     * 
     * @param  string $section 
     * @param  array $data    
     * @return bool          
     */
    public function onRunningCronPhoto($section, $data)
    {
        if (!empty($section) && $section != 'photo') {
            return true;
        }

        $nRecord = HALOConfig::get('system.recordPerCron', 100);

        $tmpFileLifeTime = HALO_MEDIA_TEMPORARY_FILE_LIFETIME;//keep temporary file for 2 hours
        $now = Carbon::now();
        $aliveDate = ($tmpFileLifeTime) ? $now->subHour($tmpFileLifeTime)->copy() : $now;

        //grab all activiti data
        $photos = HALOPhotoModel::with('comments', 'likes')
            ->where('status', '=', HALO_MEDIA_STAT_TEMP)
            ->where('created_at', '<', $aliveDate)
            ->orderBy('id')
            ->take($nRecord)
            ->get();
            //loop through photos to delete physical files
        $totalCount = count($photos);
        $deletedCount = 0;
        foreach ($photos as $photo) {
            if ($photo->deletePhysicalFile()) {
                $deletedCount++;
            }
        }
            //delete photo from db
        if ($totalCount) {
            HALOPhotoModel::destroy($photos->modelKeys());
        }

        $data->messages[] = 'Running Photo Crontask ...';
        $data->messages[] = "Deleted  $deletedCount/$totalCount photo(s)";

        return true;
    }

    /**
     * Execute file crontesk
     * 
     * @param  string $section 
     * @param  array $data    
     * @return bool
     */
    public function onRunningCronFile($section, $data)
    {
        if (!empty($section) && $section != 'file') {
            return true;
        }

        $nRecord = HALOConfig::get('system.recordPerCron', 100);

        $tmpFileLifeTime = HALO_MEDIA_TEMPORARY_FILE_LIFETIME;    //keep temporary file for 2 hours
        $now = Carbon::now();
        $aliveDate = ($tmpFileLifeTime) ? $now->subHour($tmpFileLifeTime)->copy() : $now;

            //grab all activiti data
        $files = HALOFileModel::where('status', '=', HALO_MEDIA_STAT_TEMP)
                ->where('created_at', '<', $aliveDate)
                ->orderBy('id')
                ->take($nRecord)
                ->get();
                //loop through photos to delete physical files
        $totalCount = count($files);
        $deletedCount = 0;
        foreach ($files as $file) {
            if ($file->deletePhysicalFile()) {
                $deletedCount++;
            }
        }
                //delete file from db
        if ($totalCount) {
            HALOFileModel::destroy($files->modelKeys());
        }

        $data->messages[] = 'Running File Crontask ...';
        $data->messages[] = "Deleted  $deletedCount/$totalCount file(s)";

        return true;
    }

    /**
     * Execute email crontask
     * 
     * @param  string  $section 
     * @param  array  $data    
     * @param  integer $repeat  
     * @return boll           
     */
    public function onRunningCronEmail($section, $data, $repeat = 0)
    {
        if (!empty($section) && $section != 'email') {
            return true;
        }

        $nRecordsPerCron = HALOConfig::get('system.recordPerCron', 100);
                //email sending take high latency. So just sent maximum 10 emails at a time
        $nRecord = 10;
        $now = Carbon::now();

        if ($repeat == 0) {
                    //clean up sent emails, just applied for the first execution time
            DB::table('halo_mailqueue')->where('status', HALO_MAILQUEUE_SENT)
                                     ->where('scheduled', '<', $now->copy()->subDays(3))
            ->delete();
                //delete duplicate emails. ( duplicate emails are emails having same 'to' and 'source_str')
			/*
            DB::table('halo_mailqueue')->where('status', HALO_MAILQUEUE_PENDING)
                                     ->whereNotNull('source_str')
                                     ->whereNotIn('id', function ($query) {
													$query->from('halo_mailqueue')
														->whereNotNull('source_str')
														->groupBy('to')
														->groupBy('source_str')
														->select(DB::raw('max(id)'));
												})
									->delete();
			*/
        }
            //grab pending emails
        $emails = HALOMailqueueModel::where('status', HALO_MAILQUEUE_PENDING)
                ->where('scheduled', '<', $now)
                ->orderBy('scheduled', 'asc')
                ->take($nRecord)
                ->get();
                //loop through email to send them
        $totalCount = count($emails);

        $sentCount = 0;
        $sentEmails = array();
        foreach ($emails as $email) {
            if ($email->send()) {
                $sentCount++;
                $sentEmails[] = $email->id;
            }
        }
                //update the mailqueue status
        if (!empty($sentEmails)) {
            DB::table('halo_mailqueue')->whereIn('id', $sentEmails)
                                     ->update(array('status' => HALO_MAILQUEUE_SENT));
        }

        if (!$repeat || $sentCount) {
            $data->messages[] = 'Running Email Crontask ...';
            $data->messages[] = "Sent  $sentCount/$totalCount email(s)";
        }
        $repeat++;
        if ($sentCount && (($nRecord * $repeat) < $nRecordsPerCron)) {
                    //recursive
            $this->onRunningCronEmail($section, $data, $repeat);
        }
        return true;
    }

    /**
     * Execute online user crontask
     * 
     * @param  string $section 
     * @param  array $data    
     * @return bool          
     */
    public function onRunningCronOnlineuser($section, $data)
    {
        if (!empty($section) && $section != 'onlineuser') {
            return true;
        }

        $updateInterval = HALOConfig::get('global.updateInterval');        //in miliseconds

                //convert to minute
        $updateInterval = $updateInterval / (60 * 1000);
        $updateInterval = ($updateInterval > HALO_ONLINE_IDLE_TIMER) ? $updateInterval : HALO_ONLINE_IDLE_TIMER;

        $now = Carbon::now();

                //mark idle users
        HALOOnlineuserModel::where('status', '=', HALO_ONLINE_USER_ACTIVE)
                    ->where('updated_at', '<', Carbon::now()->subMinutes($updateInterval))
                    ->update(array('status' => HALO_ONLINE_USER_IDLE, 'updated_at' => Carbon::now()));
                    //treat idle users as offline
        HALOOnlineuserModel::where('status', '=', HALO_ONLINE_USER_IDLE)
                        ->where('updated_at', '<', Carbon::now()->subMinutes($updateInterval * 2))            //idle users in 2  * updateInterval will be treated as offline.
                        ->delete();
        return true;
    }

    /**
     * Execute online user crontask
     * 
     * @param  string $section 
     * @param  array $data    
     * @return bool
     */
    public function onRunningCronLabel($section, $data)
    {
        if (!empty($section) && $section != 'label') {
            return true;
        }

        $data->messages[] = 'Running Label Crontask ...';
        HALOLabelAPI::updateTimerLabels();
        return true;
    }

    /**
     * Load social share setting
     * 
     * @param  HALOObject $meta 
     * @return bool
     */
    public function onLoadSocialSetttings(HALOObject $meta)
    {
        $id = uniqid(rand());

        $meta->setNsArray('social._meta', array("title" => __halotext("Integrations"),
            "icon" => "",
            "help" => '',
        ));
		//Facebook
        $meta->setNsArray('social.facebook._meta', array("title" => __halotext("Facebook"),
            "icon" => "",
            "help" => '',
        ));
        $meta->setNsArray('social.facebook.shareEnable', array("title" => __halotext("Enable Facebook Share"),
            "_type" => "form.radio",
            "help" => '',
            'options' => array(array('value' => '1', 'title' => __halotext('Yes')),
                array('value' => '0', 'title' => __halotext('No')),
            ),
            'value' => HALOConfig::get('social.facebook.shareEnable', 0),
            "name" => "social.facebook.shareEnable"));

        $meta->setNsArray('social.facebook.oauthClientId', array("title" => __halotext("Facebook Client ID"),
            "_type" => "form.text",
            "help" => '',
            'value' => HALOConfig::get('social.facebook.oauthClientId'),
            "name" => "social.facebook.oauthClientId"));

        $meta->setNsArray('social.facebook.oauthClientSecret', array("title" => __halotext("Facebook Client Secret"),
            "_type" => "form.text",
            "help" => '',
            'value' => HALOConfig::get('social.facebook.oauthClientSecret'),
            "name" => "social.facebook.oauthClientSecret"));

		$editScopeAuth = HALOConfig::isDev()?'form.text':'form.hidden';
		
        $meta->setNsArray('social.facebook.oauthScope', array("title" => __halotext("Facebook Scope"),
            "_type" => $editScopeAuth,
            "help" => '',
            'value' => HALOConfig::get('social.facebook.oauthScope', 'email'),
            "name" => "social.facebook.oauthScope"));

				//for display purpose only, set the meta value only
        $meta->setNsArray('social.facebook.shareOptions', array('countFn' => 'getFacebookCount', 'name' => 'facebook' . $id, 'html' => HALOUIBuilder::getInstance('', 'social.share_facebook', array('name' => 'facebook' . $id))->fetch()));
        $meta->setNsArray('social.facebook.oauthOptions', array('handler' => 'UserController::getFacebookLogin'));

                        //for display purpose only, no settings values
                        //$meta->setNsArray('social.email.shareOptions', array('countFn' => 'getEmailCount', 'name' => 'email' . $id, 'html' => HALOUIBuilder::getInstance('', 'social.share_email', array('name' => 'email' . $id))->fetch()));
                        //$meta->setNsArray('social.google.oauthOptions', array('handler' => 'UserController::getGoogleLogin'));

		//Google
        $meta->setNsArray('social.google._meta', array("title" => __halotext("Google"),
            "icon" => "",
            "help" => '',
        ));
        $meta->setNsArray('social.google.shareEnable', array("title" => __halotext("Enable Google Share"),
            "_type" => "form.radio",
            "help" => '',
            'options' => array(array('value' => '1', 'title' => __halotext('Yes')),
                array('value' => '0', 'title' => __halotext('No')),
            ),
            "name" => "social.google.shareEnable",
            'value' => HALOConfig::get('social.google.shareEnable', 0)));

        $meta->setNsArray('social.google.oauthClientId', array("title" => __halotext("Google Client ID"),
            "_type" => "form.text",
            "help" => '',
            'value' => HALOConfig::get('social.google.oauthClientId'),
            "name" => "social.google.oauthClientId"));

        $meta->setNsArray('social.google.oauthClientSecret', array("title" => __halotext("Google Client Secret"),
            "_type" => "form.text",
            "help" => '',
            'value' => HALOConfig::get('social.google.oauthClientSecret'),
            "name" => "social.google.oauthClientSecret"));

        $meta->setNsArray('social.google.oauthScope', array("title" => __halotext("Google Scope"),
            "_type" => $editScopeAuth,
            "help" => '',
            'value' => HALOConfig::get('social.google.oauthScope', 'userinfo_email,userinfo_profile'),
            "name" => "social.google.oauthScope"));
        $meta->setNsArray('social.google.redirect', array("title" => __halotext("Redirect URLs") . ': ' . URL::to('?view=user&task=oauthLogin&uid=Google', array('noSEO' => true)),
            "_type" => "form.alert",
            "help" => '',
            'type' => 'success',
            "name" => "social.google.redirect"));

                        //for display purpose only, no settings values
        $meta->setNsArray('social.google.shareOptions', array('countFn' => 'getGoogleCount', 'name' => 'google' . $id, 'html' => HALOUIBuilder::getInstance('', 'social.share_google', array('name' => 'google' . $id))->fetch()));
        $meta->setNsArray('social.google.oauthOptions', array('handler' => 'UserController::getGoogleLogin'));

        return true;
    }

    /**
     * Event handler to load default notification settings
     * 
     * @param  HALOObject $settings 
     */
    public function onNotificationLoading(HALOObject $settings)
    {
        //report approval notification
        $settings->setNsValue('report.create.i', 1);
        $settings->setNsValue('report.create.e', 1);
        $settings->setNsValue('report.create.d', __halotext('New Report'));
    }

    /**
     * Event handler to generate report approve notification
     * 
     * @param  array $target 
     * @param  array $report 
     * @return HALONotificationAPI         
     */
    public function onCreateReport($target, $report)
    {
    	//prepare data
        $options = array();
        $options['actors'] = $report->actor_id;
        $options['action'] = 'report.create';
        $options['context'] = $report->getContext();
        $options['target_id'] = $report->id;
        $params = HALOParams::getInstance();
        $options['params'] = $params->toString();
        $options['receivers'] = HALOAuth::getUsersByPermissionName('backend.view');
        return HALONotificationAPI::add($options);
    }

    /**
     * Event handler to render notification
     * 
     * @param  string $notification 
     */
    public function onNotificationRender($notification)
    {
        switch ($notification->action) {
            case 'report.create':
                                //prepare data
                $this->renderReportCreate($notification);
                break;
        }
    }

    /**
     * Event handler to render review create notification
     * 
     * @param  string $notification                
     */
    public function renderReportCreate($notification)
    {
        $attachment = new stdClass();

                        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id) || !$target->reportable) {
			//could not find the target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
			//init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
			//wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s reported %s'), $actorsHtml, $target->getDisplayLink());
			//render message as the notification content
            $attachment->content = $target->getMessage();

        }
        $notification->attachment = $attachment;
    }

    /**
     * Handler function on content rendered
     * 
     */
    public function onContentRendered()
    {
                    //append ga tracking to the end of the content
        echo HALOGATracking::getTrackingHtml();

                    //apend online list
        if (HALOConfig::get('global.showOnline', 1) && !empty(HALOOnlineuserModel::$_queuedIds)) {
            echo '<script>
				__haloReady(function() {
					halo.online.setList(' . HALOOnlineuserModel::getOnlineJson() . ');
				});
				</script>';
        }

                    //gg api key
        echo '<div id="halo_ggApiKey" data-gg="' . HALOConfig::get('global.googleAPIKey') . '"></div>';

                    //fb api key
        $settings = HALOUtilHelper::getSocialSettings();
        if ($settings->getNsValue('social.facebook.shareEnable.value', 0)) {
            echo '<div id="halo_fbApiKey" data-fb="' . $settings->getNsValue('social.facebook.oauthClientId.value') . '"></div>';
        }

                    //timestamps
        Session::set('halo_timestamps', Carbon::now());
    }

    /**
     * handler functon on content rendered
     */
    public function onAjaxResponse()
    {
                    //display online status
        if (HALOConfig::get('global.showOnline', 1) && !empty(HALOOnlineuserModel::$_queuedIds)) {
            HALOResponse::addScriptCall('halo.online.updateStatus', HALOOnlineuserModel::getOnlineJson());
        }

                    //ga collecting
        if (HALOConfig::get('global.GAEnable') && !empty(HALOGATracking::$hits)) {
            HALOResponse::addScriptCall('halo.ga.processHits', json_encode(HALOGATracking::$hits));
        }

                    //timestamps
        Session::set('halo_timestamps', Carbon::now());
    }

    /**
     * Loaad site short info
     * 
     * @param  array $site 
     * @return bool
     */
    public function onLoadSiteInfo($site)
    {

                    //stream
        $blankContent = HALOUIBuilder::getInstance('', 'usection', array('actions' => '', 'zone' => 'halo-streams-wrapper'
            , 'filters' => '', 'content' => HALOResponse::getZoneContent('halo-streams-wrapper')
            , 'onChange' => "halo.home.refreshSection('stream')"));
        $shortInfo = HALOObject::getInstance(array('url' => URL::to('?view=home&usec=stream'), 'class' => '', 'title' => __halotext('Stream')
            , 'data' => array('title' => HALOOutputHelper::getPageTitle(__halotext('Homepage'), 'stream'))
            , 'value' => __halotext('Stream')
            , 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.home.displaySection('stream')"
            , 'name' => 'stream'));
		$site->insertShortInfo($shortInfo, 0);
		
                    //member count
        // $memberCount = HALOUserModel::count();
        $memberFilters = HALOFilter::getFilterByName('user.listing.*');
        $blankContent = HALOUIBuilder::getInstance('', 'usection', array('title' => __halotext('Members'), 'actions' => '', 'zone' => 'halo-members-wrapper'
            , 'filters' => $memberFilters, 'content' => HALOResponse::getZoneContent('halo-members-wrapper')
            , 'onChange' => "halo.home.refreshSection('member')"));
        $shortInfo = HALOObject::getInstance(array('url' => URL::to('?view=home&usec=member'), 'class' => '', 'title' => __halotext('Member count')
            , 'data' => array('title' => HALOOutputHelper::getPageTitle(__halotext('Homepage'), 'member'))
            , 'value' => __halotext('Members')
            , 'content' => $blankContent->fetch(), 'onDisplayContent' => "halo.home.displaySection('member')"
            , 'name' => 'member'));
		
		$site->insertShortInfo($shortInfo, 0);
		
        return true;
    }

    /**
     * handler to display user info for a specific section
     * 
     * @param  string $section 
     * @param  array $data    
     */
    public function onDisplaySiteInfo($section, $data)
    {
        switch ($section) {
            case 'member':
                $this->ajaxListMembers($data);
                break;
            case 'stream':
                $this->ajaxListStreams($data);
                break;

        }
    }

    /**
     * Ajax handler to list a site's streams
     * 
     * @param  array $postData 
     */
    private function ajaxListStreams($postData)
    {
        Input::merge($postData);

        $streamFilters = HALOFilter::getFilterByName('activity.home.*');
        $filtersArr = Input::get('filters', array());

                //configure filters value
        foreach ($streamFilters as $filter) {
                    //for other filter, get from http request or session data
            $filter->value = isset($filtersArr[$filter->id]) ? $filtersArr[$filter->id] : '';
        }

        $streamHtml = HALOUIBuilder::getInstance('', 'content', array());
        $my = HALOUserModel::getUser();
        if ($my) {
            $streamHtml->addUI('sharebox', HALOUIBuilder::getInstance('', 'html', array('html' => HALOStatus::render('profile', $my->user_id))));
        }
        $streamHtml->addUI('header', HALOUIBuilder::getInstance('', 'stream.header', array('streamFilters' => $streamFilters)));

        $acts = HALOActivityModel::getActivities(array('filters' => $streamFilters));
        $showLoadMore = true;

        $streamHtml->addUI('body', HALOUIBuilder::getInstance('', 'stream.content', array('acts' => $acts, 'zone' => 'stream_content', 'showLoadMore' => $showLoadMore)));

        HALOResponse::setZoneContent('halo-streams-wrapper', $streamHtml->fetch(), HALO_CONTENT_INSERT_MODE);

		//update page location
        HALOResponse::addZoneScript('halo-streams-wrapper', 'halo.util.setUrl', URL::to('?view=home&usec=stream',URL::cleanParams(Input::only('filters'))));

		//update page title
        HALOResponse::addZoneScript('halo-streams-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title'));

    }

    /**
     * Ajax handler to list a site's members
     * 
     * @param  array $postData       
     */
    private function ajaxListMembers($postData)
    {

        $postData['limit'] = 12;
        Input::merge($postData);
        $query = new HALOUserModel();
		$query->select("halo_users.*");
		
		HALOUserModel::setupFilters();

		//setup filters
        $listingFilters = HALOFilter::getFilterByName('user.listing.*');
            //configure filters value
		HALOUserModel::configureFilters($listingFilters, $query);

        $members = HALOPagination::getData($query);

            //init users
        $userIds = array();
        foreach ($members as $u) {
            $userIds[] = $u->id;
        }

        $cachedUsers = HALOModel::getCachedModel('user', $userIds);
        $memberListHtml = HALOUIBuilder::getInstance('', 'user.list', array('users' => HALOUtilHelper::lazyLoadArray($cachedUsers, array('labels'))
            , 'zone' => 'halo-members-list'))->fetch();

        HALOResponse::setZoneContent('halo-members-wrapper', $memberListHtml, HALO_CONTENT_INSERT_MODE);
            //pagination

        $paginationHtml = $members->links('ui.pagination_auto')->__toString();

        HALOResponse::setZonePagination('halo-members-wrapper', $paginationHtml);

        HALOResponse::addZoneScript('halo-members-wrapper', 'halo.util.updateResultCounter', '.halo-pg-result-' . Str::slug(__halotext('Members')), HALOUIBuilder::getPaginationText($members));

		//update page location
        HALOResponse::addZoneScript('halo-streams-wrapper', 'halo.util.setUrl', URL::to('?view=home&usec=member',URL::cleanParams(Input::only('filters','pg'))));

		//update page title
        HALOResponse::addZoneScript('halo-members-wrapper', 'halo.util.updatePageTitle', HALOOutputHelper::getMetaTags('title'));
    }

    /**
     * Functiion to render html modules
     * 
     * @param  array $position 
     * @param  string $layout   
     */
    public function onLoadModule($position, $layout = 'default')
    {
        switch ($position) {
            case 'head':
                //echo HALOUIBuilder::getInstance('', 'module.head', array())->fetch();
			case 'metatag':
                echo HALOUIBuilder::getInstance('', 'module.metatag', array())->fetch();
                break;			
            case 'nav_bar':
                    //navbar module
                echo $this->renderNavbarModule($layout);
                break;
            case 'banner':
                break;
            case 'top':
                echo $this->renderTopContent($layout);
                break;
            case 'main':
                break;
            case 'sidebar_top':
                //member module
                //echo HALOUIBuilder::getInstance('', 'module.member', array())->fetch();
                //echo HALOUIBuilder::getInstance('', 'poll.googleform', array('zone' => 'poll-siderbar'))->fetch();
                break;
            case 'sidebar_bottom':
                break;
            case 'footer':
                echo Cache::rememberForever('module.footer', function () {
                    return HALOUIBuilder::getInstance('', 'module.footer', array())->fetch();
                });
                echo HALOUIBuilder::getInstance('', 'module.scrollTop', array())->fetch();
                break;
        }
    }

    /**
     * Function to render html for navbar
     * 
     * @param  string $layout 
     */
    private function renderTopContent($layout)
    {
        $html = '';
    //render search for mobile browser
    /*
    if(HALOOutputHelper::isMobile()){
    $filters = HALOFilter::getFilterByName('browse.listing.*');
    return HALOUIBuilder::getInstance('','browse.filter_form_mobile',array('title'=>__halotext('Filters'),'icon'=>'filter','class'=>'halo-mobile-quick-search',
    'filters'=>$filters,'onChange'=>""))->fetch();
    }
     */

    }

    /**
     * Function to render html for navbar
     * 
     * @param  string $layout
     */
    private function renderNavbarModule($layout)
    {

        $mobilePos = HALOOutputHelper::isMobile() ? 'mobile_nav@array' : 'left_nav@array';
        switch ($layout) {
            case 'default':

                $top_bar = HALOUIBuilder::getInstance('top_bar', 'navbar.desktop_bar', array())->addUI('title', HALOUIBuilder::getInstance('', '', array('url' => URL::to('?view=home'), 'title' => 'HALO')));
                if (HALOAuth::hasRole('registered')) {
                    if (HALOAuth::hasRole('admin')) {
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => URL::to('?view=admin'), 'title' => __halotext('Admin Panel'))));
                    }

                    $notifCount = HALONotificationAPI::getNewNotifyCount();
                    if (HALOOutputHelper::isMobile() || HALOOutputHelper::isTablet()) {
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => HALOUIBuilder::icon('bell fa-lg') . HALOUIBuilder::getInstance('', 'notification.counter', array('counter' => $notifCount, 'zone' => 'notification-counter'))->fetch(),
                            'onClick' => "halo.notification.showFullview(this)")));
                        $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => HALOUIBuilder::icon('bell fa-lg') . HALOUIBuilder::getInstance('', 'notification.counter', array('counter' => $notifCount, 'zone' => 'notification-counter'))->fetch(),
                            'onClick' => "halo.notification.showFullview(this)")));
                    } else {
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => URL::to('?view=post&task=edit&uid=0'), 'class' => 'halo-create-post-btn', 'title' => __halotext('Create Post'))));
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => HALOUIBuilder::icon('bell fa-lg') . HALOUIBuilder::getInstance('', 'notification.counter', array('counter' => $notifCount, 'zone' => 'notification-counter'))->fetch(),
                            'class' => 'halo-notification-toggle', 'parentClass' => 'halo-notification-tour', 'onClick' => "halo.notification.list(this,'" . HALOUserModel::getUser()->user_id . "')")));
                        $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => HALOUIBuilder::icon('bell fa-lg') . HALOUIBuilder::getInstance('', 'notification.counter', array('counter' => $notifCount, 'zone' => 'notification-counter'))->fetch(),
                            'class' => 'halo-notification-toggle', 'parentClass' => 'halo-notification-tour-mobile', 'onClick' => "halo.notification.list(this,'" . HALOUserModel::getUser()->user_id . "')")));
                    }
                    $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => HALOUserModel::getUser()->getUrl(), 'title' =>     /*'<img class="halo-top-bar-avatar" src="' . HALOUserModel::getUser()->getThumb() . '" title="' . HALOUserModel::getUser()->getDisplayName() . '" />' . ' ' . */HALOUserModel::getUser()->getDisplayName()))
                        ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => HALOUserModel::getUser()->getUrl(), 'title' => HALOUIBuilder::icon('user') . ' ' . __halotext('Profile'))))
                            ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => HALOUserModel::getUser()->getUrl(array('usec' => 'post')), 'title' => HALOUIBuilder::icon('bullhorn') . ' ' . __halotext('My Posts'))))
                                ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => HALOUserModel::getUser()->getUrl(array('usec' => 'shop')), 'title' => HALOUIBuilder::icon('shopping-cart') . ' ' . __halotext('My Shops'))))
                                    ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => HALOUserModel::getUser()->getUrl(array('usec' => 'bookmark')), 'title' => HALOUIBuilder::icon('bookmark') . ' ' . __halotext('My Bookmarks'))))
                                        ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => Url::to('?view=home'), 'title' => HALOUIBuilder::icon('home') . ' ' . __halotext('Home'))))
                                            ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => Url::to('?view=browse&task=display'), 'title' => HALOUIBuilder::icon('search') . ' ' . __halotext('Search'))))
                                                ->addUI('dropdown@array', HALOUIBuilder::getInstance('', '', array('url' => Url::to('?view=user&task=logout'), 'title' => HALOUIBuilder::icon('power-off') . ' ' . __halotext('Logout'))))
                    );
                    $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('url' => URL::to('?view=post&task=edit&uid=0'), 'class' => 'navbar-mobile-right', 'title' => HALOUIBuilder::icon('pencil-square-o fa-2x'))));
                } else {

                    $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('class' => 'navbar-mobile-right', 'url' => 'user/register', 'title' => __halotext('Sign Up'))));
                    if (HALOOutputHelper::isMobile()) {
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => HALOUIBuilder::icon('sign-in') . ' ' . __halotext('Login'),
                            'onClick' => "halo.user.showLogin()")));
                        $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('class' => 'navbar-mobile-right', 'url' => 'user/login?redirect_url=' . Request::url(),
                            'title' => __halotext('Login'))));
                    } else {
                        $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'javascript',
                            'title' => __halotext('Login'),
                            'class' => 'halo-login-toggle', 'onClick' => "halo.user.showLoginToggle(this)")));
                        $top_bar->addUI('mobile_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'user/login',
                            'title' => __halotext('Login'),
                            'class' => 'navbar-mobile-right')));

                    }
                    $top_bar->addUI('right_nav@array', HALOUIBuilder::getInstance('', '', array('url' => 'user/register', 'title' => __halotext('Sign Up'))));
                }

                return $top_bar->fetch();
        }
    }

    /**
     * Dispplay system pending actions
     * 
     * @return bool
     */
    public function onDisplayPendingActions()
    {
        $html = '';
                                //render pending actions module
        $data = new stdClass();
        Event::fire('system.onLoadPendingActions', array(&$data));
        $count = isset($data->actionCount) ? $data->actionCount : 0;
        $actions = isset($data->actions) ? $data->actions : array();
        $html .= HALOUIBuilder::getInstance('', 'module.pending', array('count' => $count, 'actions' => $actions))->fetch();
        echo $html;
        return true;
    }

    /**
     * On Load Configuration Settings
     * 
     * @param  array $settings 
     */
    public function onLoadConfigurationSettings($settings)
    {
        $lazyloadCfg = new stdClass();
        $lazyloadCfg->title = "Lazyload Settings";
        $lazyloadCfg->icon = "";
        $lazyloadCfg->builder = HALOUIBuilder::getInstance('lazyload_cfg', 'config.group', array('name' => 'lazyload_cfg'));

        $lazyLoadSettings = new stdClass();
                                //default
        $lazyLoadSettings->templates = array();
        Event::fire('system.getLazyLoadSettings', array(&$lazyLoadSettings));
                                //post settings section
        $section = HALOUIBuilder::getInstance('lazyload', 'config.section', array('title' => __halotext('Lazyload templates')));
        foreach ($lazyLoadSettings->templates as $key => $template) {
            $templateName = 'lazyload.template.' . $key;
            $title = isset($template['title']) ? $template['title'] : '';
            $value = isset($template['value']) ? $template['value'] : '';
            if ($title) {
                $section->addUI($templateName, HALOUIBuilder::getInstance('', 'form.text', array('name' => $templateName,
                    'title' => $title . " ({$key})",
                    'value' => HALOConfig::get($templateName, $value),
                )));
            }
        }
        $lazyloadCfg->builder->addUI('section@array', $section);

        // $settings->config['lazyload'] = $lazyloadCfg;
    }

}
