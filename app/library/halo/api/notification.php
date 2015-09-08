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

class HALONotificationAPI 
{

	/*
	add new notificaiton record to database

	 */
	/**
	 * add new notificaiton record to database
	 * @param array  $data
	 * @param bool
	 */
	public static function add($data, $canGroup = true) 
	{
		//save new record to notification table
		$my = HALOUserModel::getUser();
		$default = array('actors' => $my->id,
						'action' => '',
						'context' => '',
						'target_id' => '',
						'receivers' => array(),
						'params' => '');

		$options = array_merge($default, $data);

		//trigger before adding event
		if (Event::fire('notification.onBeforeAdding', array($options), true) === false) {
			//error occur, return
			return false;
		}

		$notification = new HALONotificationModel();
		$notification->bindData($options);
		//validate input
		if ($notification->validate()->fails()) {
			$msg = $notification->getValidator()->messages();
			HALOResponse::addMessage($msg);
			return false;
		}
		if (!isset($data['receivers']) || empty($data['receivers'])) {
			//HALOResponse::addMessage(HALOError::failed(__halotext('Receivers list is empty')));
			//silently skipp the notification
			return false;
		}
		
		$actorId = $default['actors'];
		
		if ($canGroup) {
			//search for the old notiticiation. @rule: only group notification within HALO_GROUPING_PERIOD_HOURS hours

			$oldNotification = HALONotificationModel::where('action', '=', $data['action'])
				->where('context', '=', $data['context'])
				->where('target_id', '=', $data['target_id'])
				->where('created_at', '>', Carbon::now()->subHours(HALO_GROUPING_PERIOD_HOURS))
				->get();
			if (count($oldNotification)) {
				$oldNotification = $oldNotification->first();
					//reuse the notification
				$actorArr = $oldNotification->getActorIds();

					//@rule: always put new actors at the begin of the list
				$actorArr = array_diff($actorArr, array($notification->actors));	//remove the new actors from the list
				array_unshift($actorArr, $notification->actors);	//append to new actors to the list
				$oldNotification->actors = implode(',', $actorArr);
					//use new params
				$oldNotification->params = $notification->params;
					//use old activity as the new created activity
				$notification = $oldNotification;
			}
		}

		//store display actor
		if(HALOActorListHelper::$recentAddedActor) {
			HALOActorListHelper::insertRecentActorToColumn($notification, 'actor_list');
		} else {
			HALOActorListHelper::addActorToColumn($notification, 'actor_list', $actorId);
		}
		
		if (!$notification->save()) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Error occurred while saving the notification')));
			return false;
		}

		$notification->touch();

			//save notification receivers records
		$receivers = (array) $data['receivers'];
		HALOUserModel::init($receivers);
		$receiversArr = array();	//web base notification
		$mailqueueArr = array();	//email notification
			//delete old recievers
		DB::table('halo_notification_receivers')
				->where('notification_id', '=', $notification->id)
				->whereIn('user_id', $receivers)
				->delete();

		foreach ($receivers as $id) {
					//do not self notification
			$user = HALOUserModel::getUser($id);
			if ($id != $my->id && $user && $user->getNotificationSettings($data['action'] . '.i', 0)) {
				$receiver = array();
				$receiver['notification_id'] = $notification->id;
				$receiver['user_id'] = $id;
				$receiver['status'] = 0;
				$receiversArr[] = $receiver;
			}

					//for email notification
			if ($id != $my->id && $user && $user->getNotificationSettings($data['action'] . '.e', 0)) {
				$mailqueue = array();
				$emailContent = new HALOMailqueueModel();
						//fire event to render email subject, plain_msg,html_msg
				$rtn = Event::fire('notification.onRenderEmail', array($notification, $user, &$emailContent));

				if (!$emailContent->validate()->fails()) {
							//convert emailContent to array for bulk inserting
					$mailqueue['to'] = $emailContent->to;
					$mailqueue['subject'] = $emailContent->subject;
					$mailqueue['plain_msg'] = $emailContent->plain_msg;
					$mailqueue['html_msg'] = $emailContent->html_msg;
					$mailqueue['status'] = HALO_MAILQUEUE_PENDING;
					$mailqueue['template'] = $emailContent->template;
					$mailqueue['scheduled'] = Carbon::now();
					$mailqueue['source_str'] = 'notification_' . $notification->id;
					$mailqueue['params'] = '';
					$mailqueueArr[] = $mailqueue;
				} else {
					HALOResponse::addMessage($emailContent->getValidator()->messages());
				}
			}

		}
		if (!empty($mailqueueArr)) {
			if (!HALOMailqueueModel::insert($mailqueueArr)) {
				HALOResponse::addMessage(HALOError::failed(__halotext('Error occurred while queuing the notification email')));
				return false;
			}

		}
		if (!empty($receiversArr)) {
			if (!HALONotificationreceiverModel::insert($receiversArr)) {
				HALOResponse::addMessage(HALOError::failed(__halotext('Error occurred while saving the notification receivers')));
				return false;
			}

				//trigger event for new notification added
			Event::fire('notification.onAfterAdding', array($notification));
			HALOResponse::setData('notification', $notification);
		}

