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

class PushClient 
{
	/**
	 * broadcast
	 * 
	 * @param  array $attenders
	 * @param  string $message
	 * @param  string $params 
	 * @return mixed
	 */
 	public static function broadcast($attenders,$message='',$params=''){
		$data = new stdClass();
		$data->message = $message;
		$data->params = $params;
		if(!is_array($attenders)){
			$data->users = implode(',',$attenders);
		} else {
			$data->users = $attenders;
		}
		//set host
		$data->host = Request::getHost();
		$data_string = json_encode($data);                                                                                   
		$url = HALOConfig::get('pushserver.address') . '/bcast';
		$ch = curl_init(HALOConfig::get('pushserver.address'));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string))
		);
		 
		$result = curl_exec($ch);
		
	}
}