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

class HALONotificationModel extends HALOModel
{
    protected $table = 'halo_notification';

    protected $fillable = array('actors', 'action', 'context', 'target_id');

    private $validator = null;

    private $_params = null;

    /**
     * Get validate rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('actors' => 'required', 'action' => 'required', 'context' => 'required', 'target_id' => 'required');

    }

    //////////////////////////////////// Define Relationships /////////////////////////
   
    /**
     * HALONotificationModel, HALONotificationreceiverModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function receivers()
    {
        return $this->hasMany('HALONotificationreceiverModel', 'notification_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Return target model for this notification.
     * if the target is not exists->consider notificationas elephant notification, just delete it
     * 
     * @return HALOModel
     */
    public function getTarget()
    {
        $target = HALOModel::getCachedModel($this->context, $this->target_id);
        if (!$target) {
            //could not find the comment target. Treat the target is invalid, delete the notification
            $this->delete();
            return false;
        }
        return $target;

    }

    /**
     * Return html of this notification
     * 
     * @param  string $status 
     * @return string         
     */
    public function render($status)
    {

        $html = '';
        $actors = explode(',', $this->actors);
        if (!empty($actors)) {
            //treat the first actor in actors list as notification avatar
            $actorId = $actors[0];
            $actor = HALOUserModel::getUser($actorId);
            $this->actor = $actor;
            if (!$this->actor) {
                return '';
            }

            //trigger event to render notification attachment
            $event = Event::fire('notification.onRender', array(&$this));

            if (empty($event)) {
                return;
            }

            $html = HALOUIBuilder::getInstance('notification' . $this->id, 'notification.notification_layout', array('notification' => $this, 'status' => $status)
            )->fetch();
        }

        return $html;
    }

	/**
	 * Get ActorLinks 
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getActorLinks($class = '')
	{
		$actorIds = array();
		if($this->actor_list) {
			return HALOActorListHelper::getActorLinksFromColumn($this, 'actor_list', $class);
		} else {
			return $this->actor->getDisplayLink($class);
		}
	}

	/**
	 * Get actors
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getDisplayActor()
	{
		$actorIds = array();
		if($this->actor_list) {
			return HALOActorListHelper::getActorListFromColumn($this, 'actor_list');
		} else {
			return $this->actor;
		}
	}

    /**
     * Return the onclick action for this notification
     * 
     * @return true
     */
    public function getTargetAction()
    {
        $target = $this->getTarget();
        if ($target && method_exists($target, 'getNotificationTargetAction')) {
            $target->getNotificationTargetAction();
            return true;
        }
    }

    /**
     * Override nofication delete function to delete its receivers relationship
     * 
     * @see  Illuminate\Database\Eloquent\Model::delete()
     * @return bool|null 
     */
    public function delete()
    {
        // delete all related receivers
        $this->receivers()->delete();

        return parent::delete();

    }

    /**
     * Return a list of actor for this notification
     * 
     * @return array
     */
    public function getActorIds()
    {
        return explode(',', $this->actors);
    }

}
