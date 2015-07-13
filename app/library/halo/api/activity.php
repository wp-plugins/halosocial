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

class HALOActivityAPI
{
    protected static $_data = array();

    /**
     * add a new activity
     * 
     * @param array  $data
     * @param bool   $canGroup: boolean to allow to group similiar activity (just different in actor_id) toghether. 
     * Note: just apply for non important activities
     * @return  bool
     */
    public static function add($data, $canGroup = false)
    {
        $my = HALOUserModel::getUser();
		$actor_id = $my?$my->id:0;
        $default = array(
            'actor_id' => $actor_id, //default actor is the current user
            'action' => '',
            'context' => '',
            'tagged_list' => '',
            'target_id' => '',
            'message' => '',
            'access' => 0,
            'location_name' => '',
            'location_lat' => '',
            'location_lng' => '',
            'params' => '');
        $data = array_merge($default, $data);
        //trigger before adding activity event
        if (Event::fire('activity.onBeforeAdding', array($data), true) === false) 
        {
            //error occur, return
            return false;
        }
		
		//check permission
		if(!HALOAuth::can('activity.create')) return false;
		
        //prepare data
        $act = new HALOActivityModel();
        $act->bindData($data);
        $act->actor_id = $data['actor_id'];

        //assign location to $act
        HALOLocationModel::setCurrentLocation($data['location_name'], $data['location_lat'], $data['location_lng']);
        HALOLocationModel::assignCurrentLocation($act);
		
		//store display actor
		if(isset($data['display_context']) && isset($data['display_id'])){
			$displayActor = HALOModel::getCachedModel($data['display_context'], $data['display_id']);
			//must have permission to store display actor
			if($displayActor && HALOAuth::can($data['display_context'] . '.edit', $displayActor)) {
				HALOActorListHelper::addActorToColumn($act, 'actor_list', $act->actor_id, $displayActor);
			}
		}
		
        //validate input
        if ($act->validate()->fails()) {
            $msg = $act->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        }

        if ($canGroup) {
            //search for the existing activity.@rule: only group activity within HALO_GROUPING_PERIOD_HOURS hours
            $oldAct = HALOActivityModel::where('action', '=', $data['action'])
                ->where('context', '=', $data['context'])
                ->where('target_id', '=', $data['target_id'])
                ->where('created_at', '>', Carbon::now()->subHours(HALO_GROUPING_PERIOD_HOURS))
                ->get();
            if (count($oldAct)) {
                $oldAct = $oldAct->first();
                //reuse the act
				HALOActorListHelper::addActorToColumn($oldAct, 'actor_list', $oldAct->actor_id);
				HALOActorListHelper::addActorToColumn($oldAct, 'actor_list', $act->actor_id);
                $oldAct->actor_id = $act->actor_id;
                    //use old activity as the new created activity
                $act = $oldAct;
            }
        }
            //parse link data
        HALOUtilHelper::parseShareLink($data, $act);

		//mark activity created by crontask
		if(defined('HALO_CRONTASK_DEFAULT_USER_ID')) {
			$act->setParams('act_crontask', 1);
		}
		
		//save it
        $act->save();

            //update tag list
        if (!empty($act->tagged_list)) {
            $taggedUsers = explode(',', $act->tagged_list);
            HALOTagAPI::tagUser($act, $taggedUsers);
        }

            //add actor to the follower list of this activity
        HALOFollowAPI::follow($act);

            //process mention on the message
        HALOMentionAPI::process($act->message, $act);

            //process hashtag on the message
        HALOHashTagAPI::process($act->message, $act);

            //trigger after adding activity event
        Event::fire('activity.onAfterAdding', array($act));

            //on activity added, add its reference as HALOResponse data
        HALOResponse::setData('act', $act);
        return true;
    }
    /**
     * set activity data
     * 
     * @param int $key
     * @param string $val
     */
    public static function setData($key, $val)
    {
        self::$_data[$key] = $val;
    }

    /**
     * get activity data
     * 
     * @param  int $key
     * @param  string $default
     * @return mixed
     */
    public static function getData($key, $default = '')
    {
        if (is_null($key)) {
            return self::$_data;
        }

        return isset(self::$_data[$key]) ? self::$_data[$key] : $default;
    }

    /**
     * reset activity data
     * 
     * @return array
     */
    public static function resetData()
    {
        self::$_data = array();
    }
    /**
     * set activity data from array
     * 
     * @param array $arr
     * @return  bool
     */
    public static function setArrayData(array $arr)
    {
        foreach ($arr as $key => $val) {
            self::setData($key, $val);
        }
        return true;
    }

    /**
     * group similar activity occur within $within second into a single activity
     * 
     * @param  object  $act
     * @param  array   $col
     * @param  int $within 
     * @param  string  $callback
     * @return bool          
     */
    public static function group($act, $col = array(), $within = 3600, $callback = null)
    {
            //get current column
        $columns = Schema::getColumnListing('halo_activities');
        $col = array_intersect($col, $columns);
        $time = $act->created_at;
        $time->subSeconds($within);
        $query = HALOActivityModel::where('created_at', '>=', $time);
        foreach ($col as $c) {
            $query->where($c, '=', $act->$c);
        }
        $acts = $query->orderBy('id', 'desc')->get();
        if ($acts->count() > 1) {
            //only group if there is more then 1 matched activity
            if (!is_null($callback)) {
                foreach ($acts as $dupAct) {
                    if ((int) $dupAct->id != $act->id) {
                        call_user_func_array($callback, array(&$act, &$dupAct));
                    }
                    //mark similar act as grouped
                    $dupAct->setGrouped($act->id);
                }
                $act->setGrouped(0);
                $act->save();
            }
        }
        return true;
    }
}
