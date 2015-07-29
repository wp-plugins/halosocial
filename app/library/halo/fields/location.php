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

class HALOFieldLocation extends HALOField
{
    protected $data = array('name' => '', 'lat' => '', 'lng' => '');
    /**
    * return editable html for this  field
    */
    public function getEditableUI()
    {
        $val = $this->value;
        $this->data = empty($val) ? $this->data : json_decode($val, true);

        $fieldBuilder = HALOUIBuilder::getInstance('', 'field.location', array('name' => 'field[' . $this->id . ']',
            																'value' => $this->data,
            																'title' => $this->name,
            																'helptext' => $this->tips,
            																'halofield' => $this,
            																'validation' => $this->getValidateValueString(),
            																'data' => array('confirm' => $this->data['name'],
            									)));
        return HALOUIBuilder::getInstance('', 'field.edit_layout', array('fieldHtml' => $fieldBuilder->fetch(),
        ))                                                                                         ->fetch();
    }

    /**
     * Display field value as html
     *
     * @return Field html
     */
    public function getValueUI($template = "form.readonly_field")
    {
        $val = $this->value;
        if ($val) {
            $loc = HALOLocationModel::find((int) $val);
            if ($loc) {
                return HALOUIBuilder::getInstance('', 'location_link', array('location' => $loc))->fetch();
            }
        }
        return __halotext("N/A");
    }
    /**
     * convert value before saving to database
     * 
     * @param  array $data
     * @return array
     */
    public function toPivotArray($data)
    {
        //rule: value must be converted to base unit before storing
        if (isset($data['value']['name']) && isset($data['value']['lat']) && isset($data['value']['lng'])) {
            $name = $data['value']['name'];
            $lat = $data['value']['lat'];
            $lng = $data['value']['lng'];
            $loc = HALOLocationModel::findLocation($name, $lat, $lng);
            $data['value'] = $loc ? $loc->id : '';
        } else {
            $data['value'] = '';
        }
        return parent::toPivotArray($data);
    }

    /*
    return location name for this field
     */
    public function getLocName()
    {
        return $this->data['name'];
    }

    /*
    return longitudevalue for this field
     */
    public function getLng()
    {
        return $this->data['lng'];

    }

    /*
    return latitude value for this field
     */
    public function getLat()
    {
        return $this->data['lat'];

    }
}
