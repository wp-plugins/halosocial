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

class HALOFilter
{
    public static $data = array();
    /**
     * get Display Filter UI 
     * 
     * @param   string $name
     * @return HALOUIBuilder
     */
    public static function getDisplayFilterUI($name)
    {
        $filters = HALOFilter::getFilterByName($name);
        $ui = HALOUIBuilder::getInstance('halofilter', 'form.filter_form', array('name' => 'filter_form'));
        foreach ($filters as $filter) {
            $ui->addUI('filter_' . $filter->id, $filter->getDisplayUI());
        }
        return $ui;
    }

    /**
     * get filter meta tags
     * 
     * @param   string $metaName
     * @return array list of meta tag
     */
    public static function getFilterMetaTags($metaName)
    {
		static $metaTags = null;
		if(is_null($metaTags)){
			$metaTags = new stdClass();
			$metaTags->title = array();
			$metaTags->description = array();
			$metaTags->cover = array();
			$metaTags->keywords = array();
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
						$filter->getMetaTags($metaTags, $filterValues[$filter->id]);
					}
				}
			}
		}
		
		return isset($metaTags->$metaName)?$metaTags->$metaName:array();
    }

    /**
     * transform filter parameter to SEO format 
     * 
     * @param   string $metaName
     * @return array list of meta tag
     */
    public static function transformP2S($params)
    {
		if(is_array($params) && isset($params['filters'])){
			if(is_array($params['filters'])){
				//skip filters with empty string value
				foreach($params['filters'] as $key => $value){
					if($params['filters'][$key] == ''){
						unset($params['filters'][$key]);
					}
				}
				if(!empty($params['filters'])){
					//init the transformed param (f)
					$params['f'] = array();
					$data = new stdClass();
					$data->params = $params;
					$filters = HALOFilter::getFilterByIds(array_keys($params['filters']));
					foreach ($filters as $key => $filter){
						//do not transform default parameters
						$fName = $filter->getInputName();
						$fVal = $filter->getInputValue();
						if(HALOFilter::isDefaultFilter($fName, $fVal)){
							unset($data->params['filters'][$filter->id]);
						} else {
							$transformEvent = $filter->getParams('transformEvent');
							if($transformEvent && $filter->getParams('transformKeywords')){
								Event::fire('filter.p2s.' . $transformEvent, array(&$data, $filter), true);
							}
						}
					}
					$params = $data->params;
					//join the transformed  param to string
					if($params['f']){
						$params['f'] = implode(',', $params['f']);
					} else {
						unset($params['f']);
					}
				}
			}
		}
		//for default paging
		if(isset($params['pg']) && $params['pg'] == 1) {
			unset($params['pg']);
		}
		return $params;
    }

    /**
     * transform filter SEO format to parameter
     * 
     * @return null
     */
    public static function transformS2P()
    {
		$seoString = Input::get('f');
		$pattern = '/^(([^:]+):(.+?))(,([^:^,]+:.+))*?$/';
		if($seoString){
			$str = $seoString;
			$data = new stdClass();
			$data->params = array('filters' => array());
			while(preg_match($pattern, $str, $matches) && $str !== ''){
				$keyword = $matches[2];
				$value = $matches[3];
				//lockup filter model from transform keywords
				$filters = HALOFilter::getFiltersByKeyword($keyword);
				
				//scope transform by usec param
				$usec = Input::get('usec', null);
				foreach ($filters as $filter){
					$transformEvent = $filter->getParams('transformEvent');
					$transformSection = $filter->getParams('transformSection', null);
					if($transformEvent && $usec == $transformSection){		//
						Event::fire('filter.s2p.' . $transformEvent, array(&$data, $filter, $value), true);
					}
				}
				$str = isset($matches[5])? $matches[5]: '';
			}
			if(!empty($data->params['filters'])){
				$filterParams = Input::get('filters');
				foreach($data->params['filters'] as $key => $val){
					$filterParams[$key] = $val;
				}
				Input::merge(array('filters' => $filterParams));
			}
		}
    }

    /**
     * generate metag tag for text searching filter
     * 
     * @param  object $metaTags  
     * @param  mixed   $value filter value
     * @param  HALOFilterModel $model filter model
     * @return void
     */
    public static function getTextSearchMetaFilter(&$metaTags, $value, $model)
    {
		if($value) {
			$meta = sprintf(__halotext(": with keyword '%s'"), $value);
			$metaTags->title[$model->ordering] = $meta;
			$metaTags->ogtitle[$model->ordering] = $meta;
			$metaTags->description[$model->ordering] = $meta;
			$metaTags->ogdescription[$model->ordering] = $meta;
		}
		return true;
	}

    /**
     * generate metag tag for location searching filter
     * 
     * @param  object $metaTags  
     * @param  mixed   $value filter value
     * @param  HALOFilterModel $model filter model
     * @return void
     */
    public static function byLocationMetaFilter(&$metaTags, $value, $model)
    {
        if (!empty($value) && isset($value['lat']) && isset($value['lng']) && !empty($value['name'])) {
			$meta = sprintf(__halotext("around %s"), $value['name']);
			$metaTags->title[$model->ordering] = $meta;
			$metaTags->ogtitle[$model->ordering] = $meta;
			$metaTags->description[$model->ordering] = $meta;
			$metaTags->ogdescription[$model->ordering] = $meta;
		}
		return true;
	}

    /**
     * display filter handler for showing select filter input of a column
     * 
     * @param  HALOParams $params 
     * @param  string   $uiType 
     * @return array
     */
    public static function getColumnValuesDisplayFilter(HALOParams $params, $uiType)
    {
        $params->set('uiType', 'form.filter_select');
        try {
            $options = DB::table($params->get('table'))->distinct()
                                        ->select($params->get('column') . ' as title', $params->get('column') . ' as value')->get();
        } catch (\Exception $e) {
            $options = array();
        }
        return $options;
    }

    /**
     * display filter handler for yes/no filter
     * 
     * @param  HALOParams $params
     * @param  string   $uiType
     * @return array
     */
    public static function getYesNoDisplayFilter(HALOParams $params, $uiType)
    {
        $params->set('uiType', 'form.filter_select');
        $options = array(array('title' => __halotext('Yes'), 'value' => 1)
            , array('title' => __halotext('No'), 'value' => 0));
        return $options;
    }

    
    /**
     * display filter handler for showing non leaf category
     * 
     * @param  HALOParams $params
     * @param  string  $uiType
     * @return array
     */
    public static function getNonLeafCategoriesDisplayFilter(HALOParams $params, $uiType)
    {
        $params->set('uiType', 'form.filter_select');
        try {
			$table = $params->get('table', 'halo_common_categories');
            $options = DB::table($table)
                ->whereIn('id', function ($query) use($table) {
                $query->distinct()->from($table)->select('parent_id');
            })
                ->select('name as title', 'id as value');
            $options = $options->get();
        } catch (\Exception $e) {
            $options = array();
        }
        return $options;
    }


    /**
     * apply filter handler to apply query condition statement on a column
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @param  mixed $value
     * @param  mixed $params
     * @return bool
     */
    public static function filterColumnValuesApplyFilter(&$query, $value, $params)
    {
        try {
            $query = $query->where($params->get('table') . '.' . $params->get('column'), '=', $value);
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * display filter handler for showing search text input of a column
     * 
     * @param  HALOParams $params
     * @param  string  $uiType
     * @return string
     */
    public static function displayColumnSearchDisplayFilter(HALOParams $params, $uiType)
    {
        $params->set('uiType', 'form.filter_columnsearch');
        return '';
    }


    /**
     * apply filter handler to apply query condition statement on a column
     * 
     * @param  Illuminate\Database\Query\Builder $query 
     * @param  mixed $value 
     * @param  mixed $params 
     * @return bool
     */
    public static function applyColumnSearchApplyFilter(&$query, $value, $params)
    {
        if (!empty($value)) {
            try {
                $query = $query->whereRaw(HALOUtilHelper::getTextSearchCondition(DB::getTablePrefix() . $params->get('table') . '.' . $params->get('column'), $value));
                return true;

            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }


    /**
     * return filter model by filter name
     * 
     * @param  string $name
     * @return int
     */
    public static function getFilterByName($name)
    {
       //replace wildcard
        if (is_array($name)) {
            $name = implode(',', $name);
        }

        $name = str_replace('*', '(.*)', $name);
        $name = str_replace('.', '\.', $name);
        $name = explode(',', $name);

        if (empty($name)) {
            return array();
        }

        foreach ($name as &$n) {
            $n = '(' . trim($n) . ')';
        }

        $pattern = '/' . implode('|', $name) . '/';
        $filters = self::loadFilters();
        $rtn = $filters->filter(function ($filter) use ($pattern) {
            return preg_match($pattern, $filter->name, $matches);
        });
        return $rtn;
    }

    /**
     * return filter model by filter Ids
     * 
     * @param  array $filterIds 
     * @return bool
     */
    public static function getFilterByIds($filterIds)
    {
        $filters = self::loadFilters();
        $filterIds = (array) $filterIds;
        return $filters->filter(function ($filter) use ($filterIds) {
            return in_array($filter->id, $filterIds);
        });
    }

    /**
     * return filter models by keyword
     * 
     * @param  string $keyword text
     * @return array of filter model
     */
    public static function getFiltersByKeyword($keyword)
    {
		$keywordToFilters = Cache::rememberForever('filter.cache.keywordToFilters', function(){
			$rtn = array();
			$filters = HALOFilter::loadFilters();
			foreach($filters as $filter){
				$keywords = (array) $filter->getParams('transformKeywords','');
				if(!empty($keywords)){
					foreach($keywords as $keyword){
						if(isset($rtn[$keyword]) && is_array($rtn[$keyword])){
							$rtn[$keyword][] = $filter;
						} else {
							$rtn[$keyword] = array($filter);
						}
					}
				}
			}
			return $rtn;
		});
		return isset($keywordToFilters[$keyword])?$keywordToFilters[$keyword]:array();
    }


    /**
     * load filters from database
     * 
     * @param  bool $flushCache
     * @return mixed
     */
    public static function loadFilters($flushCache = true)
    {
        static $filters = null;

        if ($flushCache) {
            Cache::forget('halo_filters_list');
        }

        if (is_null($filters) || $flushCache) {
            $filters = Cache::rememberForever('halo_filters_list', function () {
                return HALOFilterModel::where('published', 1)
                    ->orderBy('ordering')
                ->get();
            });

        }
        return $filters;
    }

    /**
     * ordering filters is to make order with value have higher index than order without value
     * 
     * @param  mixed $filters
     * @return array
     */
    public static function orderingFilters($filters)
    {
        $rtn = array();
        $preArr = array();
        $posArr = array();
        //ordering filter function
        foreach ($filters as $filter) {
            //value
            if (is_null($filter->value)) {
                //trying to get value from http request
                $filter->value = Input::get('filters.' . $filter->id, '');

            }
            if ($filter->value || $filter->getParams('visible', '')) {
                $preArr[] = $filter;
            } else {
                $posArr[] = $filter;
            }
        }
        return array_merge($preArr, $posArr);
    }

    /**
     * apply array filters to a query
     * 
     * @param  Illuminate\Database\Query\Builder $query 
     * @param  mixed $filters
     * @return Illuminate\Database\Query\Builder
     */
    public static function applyFilters(&$query, $filters)
    {
        foreach ($filters as $filter) {

            $filter->applyFilter($query, $filter->value);
        }
        return $query;
    }
    /**
     * set Value
     * 
     * @param mixed $key
     * @param mixed $val
     */
    public static function setVal($key, $val)
    {
        self::$data[$key] = $val;
    }
    /**
     * get Value
     * 
     * @param  mixed $key
     * @param  string $default
     * @return string
     */
    public static function getVal($key, $default = '')
    {
        return isset(self::$data[$key]) ? self::$data[$key] : $default;
    }
    /**
     * to Param String 
     * 
     * @param  array $filters
     * @return string
     */
    public static function toParamString($filters)
    {
        $arr = array();
        foreach ($filters as $filter) {
            $key = "filters[" . $filter->id . "]";
            $arr[$key] = $filter->value;
        }
        return http_build_query($arr);
    }
	
	/*
		function to insert new filters from array
	*/
	public static function insertNewFilters(array $filters) {
		//new filters are appended to the list with the lowest ordering
		$ordering = DB::table('halo_filters')->orderBy('ordering', 'desc')->take(1)->pluck('ordering');
		$ordering = $ordering + 1;
		foreach($filters as $filter) {
			$filter['ordering'] = $ordering;
			$inserted = HALOUtilHelper::insertNewIfNotExists('halo_filters', $filter, array('name', 'type'));
			//update ordering
			if($inserted) {
				$ordering ++;
			}
		}
	
	}
	
	/*
		return default filter settings
	*/
	public static function getDefaultFilterSettings($cache = true) {
		if(!$cache) {
			Cache::forget('system.defaultFilterSettings');
		}
		$data = Cache::rememberForever('system.defaultFilterSettings', function() {
			$data = new stdClass();
			$data->settings = array();
			Event::fire('system.loadDefaultFilterSettings', array(&$data));
			return $data;
		});
		return $data;
	}
	
	/*
		check if the given filter key pair is a default filter
	*/
	public static function isDefaultFilter($key, $val) {
		$data = self::getDefaultFilterSettings(false);
		$settings = $data->settings;
		if (!isset($settings[$key])) return false;
		$arr1 = (array) $val;
		$arr2 = (array) $settings[$key];
		$arrDiff1 = array_diff($arr1, $arr2);
		$arrDiff2 = array_diff($arr2, $arr1);
		
		return empty($arrDiff1) && empty($arrDiff2);
	}
}
