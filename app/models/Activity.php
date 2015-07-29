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

class HALOActivityModel extends HALOModel
{
    protected $table = 'halo_activities';

	protected $fillable = array('action', 'context', 'tagged_list', 'target_id', 'message', 'access');
	
	private $validator = null;
	
	private $_params = null;
	
	/**
	 * Get Validate rule 
	 * 
	 * @return array
	 */
	public function getValidateRule()
	{
		return array('action' => 'required', 'context' => 'required', 'target_id' => 'required');	
	
	}
	
	public static $resourceCbs = array();
	//////////////////////////////////// Define Relationships /////////////////////////
	
	/**
	 * HALOUserModel, HALOActivityModel: one to one (actor)
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function actor()
	{
		return $this->belongsTo('HALOUserModel', 'actor_id');
	}

	/**
	 * HALOActivityModel, HALOUserModel: polymorphic
	 * @return  object
	 */
	public function owner()
	{
		return $this->actor();
	}
	
	/**
	 * HALOLocationModel, HALOActivityModel: one to one (location)
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function location()
	{
		return $this->belongsTo('HALOLocationModel', 'location_id');
	}
	
	/**
	 * HALOActivityModel, HALOCommentModel: polymorphic (comments)
	 * @return Illuminate\Database\Eloquent\Relations\morphMany
	 */		
	public function comments()
	{
		$builder = $this->morphMany('HALOCommentModel', 'commentable')->orderBy('created_at');
		
		return $builder;
	}
		
	/**
	 * HALOActivityModel, HALOLikeModel: polymorphic
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\morphMany
	 */
	public function likes(){
		return $this->morphMany('HALOLikeModel', 'likeable');
	}
	
	/**
	 * HALOActivityModel, HALOReportModel: polymorphic
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\morphMany
	 */	
	public function reports(){
		return $this->morphMany('HALOReportModel', 'reportable');
	}
		
	/**
	 * HALOActivityModel, HALOTagModel: many to many polymorph
	 *
	 * @return Illuminate\Database\Eloquent\Relations\morphMany
	 */
	public function tags()
    {
        return $this->morphToMany('HALOTagModel', 'tagging', 'halo_tagging', 'tagging_id', 'tag_id');
    }	
	
	/**
	 * HALOActivityModel, HALOTagModel: many to many polymorph
	 * @return Illuminate\Database\Eloquent\Relations\morphToMany
	 */
	public function tagusers()
    {
        return $this->morphToMany('HALOTagModel', 'tagging', 'halo_tagging', 'tagging_id', 'tag_id')->where('halo_tags.taggable_type', 'HALOUserModel');
    }	

	/**
	 * HALOActivityModel, HALOPhotoModel: param relation
	 *
	 * Illuminate\Database\Eloquent\Relations\morphToMany
	 */
	public function photos()
	{
		return $this->hasParam('HALOPhotoModel', 'photos_id');
	}	
	
	/**
	 * HALOActivityModel, HALOHashTagModel: poly many to many
	 *
	 * Illuminate\Database\Eloquent\Relations\morphToMany
	 */
	public function hashtags()
    {
        return $this->morphToMany('HALOHashTagModel', 'taggable', 'halo_hash_taggables', 'taggable_id', 'tag_id');
    }	
	
	//////////////////////////////////// Define Relationships /////////////////////////

