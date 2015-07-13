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

class HALOActorItem {
	private $id = null;
	private $actor = null;
	private $original = null;

	public function HALOActorItem($actor, $original = null) {
		if(is_numeric($actor)) {
			$actor = HALOUserModel::getUser($actor);
		}
		$this->id = $actor?$actor->id:null;
		$this->actor = $actor;

		if(HALOActorList::isActor($original)) {
			$this->original = $original;
		}
	}
	
	public function equal($actor) {
		if(!$actor || !is_a($actor, 'HALOActorItem')) return false;
		$original = $actor->getOriginal();
		
		if($original xor $this->original) return false;
		
		if($original){
			return ($this->original->id == $original->id) 
					&& ($this->original->getContext() == $original->getContext())
					&& ($this->id == $actor->id);
		} else {
			return ($this->id == $actor->getId());
		}
	}
	
	public function toString() {
		return json_encode($this->toArray());
	}
	
	public function toArray() {
		$arr = array();
		if($this->id) {
			$arr[0] = $this->id;
			if($this->original) {
				$arr[1] = $this->original->getContext();
				$arr[2] = $this->original->id;
			}
		}
		return $arr;
	}
	
	/**
	 * return display name of this actor object
	 *
	 * @return string display name
	 */
	public function getDisplayName() {
		$original = $this->getOriginal();
		$actor = $this->getActor();
		if($original && ($original->id != $actor->id || $original->getContext() != $actor->getContext())) {
			return $original->getDisplayName();
		} else if ($actor) {
			return $actor->getDisplayName();
		} else {
			return '';
		}	
	}
	
	/**
	 * return display link of this actor object
	 *
	 * @param  mixed  $params additional params
	 * @param  boolean  $brief include the brief tooltip or not
	 * @return string display link
	 */
	public function getDisplayLink($class = '', $brief = true){
		$original = $this->getOriginal();
		$actor = $this->getActor();
		if($original && ($original->id != $actor->id || $original->getContext() != $actor->getContext())) {
			$sub = '';
			$main = $original->getDisplayLink($class, $brief);
			if(HALOAuth::can($original->getContext() . '.edit', $original)) {	//check if user have edit role on the original actor
				$sub = '<span class="halo-actor-origin">[' . $actor->getDisplayLink($class, $brief) .']</span>';
			}
			return $main . $sub;
		} else if ($actor) {
			return $actor->getDisplayLink($class, $brief);
		} else {
			return '';
		}
	}
	
	/**
	 * return url of this object
	 *
	 * @param  mixed  $params additional params
	 * @return string url
	 */
	public function getUrl(array $params = array()){
		$original = $this->getOriginal();
		$actor = $this->getActor();
		if($original) {
			return $original->getUrl($params);
		} else if ($actor) {
			return $actor->getUrl($params);
		} else {
			return '';
		}
	}
	
	/**
	 * return avatar of this object
	 *
	 * @param  mixed  $params additional params
	 * @return string avatar
	 */
	public function getAvatar($size = HALO_PHOTO_AVATAR_SIZE){
		$original = $this->getOriginal();
		$actor = $this->getActor();
		if($original) {
			return $original->getAvatar($size);
		} else if ($actor) {
			return $actor->getAvatar($size);
		} else {
			return '';
		}
	}

    /**
     * Return brief data attrubutes for this model
     * 
     * @return string
     */
    public function getBriefDataAttribute() {
		$original = $this->getOriginal();
		$actor = $this->getActor();
		if($original) {
			return $original->getBriefDataAttribute();
		} else if ($actor) {
			return $actor->getBriefDataAttribute();
		} else {
			return '';
		}
    }
	
	/**
	 * return user id of this object
	 *
	 * @return integer userid
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * return original object
	 *
	 * @return original object
	 */
	public function getOriginal() {
		return $this->original;
	}
	
	/**
	 * return actor object
	 *
	 * @return actor object
	 */
	public function getActor() {
		return $this->actor;
	}
}

class HALOActorList extends Illuminate\Support\Collection
{
	private $settings = array('canDuplicated' => false);

	/**
	 * check if a given item is a valid actor
	 *
	 * @param  mixed  $item
	 * @return true/false
	 */
	public static function isActor($item) {
		return is_a($item, 'HALOModel') || is_a($item, 'HALONestedModel');
	}
	
	/**
	 * check if a given item is a valid user model
	 *
	 * @param  mixed  $item
	 * @return true/false
	 */
	public static function isUser($item) {
		return is_a($item, 'HALOUserModel');
	}
	
	/**
	 * Add a new actor to actor list
	 *
	 * @param  mixed  $actor
	 * @return added actor
	 */
	public function addActor($actor, $original = null) {
		$actor = new HALOActorItem($actor, $original);
		if(!isset($this->settings['canDuplicated']) || $this->settings['canDuplicated'] == false) {
			foreach($this->items as $item) {
				if(is_a($item, 'HALOActorItem') && $item->equal($actor, $original)) {
					return $actor;
				}
			}
		}
		$this->push($actor);
		return $actor;
	}
	