		return true;
	}

	/**
	 * return the number of new notification for a user
	 * 
	 * @param  int $user_id 
	 * @return int
	 */
	public static function getNotificationCount($user_id = null) 
	{
		$user = HALOUserModel::getUser($user_id);
		$query = HALONotificationreceiverModel::where('user_id', '=', $user->id)
		                                                                 ->where('status', '=', 0);

			//respect user's notification setting
		$notifList = $user->getNotificationList(HALO_NOTIF_TYPE_I);

		$query->leftJoin('halo_notification', function ($join) {
			$join->on('halo_notification.id', '=', 'halo_notification_receivers.notification_id');
		});
		if (empty($notifList)) {
			$query->where('action', '=', '');
		} else {
			$query->whereIn('action', $notifList);
		}

		return $query->count();
	}

	/**
	 * return notification records for a user
	 * 
	 * @param  array $options
	 * @return object
	 */
	public static function getNotification($options) 
	{
		$default = array('orderBy' => 'id',
						'orderDir' => 'desc',
						'after' => '',
						'before' => '',
						'user_id' => HALOUserModel::getUser()->id);
		$options = array_merge($default, $options);

		$query = HALONotificationreceiverModel::with('notification')
			->where('user_id', '=', $options['user_id'])
			->orderBy($options['orderBy'], $options['orderDir']);
		if (!empty($options['before'])) {
			$query->where('id', '>', $options['before']);
		}

		if (!empty($options['after'])) {
			$query->where('id', '<', $options['after']);
		}

			//respect user's notification setting
		$user = HALOUserModel::getUser($options['user_id']);
		$notifList = $user->getNotificationList(HALO_NOTIF_TYPE_I);
		$query->leftJoin('halo_notification', function ($join) {
			$join->on('halo_notification.id', '=', 'halo_notification_receivers.notification_id');
		})
			->select('halo_notification_receivers.*');
		if (empty($notifList)) {
			$query->where('action', '=', '');
		} else {
			$query->whereIn('action', $notifList);
		}

		return HALOPagination::getData($query);

	}

	/**
	 * function to delete all notification on a specific target object model
	 * 
	 * @param  object $obj
	 * @return bool
	 */
	public static function deleteAll($obj) 
	{
		try {
			HALONotificationModel::where('context', '=', $obj->getContext())
			                                               ->where('target_id', '=', $obj->id)
				->delete();

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * load notification setting of a user
	 * 
	 * @param  HALOUserModel object $user ---default = null --> load default notification settings
	 * @return array($setting,$description)
	 */
	public static function loadNotificationSettings(HALOUserModel $user = null) 
	{
		static $defaultSettings = null;
		if (is_null($defaultSettings)) {
				//load default settings
			$defaultSettings = new HALOObject();
			Event::fire('notification.onLoadingSettings', array(&$defaultSettings));
		}
		static $globalSettings = null;
		if (is_null($globalSettings)) {
				//load global settings
			$globalSettings = HALOConfig::get('notification.default', new HALOObject());
		}

			//load user settings
		$userSettings = new HALOObject();
		if (!is_null($user)) {
			$userSettings = $user->getParams('notif', new HALOObject());
		}
			//merge settings
		$default = json_decode(json_encode($defaultSettings), true);
		$global = json_decode(json_encode($globalSettings), true);
		$userSet = json_decode(json_encode($userSettings), true);

		$settings = HALOUtilHelper::array_override_recursive($default, $global, $userSet);
		return $settings;
	}

	/**
	 * getNewNotifyCount Get new notification count after the last clicked notify
	 *
	 * @param int|null $userId
	 * @return int
	 * @author Phuong Ngo <fuongit@gmail.com>
	 */
	public static function getNewNotifyCount($userId = null) 
	{
		$user = HALOUserModel::getUser($userId);
		$lastClickedNotify = $user->getParams('last_clicked_notify');
			//respect user's notification setting
		$notifList = $user->getNotificationList(HALO_NOTIF_TYPE_I);

		$query = HALONotificationreceiverModel::where('user_id', '=', $user->id)->where('status', '=', 0);
		$query->leftJoin('halo_notification', function ($join) {
			$join->on('halo_notification.id', '=', 'halo_notification_receivers.notification_id');
		});
		if (!empty($lastClickedNotify)) {
			$query->where('halo_notification.updated_at', '>', $lastClickedNotify);
		}

		if (empty($notifList)) {
			$query->where('action', '=', '');
		} else {
			$query->whereIn('action', $notifList);
		}

		return $query->count();
	}

	/**
	 * add new notification if not exist
	 * 
	 * @param array  $data
	 * @param bool $canGroup
	 * @return  bool
	 */
	public static function addNewOrExist($data, $canGroup = true) 
	{
		$my = HALOUserModel::getUser();
		$receivers = isset($data['receivers']) ? (array) $data['receivers'] : array();
		$actors = isset($data['actors']) ? $data['actors'] : $my->id;
		$actors = is_array($actors) ? implode(',', $actors) : $actors;

		if (empty($receivers)) {
			return false;
		}

		if (!HALONotificationModel::where('action', $data['action'])
			->where('context', $data['context'])
			->where('actors', $actors)
			->where('target_id', $data['target_id'])
			->where('params', $data['params'])
			->whereHas('receivers', function ($q) use ($receivers) {
				$q->whereIn('user_id', $receivers);
			})
			->first()) {
			return self::add($data, $canGroup);

		} else {
			return true;//notification exists
		}
	}
}
