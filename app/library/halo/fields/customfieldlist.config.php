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
 
	//return array of custom field
	return json_encode(array(//ui define
							array("title" => "Text", "value" => "text"),
							array("title" => "Textarea", "value" => "textarea"),
							array("title" => "Select", "value" => "select"),
							array("title" => "Radio", "value" => "radio"),
							array("title" => "Checkbox", "value" => "checkbox"),
							array("title" => "Switch", "value" => "switch"),
							array("title" => "Multiple Select", "value" => "select_multiple"),
							array("title" => "Gender", "value" => "gender"),
							// array("title" => "Editable Table", "value" => "table"),
							array("title" => "Date", "value" => "date"),
							// array("title" => "DateRange", "value" => "daterange"),
							array("title" => "Time", "value" => "time"),
							array("title" => "DateTime", "value" => "datetime"),
							array("title" => "Number", "value" => "float"),
							array("title" => "Unit", "value" => "unit"),
							array("title" => "Media", "value" => "media"),
							array("title" => "Location", "value" => "location"),
							// array("title" => "Chain select", "value" => "select_chain"),
							// array("title" => "Group", "value" => "group"),
							array("title" => "Tab", "value" => "tab"),
							array("title" => "Separator", "value" => "separator")
						));
										

?>