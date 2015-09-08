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

class NotificationController extends BaseController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a single conv
     *
     * @return View
     */
	public function index() {
		return $this->getShow();
	}
	
    /**
     * Show a single conv
     *
     * @return View
     */
    public function getShow()
    {
        $user = HALOUserModel::getUser();

        if (!$user) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Login required'));
            return View::make('site/error', compact('title', 'messages'));
        }

        $this->ajaxLoadNotificationFullview(array());
        $title = __halotext('Notification');
        return View::make('site/notification/index', compact('title', 'user'));
    }

    /**
     * load notification
     *
     * @param  array  $postData
     * @return JSON
     */
    public function ajaxLoadNotificationFullview($postData = array())
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to view your notification'));
        }
        $postData['limit'] = HALO_NOTIFICATION_LIMIT_DISPLAY;
        Input::merge($postData);
        //prepare params for pagination ajax loading
        Input::merge(array('com' => 'notification', 'func' => 'LoadNotificationFullview'));

        // Update last clicked notification icon of current user
        $my->setParams('last_clicked_notify', Carbon::now()->toDateTimeString());
        $my->save();
        // Update notification counter on navbar right of view
        $this->ContentUpdater(null);

        $notifications = HALONotificationAPI::getNotification(array('user_id' => $my->id));

        $content = '';
        if (count($notifications)) {
            foreach ($notifications as $row) {
                if (isset($row->notification)) {
                    $content .= $row->notification->render($row->status);
                } else {
                    //invalid notification, just delete it
                    $row->delete();
                }
            }
        }
        if (empty($content)) {
            //no avaialble notification
            $content .= '<div class="text-center">' . __halotext('No New Notification') . '</div>';
        }
        //update zone
        //$wrapperHtml = HALOUIBuilder::getInstance('','notification.layout',array('content'=>$content))->fetch();
        $wrapperHtml = $content;
        HALOResponse::setZoneContent('halo-notification-content-body', $wrapperHtml, HALO_CONTENT_INSERT_MODE);

        //pagination

        $paginationHtml = $notifications->links('ui.pagination_more')->__toString();

        HALOResponse::setZonePagination('halo-notification-content-body', $paginationHtml);

        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to load older notification
     *
     * @param  array  $postData
     * @return JSON
     */
    public function ajaxLoadNotification($postData = array())
    {

        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to view your notification'));
        }
        $postData['limit'] = HALO_NOTIFICATION_LIMIT_DISPLAY;
        Input::merge($postData);

        $notifications = HALONotificationAPI::getNotification(array('user_id' => $my->id));

        // Update the last clicking notify for logged in user
        $my->setParams('last_clicked_notify', Carbon::now()->toDateTimeString());
        $my->save();

        // Update notification counter on navbar right of view
        $this->ContentUpdater(null);

        $content = '';
        if (count($notifications)) {
            foreach ($notifications as $row) {
                if (isset($row->notification)) {
                    $content .= $row->notification->render($row->status);
                } else {
                    //invalid notification, just delete it
                    $row->delete();
                }
            }
        }
        if (empty($content)) {
            //no avaialble notification
            $content .= '<div class="text-center">' . __halotext('No New Notification') . '</div>';
        }
        //update zone
        //@rule: only update wrapper if the pg is 1 or undefined
        if (!isset($postData['pg']) || $postData['pg'] == 1) {
            $wrapperHtml = HALOUIBuilder::getInstance('', 'notification.layout', array('content' => ''))->fetch();
            HALOResponse::updateZone($wrapperHtml);
        }
        HALOResponse::insertZone('halo-notification-content-nav', $content);

        //pagination

        //$paginationHtml = $notifications->links('ui.pagination_more')->__toString();

        //HALOResponse::addScriptCall('halo.util.addZonePagination','halo-notification-content',$paginationHtml);

        return HALOResponse::sendResponse();

    }

    /**
     * ajax handler to mark all notification as read
     *
     * @param  int $notifId
     * @return JSON
     */
    public function ajaxMarkAsRead($notifId = null)
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to edit your notification settings'));
        }
        if (empty($notifId)) {
            //mark all notification as read
            DB::table('halo_notification_receivers')
                ->where('user_id', '=', $my->id)
                ->update(array('status' => 1));
        } else {
            //mark a single notification as read
            DB::table('halo_notification_receivers')
                ->where('user_id', '=', $my->id)
                ->where('notification_id', '=', $notifId)
                ->update(array('status' => 1));
        }
        //update the notification counter
        $this->ContentUpdater(null);

        //redirect to the ajaxLoadNotification to update the notification popup
        //return $this->ajaxLoadNotification();
        return HALOResponse::addScriptCall('halo.notification.setReadState', $notifId, 1)->sendResponse();

    }

    /**
     * ajax handler to hide/remove a notification
     *
     * @param  int $notifId
     * @return JSON
     */
    public function ajaxHide($notifId = null)
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to edit your notification settings'));
        }

        //mark a single notification as read
        DB::table('halo_notification_receivers')
            ->where('user_id', '=', $my->id)
            ->where('notification_id', '=', $notifId)
            ->delete();

        //update the notification counter
        $this->ContentUpdater(null);

        //get notifications
        $notifications = HALONotificationAPI::getNotification(array('user_id' => $my->id));
        //if count notifications < 0, show no new notification
        if (!count($notifications)) {
            $content = '<div class="text-center">' . __halotext('No New Notification') . '</div>';
            HALOResponse::insertZone('halo-notification-content-body', $content);
        }

        //redirect to the ajaxLoadNotification to update the notification popup
        //return $this->ajaxLoadNotification();
        HALOResponse::addScriptCall('halo.notification.remove', $notifId);

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to display  a notification content
     *
     * @param  int $notifId
     * @return JSON
     */
    public function ajaxShowNotification($notifId)
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to view your notification'));
        }

        $notification = HALONotificationModel::find($notifId);

        //silently skip null notification
        if (!$notification) {
            return HALOResponse::sendResponse();
        }

        //mark the notification as read
        $receiver = $notification->receivers()->where('user_id', '=', $my->id)->get()->first();

        if (!$receiver) {
            //silently reject showing this notification if receiever list is empty
            HALOResponse::addScriptCall('halo.notification.remove', $notifId);
            //HALOResponse::addMessage(HALOError::failed(__halotext('You do not have permission to view this notification')));
            return HALOResponse::sendResponse();
        }
        $receiver->status = 1;//mark the notification as read
        $receiver->save();

        //update the notification counter
        $this->ContentUpdater(null);

        //get target notification action
        $noTargetActions = array('friend.request');
        if (!in_array($notification->action, $noTargetActions)) {
            $notification->getTargetAction();
        }

        //mark the notification as read
        HALOResponse::addScriptCall('halo.notification.setReadState', $notifId, 1);
        return HALOResponse::sendResponse();
    }

    /**
     * callback for notification updater
     *
     * @param  bool
     */
    public function ContentUpdater($filterForm)
    {
        //notification update
        $notifCount = HALONotificationAPI::getNewNotifyCount();
        $html = HALOUIBuilder::getInstance('', 'notification.counter', array('counter' => $notifCount, 'zone' => 'notification-counter'))->fetch();
        HALOResponse::updateZone($html);

        //pending action update
        $data = new stdClass();
        Event::fire('system.onLoadPendingActions', array(&$data));
        $count = isset($data->actionCount) ? $data->actionCount : 0;
        $actions = isset($data->actions) ? $data->actions : array();
        $html = HALOUIBuilder::getInstance('', 'module.pending', array('count' => $count, 'actions' => $actions))->fetch();
        HALOResponse::updateZone($html);

        return true;

    }

    /**
     * ajax handler to show notification settings
     *
     * @return JSON
     */
    public function ajaxShowSettings()
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to edit your notification settings'));
        }
        //call api to load notification settings
        $settings = HALONotificationAPI::loadNotificationSettings($my);
        $builder = HALOUIBuilder::getInstance('', 'notification.settings', array('settings' => $settings));

        $content = $builder->fetch();
        $actionSave = HALOPopupHelper::getAction(array("name" => __halotext("Save"), "onclick" => "halo.notification.saveSettings()", "icon" => "check", 'class' => 'halo-btn-primary'));
        $title = __halotext("Notification Settings");
        HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm');
        return HALOResponse::sendResponse();

    }
    /**
     * ajax handler to save notification settings
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSaveSettings($postData)
    {
        //check permission
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Please login to edit your notification settings'));
        }
        //call api to load notification settings
        $settings = $postData['notif'];

        $my->setParams('notif', $settings);
        $my->save();
        HALOResponse::addScriptCall('halo.popup.close');
        //update the notification counter
        $this->ContentUpdater(null);

        return HALOResponse::sendResponse();

    }

    /**
     * ajax request to show fullview page
     *
     * @return JSON
     */
    public function ajaxShowFullview()
    {
        HALOResponse::redirect(URL::to('?view=notification&task=show'));
        return HALOResponse::sendResponse();
    }
}
