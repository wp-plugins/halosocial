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

class HALOLabelModel extends HALOModel
{
    const POST_SOLD_STATUS = 'POST_SOLD_STATUS';


    public $timestamps = true;

    private $validator = null;

    protected $table = 'halo_labels';

    protected $fillable = array('name', 'label_code', 'group_id', 'label_type');

    private $_params = null;

    /**
     * Get validate rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required',
            'group_id' => 'required',
            'label_type' => 'required',
        ));

    }

    const LABEL_TYPE_MANUAL = 'manual';
    const LABEL_TYPE_TIMER = 'timer';

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOLabelGroupModel, HALOLabelModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function group()
    {
        return $this->belongsTo('HALOLabelGroupModel', 'group_id', 'id');
    }

    /**
     * HALOLabelModel, HALOLabelableModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function labelables()
    {
        return $this->hasMany('HALOLabelableModel', 'label_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Return list of label group types
     * 
     * @return array
     */
    public static function getLabelTypes()
    {
        return array(array('title' => __halotext('Manual Label'), 'value' => static::LABEL_TYPE_MANUAL)
            , array('title' => __halotext('Timer Label'), 'value' => static::LABEL_TYPE_TIMER)
        );
    }

    /**
     * Return label code for this label
     * 
     * @return string
     */
    public function getCode()
    {
        if ($this->id) {
            return empty($this->label_code) ? 'LABEL_' . $this->id : $this->label_code;
        } else {
            return '';
        }
    }

    /**
     * Return labels by group code
     * 
     * @param  string $groupCode 
     * @return array            
     */
    public static function getLabelsByGroupCode($groupCode)
    {
        $labelGroup = HALOLabelGroupModel::where('group_code', $groupCode)->first();
        if ($labelGroup) {
            return $labelGroup->labels;
        }
        return array();
    }

    /**
     * Return label by label code
     * 
     * @param  HALOLabelModel  $labelCode 
     * @return HALOLabelModel
     */
    public static function getLabelByCode($labelCode)
    {
        $label = HALOLabelModel::where('label_code', $labelCode)->first();
        return $label;
    }

    /**
     * Return style class setting for this label
     * 
     * @return object 
     */
    public function getStyleClass()
    {
        return $this->getParams('style', 'primary');
    }

    /**
     * Return display mame for this label
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * Return zone id for this model object
     * 
     * @param  string $level 
     * @return string
     */
    public function getGroupZone($level = null)
    {
        return $this->getContext() . '.group.' . $this->group_id;
    }

	public static function getSystemLabels(){
		return array('HALO_SYSTEM_LABEL_FEATURED', 'HALO_SYSTEM_LABEL_POPULAR', 'HALO_SYSTEM_LABEL_NEW');
	}
}
