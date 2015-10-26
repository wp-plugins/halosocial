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

class FilterEventHandler
{

    /**
     *
     * @param  array $events
     */
    public function subscribe($events)
    {
        Event::listen('filter.p2s.textsearch', 'FilterEventHandler@p2s_textSearch');
        Event::listen('filter.s2p.textsearch', 'FilterEventHandler@s2p_textSearch');

		//user post filters
        Event::listen('filter.p2s.userposts', 'FilterEventHandler@p2s_userPosts');
        Event::listen('filter.s2p.userposts', 'FilterEventHandler@s2p_userPosts');

		//user post filters
        Event::listen('filter.p2s.locationsearch', 'FilterEventHandler@p2s_locationSearch');
        Event::listen('filter.s2p.locationsearch', 'FilterEventHandler@s2p_locationSearch');

		//daterange filters
        Event::listen('filter.p2s.daterange', 'FilterEventHandler@p2s_dateRange');
        Event::listen('filter.s2p.daterange', 'FilterEventHandler@s2p_dateRange');

		//pricerange filters
        Event::listen('filter.p2s.pricerange', 'FilterEventHandler@p2s_priceRange');
        Event::listen('filter.s2p.pricerange', 'FilterEventHandler@s2p_priceRange');

		//statuslist filters
        Event::listen('filter.p2s.statuslist', 'FilterEventHandler@p2s_statusList');
        Event::listen('filter.s2p.statuslist', 'FilterEventHandler@s2p_statusList');

		//sort filters
        Event::listen('filter.p2s.sort', 'FilterEventHandler@p2s_sort');
        Event::listen('filter.s2p.sort', 'FilterEventHandler@s2p_sort');

		//label filters
        Event::listen('filter.p2s.labellist', 'FilterEventHandler@p2s_labelList');
        Event::listen('filter.s2p.labellist', 'FilterEventHandler@s2p_labelList');

		
		//filter configuration
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter user activities (on profile page)',
											'value' => array('display' => '',
															'apply' => 'HALOProfileModel::userStream',
															'name' => 'activity.profile.user'),
											'params' => array(),
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by user (on profile page)',
											'value' => array('display' => 'HALOPrivacy::getUserActivities',
															'apply' => 'HALOPrivacy::getUserActivities',
															'name' => 'activity.profile.byuser'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By User'),
															'uiType' => array('ui' => 'form.hidden', 'value' => 'form.filter_tree_radio'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by category (on profile page)',
											'value' => array('display' => 'HALOProfileModel::getCategories',
															'apply' => 'HALOProfileModel::categoryStream',
															'name' => 'activity.profile.category'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By Category'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by user (on home page)',
											'value' => array('display' => 'HALOPrivacy::getUserActivities',
															'apply' => 'HALOPrivacy::getUserActivities',
															'name' => 'activity.home.byuser'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By User'),
															'uiType' => array('ui' => 'form.hidden', 'value' => 'form.filter_tree_radio'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by text (on home page)',
											'value' => array('display' => '',
															'apply' => 'HALOActivityModel::applyTextSearch',
															'name' => 'activity.home.string'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'Search'),
															'uiType' => array('ui' => 'form.hidden', 'value' => 'filter.text'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by category (on home page)',
											'value' => array('display' => 'HALOProfileModel::getCategories',
															'apply' => 'HALOProfileModel::categoryStream',
															'name' => 'activity.home.category'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By Category'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by hashtag',
											'value' => array('display' => '',
															'apply' => 'HALOActivityModel::getByHashTag',
															'name' => 'activity.home.hashtag'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'Tags'),
															'uiType' => array('ui' => 'form.hidden', 'value' => 'filter.text'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activities by privacy',
											'value' => array('display' => '',
															'apply' => 'HALOPrivacy::privacyStream',
															'name' => 'activity.privacy.index'),
											'params' => array()
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter activity by ID',
											'value' => array('display' => '',
															'apply' => 'HALOActivityModel::singleActivity',
															'name' => 'activity.single.index'),
											'params' => array()
										);
		});

		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter users by text',
											'value' => array('display' => 'HALOUserModel::displayNameSearch',
															'apply' => 'HALOUserModel::applyNameSearch',
															'name' => 'user.listing.search'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'Search'),
															'table' => array('ui' => 'form.hidden', 'value' => 'halo_users'),
															'column' => array('ui' => 'form.hidden', 'value' => 'name'),
															'metaCb' => array('ui' => 'form.hidden', 'value' => 'HALOFilter::getTextSearch'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter users by type',
											'value' => array('display' => 'HALOUserModel::getUserType',
															'apply' => 'HALOUserModel::getUserType',
															'name' => 'user.listing.type'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By Type'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter to sort users',
											'value' => array('display' => 'HALOUserModel::displaySortBy',
															'apply' => 'HALOUserModel::applySortBy',
															'name' => 'user.listing.sort'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'Sort'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter users by profile type',
											'value' => array('display' => 'HALOUserModel::getMemberProfileType',
															'apply' => 'HALOUserModel::getMemberProfileType',
															'name' => 'user.listing.profiletype'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'Profile Type'),
											)
										);
		});
		
		Event::listen('filter.config.load', function($filterConfig) {
			$filterConfig->filters[] = array('title' => 'Filter followers by type',
											'value' => array('display' => 'HALOFollowerModel::getFollowerType',
															'apply' => 'HALOFollowerModel::getFollowerType',
															'name' => 'follower.listing.type'),
											'params' => array('title' => array('placeholder' => 'Enter filter title', 'value' => 'By Type'),
											)
										);
		});
		
    }

    /**
     * Event handler to do p2s transform for text search filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_textSearch($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && !is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				$params['f'][] = $key . ':' . $params['filters'][$filter->id] ;
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for text search filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_textSearch($data, $filter, $value){
		$data->params['filters'][$filter->id] = $value;
	}

    /**
     * Event handler to do p2s transform for user post filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_userPosts($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && !is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				if($params['filters'][$filter->id] != 'all_posts'){
					$params['f'][] = $key . ':' . $params['filters'][$filter->id];
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for user post filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_userPosts($data, $filter, $value){
		$data->params['filters'][$filter->id] = $value;
	}

    /**
     * Event handler to do p2s transform for user post filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_locationSearch($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				$address = isset($params['filters'][$filter->id]['name'])?$params['filters'][$filter->id]['name']:'';
				$lat = isset($params['filters'][$filter->id]['lat'])?$params['filters'][$filter->id]['lat']:'';
				$lng = isset($params['filters'][$filter->id]['lng'])?$params['filters'][$filter->id]['lng']:'';
				$distance = isset($params['filters'][$filter->id]['distance'])?$params['filters'][$filter->id]['distance']:'';
				//default distance is 5km
				$distance = ($distance == 5)?'':$distance;
				$values = array();
				if($address !== '') {
					$values[] = 'name '.$address;
				}
				if($lat !== '') {
					$values[] = 'lat '.$lat;
				}
				if($lng !== '') {
					$values[] = 'lng '.$lng;
				}
				if($distance !== '') {
					$values[] = 'distance '.$distance;
				}
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $values);
				}
				/*
				//only generate key if one of location attribute is not empty
				if($address !== '' && $lat !== '' && $lng != '' && $distance !== 5){
					$params['f'][] = $key . ':' . implode(',', array($address,$distance,$lat,$lng));
				}
				*/
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for user post filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_locationSearch($data, $filter, $value){
		$values = explode(',', $value);
		$pattern = '/^(name|lat|lng|distance)\s(.+?)$/';
		$keys = array('name', 'lat', 'lng', 'distance');
		foreach($values as $value){
			if(preg_match($pattern, $value, $matches)){
				$k = array_search($matches[1], $keys);
				if($k !== false){
					$data->params['filters'][$filter->id][$keys[$k]] = $matches[2];
				}
			}
		}
	}

    /**
     * Event handler to do p2s transform for date range filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_dateRange($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				$within = isset($params['filters'][$filter->id]['within'])?$params['filters'][$filter->id]['within']:'';
				$morethan = isset($params['filters'][$filter->id]['morethan'])?$params['filters'][$filter->id]['morethan']:'';
				$startdate = isset($params['filters'][$filter->id]['startdate'])?$params['filters'][$filter->id]['startdate']:'';
				$enddate = isset($params['filters'][$filter->id]['enddate'])?$params['filters'][$filter->id]['enddate']:'';
				//only generate key if one of location attribute is not empty
				$values = array();
				if($within !== '') {
					$values[] = 'within'.$within;
				}
				if($morethan !== '') {
					$values[] = 'morethan'.$morethan;
				}
				if($startdate !== '') {
					$values[] = 'startdate'.$startdate;
				}
				if($enddate !== '') {
					$values[] = 'enddate'.$startdate;
				}
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $values);
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for date range filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_dateRange($data, $filter, $value){
		$values = explode(',', $value);
		$pattern = '/^(within|morethan|startdate|enddate)(.+?)$/';
		$keys = array('within', 'morethan', 'startdate', 'enddate');
		foreach($values as $value){
			if(preg_match($pattern, $value, $matches)){
				$k = array_search($matches[1], $keys);
				if($k !== false){
					$data->params['filters'][$filter->id][$keys[$k]] = $matches[2];
				}
			}
		}
	}

    /**
     * Event handler to do p2s transform for price range filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_priceRange($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				$min = isset($params['filters'][$filter->id]['min'])?$params['filters'][$filter->id]['min']:'';
				$max = isset($params['filters'][$filter->id]['max'])?$params['filters'][$filter->id]['max']:'';
				//only generate key if one of location attribute is not empty
				$values = array();
				if($min !== '') {
					$values[] = 'min'.$min;
				}
				if($max !== '') {
					$values[] = 'max'.$max;
				}
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $values);
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for price range filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_priceRange($data, $filter, $value){
		$values = explode(',', $value);
		$pattern = '/^(min|max)(.+?)$/';
		$keys = array('min', 'max');
		foreach($values as $value){			
			if(preg_match($pattern, $value, $matches)){
				$k = array_search($matches[1], $keys);
				if($k !== false){
					$data->params['filters'][$filter->id][$keys[$k]] = $matches[2];
				}
			}
		}
	}

    /**
     * Event handler to do p2s transform for sort filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_sort($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				//only generate key if one of location attribute is not empty
				$values = $params['filters'][$filter->id];
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $params['filters'][$filter->id]);
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for sort filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_sort($data, $filter, $value){
		$values = explode(',', $value);
		$data->params['filters'][$filter->id] = $values;
	}

    /**
     * Event handler to do p2s transform for status list filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_statusList($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				//only generate key if one of location attribute is not empty
				$values = $params['filters'][$filter->id];
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $params['filters'][$filter->id]);
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for status list filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_statusList($data, $filter, $value){
		$values = explode(',', $value);
		$data->params['filters'][$filter->id] = $values;
	}

	 /**
     * Event handler to do p2s transform for status list filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     */
	public function p2s_labelList($data, $filter){
		$params = $data->params;
		if(isset($params['filters'][$filter->id]) && !is_object($params['filters'][$filter->id]) && is_array($params['filters'][$filter->id])){
			$keywords = (array) $filter->getParams('transformKeywords','');
			$key = array_shift($keywords);
			if($key){
				//only generate key if one of location attribute is not empty
				$values = $params['filters'][$filter->id];
				if(!empty($values)){
					$params['f'][] = $key . ':' . implode(',', $params['filters'][$filter->id]);
				}
				unset($params['filters'][$filter->id]);
			}
			$data->params = $params;
		}
	}

    /**
     * Event handler to do s2p transform for status list filter
     * 
     * @param  stdClass $data wrapper stdClass that cary the target params
     * @param  HALOFilterModel $filter
     * @param  string $value value will be apply for this filter
     */
	public function s2p_labelList($data, $filter, $value){
		$values = explode(',', $value);
		$data->params['filters'][$filter->id] = $values;
	}

	/*
		Event handler to apply filter by label
	*/
    public function byLabelApplyFilter($data, $value, $params)
    {
        $query = $data->query;
        if (empty($value)) {
            return true;
        }
        if (is_string($value)) {
            $values = explode(',', $value);
        } elseif (is_array($value)) {
            $values = $value;
        }
        try {
            $query = $query->byLabel(array('label_slug' => $values));
        } catch (Exception $e) {
            return false;
        }
        $data->query = $query;
        return true;
    }
	
}
