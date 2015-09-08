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

class HALOOnlineuserModel extends HALOModel
{
	public static $_queuedIds = array();

	protected $table = 'halo_online_users';

	protected $fillable = array('user_id','ip_addr','client_type','b_userAgent','b_name','b_version','b_platform','session','status');
				
	//////////////////////////////////// Define Relationships /////////////////////////
	
	/**
	 * HALOUserModel, HALOOnlineuserModel: one to many
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function user(){
		return $this->belongsTo('HALOUserModel','user_id','id');
	}
	//////////////////////////////////// Define Relationships /////////////////////////

	/**
	 * Function to update the online status 
	 * 
	 * @param  string $user
	 */
	public static function online($user = null){
		$user = is_null($user) ? HALOUserModel::getUser() : $user;
		
		if(!$user) return null;
		
		$ip_addr = Request::getClientIp();
		$client_type = 0;
		$sessionId = Session::getId();
		
		//$online = HALOOnlineuserModel::firstOrNew(array('user_id'=>$user->id,'ip_addr'=>$ip_addr,'client_type'=>$client_type,'session'=>$sessionId));
		$online = HALOOnlineuserModel::firstOrNew(array('user_id'=>$user->id,'ip_addr'=>$ip_addr,'client_type'=>$client_type,'session'=>$sessionId));
		$browser = HALOUtilHelper::getBrowser();
		$online->bindData($browser);
		$online->status = HALO_ONLINE_USER_ACTIVE;
		$online->save();
	}
	
	/**
	 * Function to update the offline status
	 * 
	 * @param  string $user 
	 */
	public static function offline($user = null){
		$user = is_null($user) ? HALOUserModel::getUser() : $user;
		
		if(!$user) return null;
		
		$ip_addr = Request::getClientIp();
		$client_type = 0;
		$online = HALOOnlineuserModel::where('user_id',$user->id)
									->where('ip_addr',$ip_addr)
									->where('client_type',$client_type)
									->delete();
	
	}
	
	/**
	 * Function to update view counter of a target model
	 * 
	 * @param  string $users 
	 */
	public static function checkOnline($users){
		$ids = array();
		if(!is_array($users)) {	$ids[] = $users->id; }
		else {
			foreach($users as $user){
				$ids[] = $user->id;
			}
		}
		$newIds = array_diff($ids,self::$_queuedIds);
		self::$_queuedIds = array_merge(self::$_queuedIds,$newIds);
	}

	/**
	 * Return list of online_user models that are queued up for checking
	 * 
	 * @return mixed
	 */
	public static function getOnlineQueuedModels(){
		$onlineModels = null;
		if(!empty(self::$_queuedIds)){
			$onlineModels = HALOOnlineuserModel::whereIn('user_id',self::$_queuedIds)
											->get();
		}
		return $onlineModels;
	}

	/**
	 * Return json string that list all queued onlined users
	 * 
	 * @return array
	 */
	public static function getOnlineJson(){
		//$onlineModels = self::getOnlineQueuedModels();
		halo_flushOnlineList();
		
		$onlineModels = array();
		$now = Carbon::now();
		$intervalMin = 5;
		if(!empty(self::$_queuedIds)){
			$onlineModels = DB::table('halo_online_users')
								->whereIn('user_id',self::$_queuedIds)
								->where('updated_at', '>', $now->subMinutes($intervalMin))
								->select(DB::raw("distinct(user_id)"))
								->lists('user_id')
								;
		}
		$onlineList = array();
		$offlineList = self::$_queuedIds;
		sort($offlineList);
		if(!empty($onlineModels)){
			foreach($onlineModels as $row){
				$onlineList[] = array('id'=>$row, 'client'=>0);
				//remove the online id from the offline list
				$key = array_search($row,$offlineList);
				if($key !== false) {
					unset($offlineList[$key]);
				}
			}
		}
		$list = new stdClass();
		$list->online = $onlineList;
		$list->offline = $offlineList;
		return json_encode($list);
	}

	/**
	 * Return the queued list
	 * 
	 * @return array
	 */
	public static function getQueuedList(){
		return self::$_queuedIds;
	}
	
	/**
	 * Return online user counter
	 * 
	 * @return int
	 */
	public static function getOnlineUsersCounter(){
		//return HALOOnlineuserModel::distinct('user_id')->count();
		$rtn = DB::table('halo_online_users')->select(DB::raw('count(distinct(user_id))'))->first();
		if($rtn && isset($rtn->count)) {
			return $rtn->count;
		}	else {
			return 0;
		}
	}
}