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
return json_encode(array(
    //ui define
    array("title" => "Options",

        //sample format: json array string where first element must be header strings
        "_type" => "form.jsontable",

        //[["title","value"],["Yes","1"],["No","0"]]
        "helptext" => __halotext('List of selectable options'),
        "name" => "options",
		"class" => "halo-hide-header",
        "default" => '[["title"]]'),
    array("title" => "Validate Rule",
        "_type" => "form.text",
		"class" => "hidden",
        "placeholder" => __halotext('Validation rule for this field'),
        "name" => "validate_rule"),
    array("title" => "Default Value",
        "_type" => "form.hidden",
        "placeholder" => __halotext('Default text value for this field'),
        "name" => "default"),
)
);