	/**
	 * Remove an actor from actor list
	 *
	 * @param  mixed  $actor
	 * @return removed actor
	 */
	public function removeActor($actor, $original = null) {
		$actor = new HALOActorItem($actor, $original);
		foreach($this->items as $key => $item) {
			if(is_a($item, 'HALOActorItem') && $item->equal($actor)) {
				return $this->splice($key, 1);
			}
		}
		return true;	
	}
	
	/**
	 * instance a new actor list object from array
	 *
	 * @param  array  array
	 * @return new actor list object
	 */
	public static function fromArray(array $arr) {
		$instance = new HALOActorList();
		try{
			foreach($arr as $actorData) {
				if(is_array($actorData)) {
					if(count($actorData) == 1) {
						$actor = new HALOActorItem($actorData[0]);
					} else if(count($actorData) == 3) {
						$actor = new HALOActorItem($actorData[0], HALOModel::getCachedModel($actorData[1], $actorData[2]));
					}
				} else if(is_numeric($actorData)) {
					$actor = new HALOActorItem($actorData);
				}
				if($actor->getId()) {
					$instance[] = $actor;
				}
			}
		} catch(\Exception $e) {
		
		}
		return $instance;
	}
	
	/**
	 * instance a new actor list object from serialized string
	 *
	 * @param  string  $str
	 * @return new actor list object
	 */
	public static function fromString($str) {
		try {
			$data = json_decode($str);
		} catch(\Exception $e) {
			return null;
		}
		$instance = new HALOActorList();
		//init settings
		$instance->settings = isset($data->settings)?$data->settings:array();
		//init actor models
		if(isset($data->actors)) {
			try{
				foreach($data->actors as $actorData) {
					if(is_array($actorData)) {
						if(count($actorData) == 1) {
							$actor = new HALOActorItem($actorData[0]);
						} else if(count($actorData) == 3) {
							$actor = new HALOActorItem($actorData[0], HALOModel::getCachedModel($actorData[1], $actorData[2]));
						}
					}
					if($actor->getId()) {
						$instance[] = $actor;
					}
				}
			} catch(\Exception $e) {
			
			}
		}
		return $instance;
	}
	
	/**
	 * return serialized string for this object
	 *
	 * @param  none
	 * @return string serialized string
	 */
	public function toString () {
		$data = new stdClass();
		$data->settings = $this->settings;
		//only need to return actor id and actor context
		$actors = array();
		foreach($this->items as $key => $item) {
			if(is_a($item, 'HALOActorItem')) {
				$actors[] = $item->toArray();
			}
		}
		$data->actors = $actors;
		return json_encode($data, 0);
	}
	
	/**
	 * return a list of actors links
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getDisplayLink($class = '', $brief = true)
	{
		$html = '';
		$my = HALOUserModel::getUser();
		if(count($this) <= 0) return $html;
		//1. reorder the actors to prioritize me  and friends
		$this->pretty();
		
		//2. take the first
		$first = $this->first();
		$html = $html . $first->getDisplayLink($class, $brief);

		//3. take the last
		$last = $this->slice(1);
		if(count($last) == 1) {
			$html = $html . __halotext(' and ') . $last[0]->getDisplayLink($class, $brief);
		} else if(count($last) > 1) {
			$tooltipNames = array();
			foreach($last as $actor) {
				$tooltipNames[] = $actor->getDisplayName();
			}
			
			$html = $html . __halotext(' and ') . 
					'<a href="javascript:void(0)" class="halo-tooltip" data-toggle="tooltip" title="' . implode("\n", $tooltipNames) . '">' .
						sprintf(__halotext('%s others'), count($last)) .
					'</a>'
					;
		}
		
		return $html;
	}
	
	/**
	 * return a list of actors name
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getDisplayName($class = '')
	{
		return $this->getActorNames($class);
	}
	
	/**
	 * return a list of actors name
	 * 
	 * @param  mixed $class
	 * @return object
	 */
	public function getActorNames($class = '')
	{
		$html = '';
		$my = HALOUserModel::getUser();
		if(count($this) <= 0) return $html;
		//1. reorder the actors to prioritize me  and friends
		$this->pretty();
		//2. take the first
		$first = $this->first();

		// if($my && $first->getId() == $my->id) {
			// $html = $html . __halotext('You');
		// } else {
			// $html = $html . $first->getDisplayName();
		// }
		$html = $html . $first->getDisplayName();
		//3. take the last
		$last = $this->slice(1);
		if(count($last) == 1) {
			$html = $html . __halotext(' and ') . $last[0]->getDisplayName();
		} else if(count($last) > 1) {
			$tooltipNames = array();
			foreach($last as $actor) {
				$tooltipNames[] = $actor->getDisplayName();
			}
			
			$html = $html . __halotext(' and ') . 
					'<a href="javascript:void(0)" class="halo-tooltip" data-toggle="tooltip" title="' . implode("\n", $tooltipNames) . '">' .
						sprintf(__halotext('%s others'), count($last)) .
					'</a>'
					;
		}
		
		return $html;
	}
		
