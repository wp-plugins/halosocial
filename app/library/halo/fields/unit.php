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

class HALOFieldUnit extends HALOField 
{
	/**
	*return editable html for this  field
	 */
	
	public function getEditableUI() 
	{

		$val = $this->value;
		$val = empty($val) ? $this->model->getParams('default') : $this->value;

		$unitList = $this->getUnitList();
		//get baseUnit
		$baseUnit = $this->getBaseUnit();

		$baseUnitName = is_null($baseUnit) ? '' : $baseUnit[HALO_UNIT_TITLE_IND];
		$preferedUnit = $this->model->getParams('preferedUnit', $baseUnitName);

		$max = $this->model->getParams('vMax', '999999999999.99');
		$min = $this->model->getParams('vMin', 0);
		$tip = $this->tips;

		$fieldBuilder = HALOUIBuilder::getInstance('', 'field.unit', array('id' => $this->id, 
																		'name' => 'field[' . $this->id . ']',
																		'value' => Input::old('field.' . $this->id, $val),
																		'title' => $this->name,
																		'placeholder' => $this->model->getParams('placeholder',''),
																		'helptext' => $tip,
																		'field' => $this->model,
																		'halofield' => $this,
																		'data' => array('vMin' => $min,
																			'vMax' => $max,
																			'aDec' => $this->model->getParams('aDec', '.'),
																			'aSep' => $this->model->getParams('aSep', ','),
																			'mDec' => $this->model->getParams('mDec', 2),
																			'confirm' => Input::old('field.' . $this->id, $val),
																		),
																		'unit_name' => 'vparams[' . $this->id . '][unit]',
																		'preferedUnit' => $preferedUnit,
																		'unitList' => $unitList,
																		'validation' => $this->getValidateValueString()));

		return HALOUIBuilder::getInstance('', 'field.edit_layout', array('fieldHtml' => $fieldBuilder->fetch()))->fetch();

	}

	/**
	 * Display field  as readable html
	 *
	 * @return Field html
	 */
	public function getValueUI($template = "field.readable_unit") 
	{
		$val = $this->value;
		$val = empty($val) ? $this->model->getParams('default', '') : $this->value;

		if ($val === '') {
			return __halotext("N/A");
		}

		$unitList = $this->getUnitList();
		//get baseUnit
		$baseUnit = $this->getBaseUnit();

		$baseUnitName = is_null($baseUnit) ? '' : $baseUnit[HALO_UNIT_TITLE_IND];
		$preferedUnit = $this->model->getParams('preferedUnit', $baseUnitName);
		return HALOUIBuilder::getInstance('', $template, array('id' => $this->id,
															'name' => 'field[' . $this->id . ']',
															'value' => $val,
															'title' => $this->name,
															'field' => $this->model,
															'data' => array('vMin' => $this->model->getParams('vMin', 0),
																'vMax' => $this->model->getParams('vMax', '999999999999.99'),
																'aDec' => $this->model->getParams('aDec', '.'),
																'aSep' => $this->model->getParams('aSep', ','),
																'mDec' => $this->model->getParams('mDec', 2),
															),
															'unit_name' => 'vparams[' . $this->id . '][unit]',
															'preferedUnit' => $preferedUnit,
															'unitList' => $unitList))->fetch();
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
		if (isset($data['params']['unit'])) {
			$baseUnit = $this->getBaseUnit();
			//perform converting if base unit is not matched
			$unit = $this->getUnitByName($data['params']['unit']);
			if (!is_null($baseUnit) && !is_null($unit) && $baseUnit[HALO_UNIT_TITLE_IND] != $data['params']['unit']) {
				$data['value'] = $this->convertValue($data['value']) * $unit[HALO_UNIT_RATE_IND] / $baseUnit[HALO_UNIT_RATE_IND];
			} else {
				//stored value must be in math format
				$data['value'] = $this->convertValue($data['value']);
			}
		}
		return parent::toPivotArray($data);
	}
	/**
	 * get Unit List
	 * 
	 * @return array
	 */
	public function getUnitList() 
	{
		$unitList = $this->model->getParams('unitList');
		$unitList = json_decode($unitList);
		//unitList must be array of units
		if (!is_array($unitList)) {
			$unitList = array();//empty array
		} else {
			array_shift($unitList);
		}
		return $unitList;
	}
	/**
	 * return base unit of this field
	 * 
	 * @return array
	 */
	public function getBaseUnit() 
	{
		$baseUnit = null;
		$unitList = $this->getUnitList();
		//@rule: base unit is always the first unit
		if(!empty($unitList)){
			$baseUnit = array_shift($unitList);
		}
		return $baseUnit;
	}
	
	/**
	 * return base unit option index of this field
	 * 
	 * @return array
	 */
	public function getBaseUnitIndex() 
	{
		$baseUnitIndex = null;
		$unitList = $this->getUnitList();
		if(!empty($unitList)){
			reset($unitList);
			$baseUnitIndex = key($unitList);
		}
		return $baseUnitIndex;
	}
	/**
	 * get Unit by name
	 * 
	 * @param  string  $name
	 * @return mixed
	 */
	public function getUnitByName($name) 
	{
		$unitList = $this->getUnitList();
		foreach ($unitList as $unit) {
			if ($unit[HALO_UNIT_TITLE_IND] == $name) {
				return $unit;
			}
		}
		return null;
	}

	/**
	 * convert the display formated value to math value
	 * 
	 * @param  mixed  $val 
	 * @param  bool $toMath
	 * @return mixed $val
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
			$val = number_format($val, $mDec, $aDec, $aSep);

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
