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
 
return array(
	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	"accepted"         => __halotext("The :attribute must be accepted."),
	"active_url"       => __halotext("The :attribute is not a valid URL."),
	"after"            => __halotext("The :attribute must be a date after :date."),
	"alpha"            => __halotext("The :attribute may only contain letters."),
	"alpha_dash"       => __halotext("The :attribute may only contain letters, numbers, and dashes."),
	"alpha_num"        => __halotext("The :attribute may only contain letters and numbers."),
	"before"           => __halotext("The :attribute must be a date before :date."),
	"between"          => array(
		"numeric" => __halotext("The :attribute must be between :min - :max."),
		"file"    => __halotext("The :attribute must be between :min - :max kilobytes."),
		"string"  => __halotext("The :attribute must be between :min - :max characters."),
	),
	"confirmed"        => __halotext("The :attribute confirmation does not match."),
	"date"             => __halotext("The :attribute is not a valid date."),
	"date_format"      => __halotext("The :attribute does not match the format :format."),
	"different"        => __halotext("The :attribute and :other must be different."),
	"digits"           => __halotext("The :attribute must be :digits digits."),
	"digits_between"   => __halotext("The :attribute must be between :min and :max digits."),
	"email"            => __halotext("The :attribute format is invalid."),
	"exists"           => __halotext("The selected :attribute is invalid."),
	"image"            => __halotext("The :attribute must be an image."),
	"in"               => __halotext("The selected :attribute is invalid."),
	"integer"          => __halotext("The :attribute must be an integer."),
	"ip"               => __halotext("The :attribute must be a valid IP address."),
	"max"              => array(
		"numeric" => __halotext("The :attribute may not be greater than :max."),
		"file"    => __halotext("The :attribute may not be greater than :max kilobytes."),
		"string"  => __halotext("The :attribute may not be greater than :max characters."),
	),
	"mimes"            => __halotext("The :attribute must be a file of type: :values."),
	"min"              => array(
		"numeric" => __halotext("The :attribute must be at least :min."),
		"file"    => __halotext("The :attribute must be at least :min kilobytes."),
		"string"  => __halotext("The :attribute must be at least :min characters."),
	),
	"not_in"           => __halotext("The selected :attribute is invalid."),
	"numeric"          => __halotext("The :attribute must be a number."),
	"regex"            => __halotext("The :attribute format is invalid."),
	"required"         => __halotext("The :attribute field is required."),
	"required_if"      => __halotext("The :attribute field is required when :other is :value."),
	"required_with"    => __halotext("The :attribute field is required when :values is present."),
	"required_without" => __halotext("The :attribute field is required when :values is not present."),
	"same"             => __halotext("The :attribute and :other must match."),
	"size"             => array(
		"numeric" => __halotext("The :attribute must be :size."),
		"file"    => __halotext("The :attribute must be :size kilobytes."),
		"string"  => __halotext("The :attribute must be :size characters."),
	),
	"unique"           => __halotext("The :attribute has already been taken."),
	"url"              => __halotext("The :attribute format is invalid."),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
