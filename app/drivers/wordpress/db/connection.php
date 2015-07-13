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

class HALODBConnection {

	protected static $instance = null;
	
	/*
	*	@api
	*	
	*/
	public static function getDBDriver(){
		//wordpress only support mysql driver
		return 'mysql';
	}
	/*
	*	@api
	*	
	*/
	public static function getOptions(){
		global $wpdb;
		$charset = DB_CHARSET;
		$collate = DB_COLLATE==''?'utf8_general_ci':DB_COLLATE;

		if ( 'utf8' === $charset && $wpdb->has_cap( 'utf8mb4' ) ) {
			$charset = 'utf8mb4';
		}

		if ( 'utf8mb4' === $charset && ( ! $collate || stripos( $collate, 'utf8_' ) === 0 ) ) {
			$collate = 'utf8mb4_unicode_ci';
		}
		$options = array(
			'mysql' => array(
				'driver'    => 'mysql',
				'host'      => DB_HOST,
				'database'  => DB_NAME,
				'username'  => DB_USER,
				'password'  => DB_PASSWORD,
				'charset'   => $charset,
				// 'collation' => 'utf8_unicode_ci',
				'collation' => $collate,
				'prefix'    => $wpdb->prefix,
				//'prefix'    => '',
			)
		);
		
		return $options;
	}
}
?>