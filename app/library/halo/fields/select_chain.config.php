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
 
	//return json string of HALOUIBuilderArray object
	return json_encode(array(//ui define
							array("title" => "SubSelect Fields",
								//sample format: json array string where first element must be header strings
								"_type" => "form.jsontable",							
								//[["fieldCode"],["FIELD_1"],["FIELD_2"]]
								"name" => "subFields",
								"helptext" => __halotext('List of fields in this select chain'),
								"default" => '[["fieldCode"]]'),
							array("title" => "SubSelect Options",						
								//sample format: json array string where first element must be header strings
								"_type" => "form.jsontable",							
								//[["value","title","parent","level"],["dt1","sq1","","0"],["dt2","sq2","","0"],["dt3","sq3","","0"],["wc1-1","toliet1-1","dt1","1"],["wc1-2","toliet1-2","dt1","1"],["wc1-3","toliet1-3","dt1","1"],["wc2-1","toliet2-1","dt2","1"],["wc2-2","toliet2-2","dt2","1"],["wc2-3","toliet2-3","dt2","1"],["wc3-1","toliet3-1","dt3","1"],["wc3-2","toliet3-2","dt3","1"],["wc3-3","toliet3-3","dt3","1"]]
								"name" => "subOptions",
								"helptext" => __halotext('List of field values/options in this select chain'),
								"default" => '[["value","title","level","parent"]]')
							)
						);
											

?>