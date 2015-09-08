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

class HALOLazyLoadHelper 
{
	/**
	 * load data
	 * 
	 * @param  object $data
	 * @param  string $template
	 * @return collection instance
	 */
	public static function load($data, $template) 
	{
		if (!is_object($data)) {
			return array();
		}
		//init collection instance
		if (is_a($data, 'Illuminate\Pagination\Paginator')) {
			$collection = new Illuminate\Database\Eloquent\Collection($data->getItems());
		} else if (is_array($data)) {
			$collection = new Illuminate\Database\Eloquent\Collection($data);
		} else if (is_a($data, 'Illuminate\Database\Eloquent\Collection')) {
			$collection = $data;
		} else {
			//unsupport $data type
			return new Illuminate\Database\Eloquent\Collection();
		}
		$templateValue = HALOConfig::get('lazyload.template.' . $template, '');
		$values = array();
		if ($templateValue) {
			$values = explode(',', $templateValue);
		}
		foreach ($values as $key => $value) {
			$value = trim($value);
			try {
				if (strpos($value, '_counter') === 0) {
					//counter lazyload
					$value = strtolower(substr($value, 8));
					if ($value) {
						HALOModelHelper::loadRelationCounter($collection, array($value));
					}
				} else {
					call_user_func_array(array($collection, 'load'), array($value));
				}
			} catch (\Exception $e) {

			}
		}
		return $collection;
	}
}