	/**
	 * return a list of actors url
	 * 
	 * @param  mixed $class
	 * @return string url
	 */
	public function getUrl(array $params = array())
	{
		$url = '';
		$my = HALOUserModel::getUser();
		if(count($this) <= 0) return $url;
		//1. reorder the actors to prioritize me  and friends
		$this->pretty();
		
		//2. take the first
		$first = $this->first();
		
		return $first->getUrl($params);
	}

	/**
	 * return a list of actors url
	 * 
	 * @param  mixed $class
	 * @return string url
	 */
	public function getBriefDataAttribute()
	{
		$attr = '';
		$my = HALOUserModel::getUser();
		if(count($this) <= 0) return $attr;
		//1. reorder the actors to prioritize me  and friends
		$this->pretty();
		
		//2. take the first
		$first = $this->first();
		
		return $first->getBriefDataAttribute();
	}
		
	/**
	 * return a list of actors avatar
	 * 
	 * @param  mixed $class
	 * @return string avatar
	 */
	public function getAvatar($size = HALO_PHOTO_AVATAR_SIZE)
	{
		$avatar = '';
		$my = HALOUserModel::getUser();
		if(count($this) <= 0) return $avatar;
		//1. reorder the actors to prioritize me  and friends
		$this->pretty();
		
		//2. take the first
		$first = $this->first();
		
		return $first->getAvatar($size);
	}
		
	public function pretty() {
		$my = HALOUserModel::getUser();
		if($my) {
			$this->sort(function($a, $b) use ($my) {
				if($a->getId() == $my->id) return -1;
				if($b->getId() == $my->id) return 1;
				return 0;
			});
		}
	}
	
}

class HALOActorListHelper {
	public $testData;
	
	public static $recentAddedActor = null;
	/**
	 * Add a new actor to model column
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return added actor
	 */
	public static function insertRecentActorToColumn(&$model, $column) {
		if(HALOActorListHelper::$recentAddedActor) {
			$actor = HALOActorListHelper::$recentAddedActor->getActor();
			$original = HALOActorListHelper::$recentAddedActor->getOriginal();
			$actorList = HALOActorList::fromString($model->$column);
			$insertedActor = $actorList->addActor($actor, $original);
			$model->$column = $actorList->toString();			
		}
		return $model;
	}

	/**
	 * Add a new actor to model column
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return added actor
	 */
	public static function addActorToColumn(&$model, $column, $actor, $original = null) {
		$actorList = HALOActorList::fromString($model->$column);
		$insertedActor = $actorList->addActor($actor, $original);
		$model->$column = $actorList->toString();
		
		//keep track of last added actor
		HALOActorListHelper::$recentAddedActor = $insertedActor;
		return $model;
	}

	/**
	 * Add an actor string (user only) to model column
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return added actor
	 */
	public static function addUserStringToColumn(&$model, $column, $str) {
		$actorList = HALOActorList::fromString($model->$column);
		$userIds = explode(',', $str);
		foreach($userIds as $id) {
			if($id) {
				$actorList->addActor($id);
			}
		}
		$model->$column = $actorList->toString();
		return $model;
	}

	/**
	 * Remove an actor from actor list column
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to remove
	 * @return added actor
	 */
	public static function removeActorFromColumn(&$model, $column, $actor, $original = null) {
		if(isset($model->$column)) {
			$actorList = HALOActorList::fromString($model->$column);
			$actorList->removeActor($actor, $original);
			$model->$column = $actorList->toString();
		}
		return $model;
	}
	
	/**
	 * Function to return actor list collection
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to remove
	 * @return added actor
	 */
	public static function getActorListFromColumn(&$model, $column) {
		if(isset($model->$column)) {
			$actorList = HALOActorList::fromString($model->$column);
			return $actorList;
		}
		//unknow column
		return null;
	}
	
	/**
	 * Return the actor links
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return string actor links
	 */
	public static function getActorLinksFromColumn(&$model, $column, $class='', $brief = true) {
		if(isset($model->$column)) {
			$actorList = HALOActorList::fromString($model->$column);
			return $actorList->getDisplayLink($class, $brief);
		}
		return '';
	}

	/**
	 * Return the actor url
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return string actor url
	 */
	public static function getActorUrlFromColumn(&$model, $column, array $params = array()) {
		if(isset($model->$column)) {
			$actorList = HALOActorList::fromString($model->$column);
			return $actorList->getUrl($params);
		}
		return '';
	}

	/**
	 * Return the actor avatar
	 *
	 * @param  HALOModel $model the underlying model
	 * @param  string $column the column model that used to store actor list string
	 * @param  mixed  $actor actor to add
	 * @return string actor avatar
	 */
	public static function getAvatarFromColumn(&$model, $column, array $params = array()) {
		if(isset($model->$column)) {
			$actorList = HALOActorList::fromString($model->$column);
			return $actorList->getAvatar($params);
		}
		return '';
	}

}
