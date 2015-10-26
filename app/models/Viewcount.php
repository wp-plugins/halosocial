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

class HALOViewcounterModel extends HALOModel
{
	public $timestamps = false;

    protected $table = 'halo_view_counters';

    protected $fillable = array('viewable_type', 'viewable_id', 'counter');

    protected static $_counters = array();
    //////////////////////////////////// Define Relationships /////////////////////////
    //define polymorphic relationship
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function viewable()
    {
        return $this->morphTo();
    }
    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Function to update view counter of a target model
     * 
     * @param  int $target 
     */
    public static function updateCounter($target)
    {
        $counterId = 'v' . get_class($target) . $target->id;
        if (!Session::get($counterId, null)) {
            //update the counter to db
            $model = HALOViewcounterModel::where('viewable_type', get_class($target))->where('viewable_id', $target->id)->first();
            if ($model) {
                $model->counter++;
            } else {
                $model = new HALOViewcounterModel(array('viewable_type' => get_class($target), 'viewable_id' => $target->id, 'counter' => 1));
            }
            $model->save();
            //cache the counter
            self::$_counters[$counterId] = $model;
            //mark the target as viewed
            Session::put($counterId, 1);
        }
    }

    /**
     * Function ti get view counter of target models. Note: target model must the same classname
     * 
     * @param  int  $targets 
     * @param  boolean $update  
     * @return int           
     */
    public static function getCounter($targets, $update = true)
    {
        $singleCounter = false;
        if (!is_array($targets)) {
            $singleCounter = true;
            $targetIds = array($targets->id);
            $viewable_type = get_class($targets);

            //force to update viewcounter
            if ($update) {
                self::updateCounter($targets);
            }

            $counterId = 'v' . get_class($targets) . $targets->id;
            //use local cache for single view
            if (isset(self::$_counters[$counterId])) {
                return self::$_counters[$counterId]->counter;
            }
        } else {
            $func = function ($value) {
                return $value->id;
            };

            $targetIds = array_map($func, $targets);
            $viewable_type = get_class(end($targets));
        }
        $counters = HALOViewcounterModel::where('viewable_type', $viewable_type)->whereIn('viewable_id', $targetIds)->lists('counter');
        return $singleCounter ? (int) end($counters) : $counters;

    }
}
