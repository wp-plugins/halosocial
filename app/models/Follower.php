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

class HALOFollowerModel extends HALOModel
{
    protected $table = 'halo_followers';

    protected $hidden = array('created_at', 'updated_at');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule
     * 
     * @return array 
     */
    public function getValidateRule()
    {
        return array();

    }

    //////////////////////////////////// Define Relationships /////////////////////////
    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function followable()
    {
        return $this->morphTo();
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Return html for the follower action on a specific target 
     * 
     * @param  stirng $target 
     * @param  string $class  
     * @return HALOUIBuilder         
     */
    public static function getFollowerHtml($target, $class = "halo-btn halo-btn-xs halo-btn-default halo-follower-wrapper")
    {
        $context = lcfirst($target->getContext());
        $zone = 'follower.' . $context . '.' . $target->id;
        if (HALOAuth::hasRole('follower', $target)) {
            $builder = HALOUIBuilder::getInstance('', 'follow', array('title' => __halotext('Following'), 'onClick' => "halo.follower.unfollow('" . $context . "','" . $target->id . "')",
                'zone' => $zone,
                'target' => $target,
                'class' => $class,
                'icon' => 'check'));
        } else {
            $builder = HALOUIBuilder::getInstance('', 'follow', array('title' => __halotext('Follow'), 'onClick' => "halo.follower.follow('" . $context . "','" . $target->id . "')",
                'zone' => $zone,
                'target' => $target,
                'class' => $class,
                'icon' => 'hand-o-right'));
        }
        return $builder->fetch();
    }

	/*
		apply default filter for listing
	*/
	public static function configureFilters(&$listingFilters, &$query) {
		
	}
	
	/*
		check and install filter for follower
	*/
	public static function setupFilters() {
		$filters = array(
						array(
							'name'      	=> 'follower.listing.type',
							'type' 	        => 'core', 
							'description' 	=> 'Filter followers by type',
							'on_display_handler' 	=> 'HALOFollowerModel::getFollowerType', 
							'on_apply_handler' 		=> 'HALOFollowerModel::getFollowerType', 
							'params' 		=> '{"title":"By Type"}',

							'published' => 1,

							'created_at' => new DateTime,
							'updated_at' => new DateTime,
						)
					);
		
		HALOFilter::insertNewFilters($filters);
		return true;
	}
	
    /**
     * Display filter to display list of post by status 
     * 
     * @param  string $params 
     * @param  string $uiType 
     * @return array         
     */
    public static function getFollowerTypeDisplayFilter($params, &$uiType)
    {
        $options = array();
		$options[] = HALOObject::getInstance(array('name' => __halotext('Followers'), 'value' => 'followers'));
		$options[] = HALOObject::getInstance(array('name' => __halotext('Following'), 'value' => 'following'));
        $uiType = 'filter.single_select';//user filter_tree_radio UI for this filter
        return $options;
    }

    /**
     * Apply filter by post status 
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  array $value  
     * @param  string $params 
     * @return bool 
     */
    public static function getFollowerTypeApplyFilter(&$query, $value, $params)
    {
        if (is_string($value)) {
            $values = explode(',', $value);
        } else if (is_array($value)) {
            $values = $value;
        }
        if (empty($values)) {
            //nothing to apply on the value
            return true;
        }

        return true;

    }
	
}