	/**
	 * return html of this activity
	 *
	 * @return  object
	 */
	public function render()
	{
		//process message hashtag
		$this->message = HALOHashTagAPI::renderMessage($this->message, $this);
		//message content is treated as the title of attachment
		//$this->message = HALOUtilHelper::renderMessage($this->message);
		
		//trigger event to render activity attachment
		$event = Event::fire('activity.onRender', array(&$this));
			
		$html = HALOUIBuilder::getInstance('activity'.$this->id, 'activity_layout', array('act'=>$this, 'zone' => 'layout.'.$this->getZone())
														)->fetch();
	
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

	/*
		return actor switcher html applied for this activity context
	*/
	public function listDisplayActors($template) {
		//check permission
		$my = HALOUserModel::getUser();
		$context = HALOModel::getCachedModel($this->context, $this->target_id);
		if(method_exists($context, 'listDisplayActors')) {
			return $context->listDisplayActors($template);
		}
		return '';
	}

	/**
	 * Get Message this is string
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		$message = $this->message;
		$message = HALOUtilHelper::renderMessage($message);
		return trim($message);
	}

	/**
	 * Get notification content 
	 * 
	 * @param  mixed $class
	 * @return string
	 */
	public function getNotifContent($class = '')
	{
		$message = $this->getMessage();
		if(trim($message) !== '') {
			$message = ' "' . HALOOutputHelper::ellipsis(HALOOutputHelper::striptags($message, array('p'))) . '"';
		} else {
			$message = '';
		}
		return $this->getDisplayLink($class) . $message ;
	}
	
	/**
	 * Get Info Html
	 * 
	 * @return string this is html content 
	 */
	public function getInfoHtml()
	{
		$arr = array();
		$html = '';
		//location info html
		if($this->location_id) {
			$arr[] = __halotext('at') . ' ' . $this->location->getDisplayLink();
		}
		//tagged user info html
		
		//additional info html
		
		if (!empty($arr)) {
			$html = ' - ' . implode(' ', $arr);
		}
		return $html;
	}	
	
	/**
	 * Get Activities a list of activity by using input options
	 * 
	 * @param  array $options
	 * @return string  		a list of activity
	 */
	public static function getActivities($options) {
		$default = array('limit' => HALO_ACTIVITY_LIMIT_DISPLAY,
						'orderBy'=>'created_at',
						'orderDir'=>'desc',
						'after' => '',
						'before' => '',
						'actid' => array(),
						'filters'=> array(),
						'updatedOnly'=>false,						//return updated activities only
						'groupOnly'=> true
						);
		$options = array_merge($default, $options);
		$query = HALOActivityModel::with('actor')
								->orderBy($options['orderBy'], $options['orderDir'])
								->take($options['limit']);
		if(!empty($options['before'])) {
			$query->where('id', '>', $options['before']);
		}
						
		if(!empty($options['after'])) {
			$query->where('id', '<', $options['after']);
		}

		if(!empty($options['actid'])) {
			$query->whereIn('id', $options['actid']);
		}

		if($options['updatedOnly']) {
			$timestamps = Session::get('halo_stream_timestamps');
			$query->where('updated_at', '>', Carbon::parse($timestamps));
		}
		
		if(!empty($options['groupOnly'])) {
			$query->where('grouped', 0);
		}

		$filters = $options['filters'];

		//privacy setting enforcement
		$privacyFilters = HALOFilter::getFilterByName('activity.privacy.index');
		//configure filters value
		if(!empty($filters)) {
			$filters = $filters->merge($privacyFilters);
		} else {
			$filters = $privacyFilters;
		}
		
		//apply filters
		$query = HALOFilter::applyFilters($query, $filters);

		$acts = $query->get();
		
		//lazy load acts related models
		Event::fire('activity.onLoadRelatedModels', array(&$acts));
		
		return $acts;
	}
   
    /**
     * Get display name link for this model
     * 
     * @param  string $class 
     * @return string
     */
    public function getDisplayLink($class = '') 
    {	
		if(!empty($class)) {
			$class = 'class="'.$class.'" ';
		}
		return	'<a ' . $class .'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
	}	

    /**
     * Get display name with link for this model
     * 
     * @return string 	 display name without link for this model
     */
  	public function getDisplayName() 
  	{	
		return	__halotext('stream') ;
	}	

	/**
	 * Get url 
	 * 
	 * @return string
	 */
	public function getUrl()
	{
		return URL::to('? view=stream&task=show&uid=' . $this->id);
	}
	
	/**
	 * Delete override default delete method to delete all activity relationship
	 * 
	 * @see Illuminate\Database\Eloquent\Model::delete() 	To set the iteams for this function
	 */
	public function delete()
    {
        // delete all related comment 
        $this->comments()->delete();
		
		// delete all related like
		$this->likes()->delete();
		
		// delete all related notification
		HALONotificationAPI::deleteAll($this);
		
        return parent::delete();
    }	

	/**
	 * Get response target function to return response target for like, comment actions on this activity
	 * 
	 * @return HALOActivityModel
	 */
	public function getResponseTarget()
	{
		if (isset($this->attachment->responseTarget)) {
			return $this->attachment->responseTarget;
		} else {
			return $this;
		}
	}

	/**
	 * Set marked an activity as grouped
	 * 
	 * @param string $parentId
	 */
	public function setGrouped($parentId)
	{
		$this->grouped = $parentId;
		$this->save();
	}
	
	/**
	 * Function to render footer content of the current activity
	 * 
	 * @return string
	 */
	public function renderFooter()
	{
		$data = new stdClass();
		Event::fire('activity.onRenderFooter', array(&$this, &$data));
		if(isset($data->html)) {
			return $data->html;
		} else {
			return '';
		}
	}
	
	/** 
	 * Single activity apply filter  call back to limit the stream updater to update a single activity only
	 * 
	 * @param  Illuminate\Database\Query\Builder $query  
	 * @param  string $value  
	 * @param  string $params    
	 */
	public static function singleActivityApplyFilter(&$query, $value, $params)
	{
		$query->where('id', '=', $value);
	}
	
	/**
	 * Apply text search apply filter call back to search activity with input text
	 * 
	 * @param  Illuminate\Database\Query\Builder $query  
	 * @param  string $value  
	 * @param  string $params 
	 */
	public static function applyTextSearchApplyFilter(&$query, $value, $params)
	{
		$query = $query->whereRaw(HALOUtilHelper::getTextSearchCondition('message', $value));
	}

	/**
	 * Get by hash tag apply filter call back to search activity by hashtag
	 * 
	 * @param  Illuminate\Database\Query\builder $query 
	 * @param  string $value  
	 * @param  mixed $params 
	 * @return bool       
	 */
	public static function getByHashTagApplyFilter(&$query, $value, $params)
	{
		if (!empty($value)) {
			try {
				$tagList = explode(',', $value);
				if($tagList) {
					$actIds = array(-1);
					foreach($tagList as $tagName) {
						$hashTag = HALOHashTagModel::where('name',$tagName)->first();
						if($hashTag) {
							$actIds = array_merge($actIds,$hashTag->activities()->lists('id'));
						}
					}
					$query = $query->whereIn('halo_activities.id', $actIds);
				}
				return true;
			} catch (\Exception $e) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get notification target action 
	 * 
	 * @return bool
	 */
	public function getNotificationTargetAction()
	{
		//open the single view mode for this activity
		HALOResponse::redirect($this->getUrl());
		return true;
	}

	/**
	 * Get total activity counter
	 * 
	 * @return int
	 */
	public static function getTotalActivitiesCounter()
	{
		return self::count();
	}

	/**
	 * Set resource call back fucntion 
	 * 
	 * @param object $method 
	 * @param object $func   
	 */
	public static function setResourceCb($method, $func)
	{
		self::$resourceCbs[$method] = $func;
	}

	/**
	 * Function to get activity's resrouce callback function
	 * 
	 * @param  object $method
	 * @return object        
	 */
	public static function getResourceCb($method)
	{
		//trigger event if resourceCbs is not loaded
		if(empty(self::$resourceCbs)) {
			Event::fire('activity.onGetResourceCb', array());
		}
		return isset(self::$resourceCbs[$method]) ? self::$resourceCbs[$method]:null;
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  object  $method
	 * @param  array   $parameters
	 * @return array
	 */
	public function __call($method, $parameters)
	{
		if (in_array($method, array('increment', 'decrement')))
		{
			return call_user_func_array(array($this, $method), $parameters);
		}

		$cb = HALOActivityModel::getResourceCb($method);
		if(!is_null($cb)) {
			return call_user_func_array($cb, array_merge(array($this), $parameters));
		}

		$query = $this->newQuery();

		return call_user_func_array(array($query, $method), $parameters);
	}
	
}