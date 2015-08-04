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

class HALOPagination {

	public static $pagination = null;
	
	public static function getPagination(){
		return self::$pagination;
	}
	
	public static function getData($model,$defaultSort = 'id'){
		$o = $model;
		
		//workaround: paginator get the current page via get url
		if(is_null(Input::instance()->query->get('pg'))){
			$page = Input::get('pg',1);
			Input::instance()->query->set('pg',$page);
		}

		//process filter settings
		$filterValues = Input::get('filters');
		if(is_array($filterValues)){
			//skip filters with empty string value

			foreach($filterValues as $key => $value){
				if($filterValues[$key] == ''){
					unset($filterValues[$key]);
				}
			}
			if(!empty($filterValues)){
				$filters = HALOFilter::getFilterByIds(array_keys($filterValues));
				foreach ($filters as $key => $filter){
					$filter->applyFilter($o,$filterValues[$filter->id]);
				}
			}
		}
		//process sorting
		$defaultSortParts = explode('.',$defaultSort);
		if(count($defaultSortParts) == 1) {
			$defaultSort = method_exists($model,'getTable')?$model->getTable() . '.'.$defaultSort:$defaultSort;
		}
		//process pagination settings
		$limit 	= Input::get('limit',HALOConfig::get('global.defaultLimit'));
		$sort 	= Input::get('sort','');
		$dir	= Input::get('dir','asc');

		if(!empty($sort)){
			$o = $o->orderBy($sort,$dir);
		}else if(isset($o->orders) && empty($o->orders)){
			$o = $o->orderBy($defaultSort,$dir);		
		}

		$se = HALOConfig::get('se.enable',false) && HALOSearch::hasInstance();		//enable search engine
		if($se){
			$se = HALOSearch::getInstance();
			
			$se->take($limit);
			
			$rtn = $se->paginate($model);
			
		} else {
			if(!empty($limit)){
				$rtn = $o->paginate($limit);

			} else {
				$rtn = $o->paginate();
			}
		}
		//append all original params
		$rtn->appends(Input::except(array('func', 'com')));
		
		
		//append sort paramter to url
		if(!empty($sort)){
			$rtn->appends(array('sort' => $sort));
			$rtn->appends(array('dir' => $dir));

			//for ajax pagination
			Input::merge(array('sort'=>$sort,'dir'=>$dir));
		}

		//append sort paramter to url
		if(!empty($filterValues)){
			foreach($filterValues as $key => $value){
				$rtn->appends(array('filters[' . $key . ']' => $value));

				//for ajax pagination
				Input::merge(array('filters[' . $key . ']' => $value));
			}

		}
		if(!empty($limit)){
			$rtn->appends(array('limit' => $limit));

			//for ajax pagination
			Input::merge(array('limit' => $limit));
		}

		self::$pagination = $rtn;
		
		return self::$pagination;

	}


}
