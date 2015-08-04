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

class HALOLabelGroupModel extends HALOModel
{
    

    public $timestamps = true;

    protected $table = 'halo_label_groups';

    protected $fillable = array('name', 'group_code', 'group_type');

    private $validator = null;

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
            'group_type' => 'required',
        ));

    }

    const GROUP_TYPE_SINGLE = 0;
    const GROUP_TYPE_MULTIPLE = 1;

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOLabelGroupModel, HALOLabelModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function labels()
    {
        return $this->hasMany('HALOLabelModel', 'group_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Return list of label group types
     * 
     * @return array
     */
    public static function getGroupTypes()
    {
        return array(array('title' => __halotext('Single stick'), 'value' => static::GROUP_TYPE_SINGLE)
            , array('title' => __halotext('Multiple stick'), 'value' => static::GROUP_TYPE_MULTIPLE)
        );
    }

    /**
     * Return group code for this label group
     * 
     * @return string
     */
    public function getCode()
    {
        if ($this->id) {
            return empty($this->group_code) ? 'GROUPLABEL_' . $this->id : $this->group_code;
        } else {
            return '';
        }
    }

    /**
     * Return type for this label group
     * 
     * @return string
     */
    public function getType()
    {
        if ($this->id) {
            $types = HALOLabelGroupModel::getGroupTypes();
            foreach ($types as $type) {
                if ($type['value'] == $this->group_type) {
                    return $type['title'];
                }
            }
            return '';
        } else {
            return '';
        }
    }

    /**
     * Return label option of a label group
     * 
     * @return array
     */
    public function getLabelOptions()
    {
        $options = array();
        foreach ($this->labels as $label) {
            $options[] = array('title' => $label->name, 'value' => $label->id, 'style' => $label->getStyleClass());
        }
        return $options;
    }

    /**
     * Return label group value
     * 
     * @param  string  $labelIds 
     * @param  boolean $single     
     * @return array              
     */
    public function getLabelValue($labelIds, $single = true)
    {
        $values = array();
        foreach ($this->labels as $label) {
            if (in_array($label->id, $labelIds)) {
                $values[] = $label->id;
            }
        }
        if ($single) {
            return empty($values) ? '' : array_shift($values);
        } else {
            return $values;
        }
    }

	public static function hasLabels($context) {
		static $cached = array();
		if(empty($context)) {
			return false;
		}
		$data = array_merge((array)HALOConfig::get($context . '.label.status'), (array)HALOConfig::get($context . '.label.badge'));
		if(empty($data)) return false;
		$hash = HALOUtilHelper::getHashArray($data);
		if(!isset($cached[$hash])){
			$groups = HALOLabelGroupModel:: whereIn('group_code', $data)->get();
			foreach($groups as $group) {
				if($group->labels->count()) {
					return $cached[$hash] = true;
				}
			}
			$cached[$hash] = false;
		}
		return $cached[$hash];
	}
}
