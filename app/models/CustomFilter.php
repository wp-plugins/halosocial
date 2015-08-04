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

class HALOCustomFilterModel extends HALOModel
{
    protected $table = 'halo_custom_filters';

    protected $fillable = array('name', 'filter_str', 'status');

    protected $toggleable = array('status');

    /**
     * Get validate rule 
     * 
     * @return array 
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required',
        ));

    }

    /**
     * Create relationship
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function creator()
    {
        return $this->belongsTo('HALOUserModel', 'creator_id', 'id');
    }

    /**
     * Core filter relationship
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function filter()
    {
        return $this->belongsTo('HALOFilterModel', 'filter_id', 'id');
    }

    /**
     * Display filter handler for showing custom filter
     * 
     * @param  HALOParams 		$params
     * @param  string   		$uiType
     * @param  HALOFilterModel   	$filter 
     * @return array           
     */
    public static function customDisplayFilter(HALOParams $params, $uiType, $filter)
    {
        $customFilters = HALOCustomFilterModel::where('filter_id', $filter->id)->get();
        $options = array();
        if ($customFilters->count()) {
            foreach ($customFilters as $custom) {
                $options[] = HALOObject::getInstance(array('name' => $custom->name, 'value' => $custom->id));
            }
        }
        $params->set('uiType', 'form.filter_custom');//use custom filter display UI

        return $options;
    }

    /**
     * Apply filter handler to apply custom filter
     *  
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  string $value  
     * @param  string $params 
     * @return bool        
     */
    public static function customApplyFilter(&$query, $value, $params)
    {
        //$value content custom filter id
        if (!empty($value) && ($customFilter = HALOCustomFilterModel::find($value))) {
            $filterArray = json_decode($customFilter->filter_str, true);
            $filterIds = array_diff(array_keys($filterArray), array($customFilter->filter_id));//remove the original filter to prevent infinity loop
            if (!empty($filterIds) && ($filters = HALOFilterModel::find($filterIds))) {
                foreach ($filters as $filter) {
                    //var_dump($filter->name,$filterArray[$filter->id]);
                    if (!empty($filterArray[$filter->id])) {

                        $filter->applyFilter($query, $filterArray[$filter->id]);
                    }
                }
            }
        }

        return true;
    }

}
