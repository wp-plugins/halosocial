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

class HALOFieldRadio extends HALOField
{

    /**
     * return editable html for this  field
     * 
     * @return HALOUIBuilder
     */
    public function getEditableUI()
    {

        $val = $this->value;
        $val = empty($val) ? $this->model->getParams('default') : $this->value;

        $optionsString = $this->model->getParams('options');
        $options = HALOUtilHelper::parseHtmlInputOption($optionsString);
        $fieldBuilder = HALOUIBuilder::getInstance('', 'field.radio', array('name' => $this->getFieldName(),
															            'value' => Input::old('field.' . $this->id, $val),
															            'title' => $this->name,
															            'helptext' => $this->tips,
															            'options' => $options,
															            'field' => $this->model,
															            'halofield' => $this,
															            'validation' => $this->getValidateValueString()));
        return HALOUIBuilder::getInstance('', 'field.edit_layout', array('fieldHtml' => $fieldBuilder->fetch()
        	))->fetch();

    }

    /**
     * Display field value as html
     *
     * @return Field html
     */
    public function getValueUI($template = "form.readonly_field")
    {
        $val = $this->value;
        $val = empty($val) ? $this->model->getParams('default') : $this->value;

        $optionsString = $this->model->getParams('options');
        $options = HALOUtilHelper::parseHtmlInputOption($optionsString);

        $value = $this->getOptionTitle($options, $val);

        return ($value === null) ? __halotext("N/A") : $value;

    }

    /**
     * get option title from given value 
     * 
     * @param  array $options
     * @param  mixed $val 
     * @return mixed
     */
    public function getOptionTitle($options, $val)
    {
        foreach ($options as $option) {
            if ($option->value == $val) {
                return $option->title;
            }
        }
        return null;
    }

}
