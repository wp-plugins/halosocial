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
							array("title" => "Media Type","helptext" => __halotext('Select media type'),
								"_type" => "form.radio",
								"options" => array(array("title" => "Photo","value" => "photo"),
													array("title" => "File","value" => "file")
												),
									"name" => "mediaType")
							/*		
							,array(	"title"=>"Accepted Extensions","helptext"=>'Configure file extensions allowed to upload. Use comma (,) to separate multiple file extensions',
									"_type"=>"form.text",
									"name"=>"extensions")
							*/
							)
						);
											

?>