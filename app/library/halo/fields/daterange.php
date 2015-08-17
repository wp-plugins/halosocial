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

class HALOFieldDaterange extends HALOField
{
	/**
	 * return editable html for this  field
	 */
	public function getEditableUI()
	{
		$subFieldsStr = $this->model->getParams('subFields');
		$subFields = json_decode($subFieldsStr);

		if (!is_array($subFields)) {
			return '';
		}
		//header is the first element of array
		$subFieldHeader = array_shift($subFields);

		//convert the subField code to subField input name
		if (!isset($subFields[0])) return '';
		$startField = 'field[' . HALOField::getFieldId($subFields[0][0]) . ']';
		$endField = 'field[' . HALOField::getFieldId($subFields[0][1]) . ']';

		$rangeFields = array();
		$rangeFields['start'] = $startField;
		$rangeFields['end'] = $endField;

		return HALOUIBuilder::getInstance('', 'field.daterange', array('id'          => $this->id,
		                                                             'title'       => $this->name,
		                                                             'field'       => $this->model,
		                                                             'halofield'     => $this,
		                                                             'rangeFields' => $rangeFields,
		                                                             'startField'  => $startField,
		                                                             'endField'    => $endField
		))->fetch();

	}

	/**
	 * Display field  as readable html
	 *
	 * @return Field html
	 */
	public function getReadableUI($template = "form.readonly_field", array $attr = array())
	{
		return $this->getEditableUI();
	}

}
