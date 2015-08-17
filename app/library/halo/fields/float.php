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

class HALOFieldFloat extends HALOField
{

    /**
     *  return editable html for this  field
     *  
     * @return HALOUIBuilder
     */
    public function getEditableUI()
    {

        $val = $this->value;
        $val = empty($val) ? $this->model->getParams('default') : $this->value;
        $max = $this->model->getParams('vMax', '999999999999.99');
        $min = $this->model->getParams('vMin', 0);
        $tip = $min ? ($this->tips . ' ' . sprintf(__halotext('(min: %s - max: %s)'), $min, $max)) : ($this->tips . ' ' . sprintf(__halotext('(max: %s)'), $max));

        $fieldBuilder = HALOUIBuilder::getInstance('', 'field.float', array('id' => $this->id, 'name' => 'field[' . $this->id . ']',
            															'value' => Input::old('field.' . $this->id, $val),
           		 														'title' => $this->name,
            															'placeholder' => $this->model->getParams('placeholder', ''),
            															'helptext' => $tip,
            															'field' => $this->model,
            															'halofield' => $this,
            															'data' => array('vMin' => $min,
               																			'vMax' => $max,
                																		'aDec' => $this->model->getParams('aDec', '.'),
                																		'aSep' => $this->model->getParams('aSep', ','),
                																		'mDec' => $this->model->getParams('mDec', 2),
                                                                                        'aNotCheck' => true,
                																		'confirm' => Input::old('field.' . $this->id, $val),
            																			),
            															'validation' => $this->getValidateValueString()));

        return HALOUIBuilder::getInstance('', 'field.edit_layout', array('fieldHtml' => $fieldBuilder->fetch()))->fetch();

    }
    /**
     * Display field  as readable html
     *
     * @return Field html
     */
    public function getValueUI($template = "form.readonly_field")
    {
        $val = $this->value;
        $val = empty($val) ? $this->model->getParams('default') : $this->value;
        $value = $this->convertValue($val, false);
        return ($value === '') ? __halotext("N/A") : $value;
    }
    /**
     * 
     * @param  array $data
     * @return array
     */
    public function toPivotArray($data)
    {
        $data['value'] = $this->convertValue($data['value']);
        return parent::toPivotArray($data);
    }

    /**
     * convert the display formated value to math value
     * 
     * @param  mixed  $val
     * @param  bool $toMath
     * @return mixed
     */
    public function convertValue($val, $toMath = true)
    {
        $aDec = $this->model->getParams('aDec', '.');
        $aSep = $this->model->getParams('aSep', ',');
        $mDec = $this->model->getParams('mDec', 2);
        if ($toMath) {
            //remove all thoudsan separators
            $val = str_replace($aSep, '', $val);
            //change the decimal separator to dot charactor
            $val = str_replace($aDec, '.', $val);
        } else {
            $val = @number_format((double) $val, $mDec, $aDec, $aSep);

        }
        return $val;
    }

    /**
     * Return validation rule for value of this field
     * 
     * @return array valiation value rule
     */
    public function getValidateValueRule()
    {
        //the validate value rule is stored in params as name validate_rule
        $rule = $this->getValidateValueString();
        $ruleList = empty($rule) ? array() : explode('|', $rule);
        //add min/max rule if configured
        $min = $this->model->getParams('vMin', '');
        if ($min !== '') {
            $ruleList[] = 'minval:' . $min;
        }

        $max = $this->model->getParams('vMax', '');
        if ($max !== '') {
            $ruleList[] = 'maxval:' . $max;
        }

        return array('field.' . $this->model->id => implode('|', $ruleList));
    }

}
