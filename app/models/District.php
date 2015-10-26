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

class HALODistrictModel extends HALOModel
{
    public $timestamps = false;

    protected $table = 'halo_districts';

    protected $fillable = array('name', 'city_id');
    
    /////////////////// Define ORM //////////////////////

    /**
     * relationship: city has many districts
     *
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function city()
    {
        return $this->belongsTo('HALOCityModel', 'city_id');
    }

    /**
     * Relationshiip: district has many location(address)
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function locations()
    {
        return $this->hasMany('HALOLocationModel', 'district_id');
    }

    /**
     * Return display name for this model
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * Return number of location associate for this district
     * 
     * @return int
     */
    public function getLocationsCount()
    {
        return $this->locations->count();
    }
}
