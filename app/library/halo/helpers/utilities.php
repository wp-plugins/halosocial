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

class HALOUtilHelper
{
	/**
	 * Return a hash string of an array that safety for memmory caching
	 * 
	 * @param	array	input array
	 * @return	string	hash string
	 */
	public static function getHashArray(array $a) 
	{
		$string = serialize($a);
		return md5($string);
	}
	/**
	 * render Message
	 * 
	 * @param  string $message
	 * @return string
	 */
	public static function renderMessage($message) 
	{
		//search for user tagging
		$pattern = '#@\[([0-9]+) ([\w| ]+)\]#u';
		$matches = array();
		$occur = preg_match_all($pattern, $message, $matches);

		if ($occur) {
			$userIds = array();
			$searches = array();
			$replaces = array();
			for ($i = 0; $i < $occur; $i++) {
				$userIds[] = $matches[1][$i];
				$searches[$matches[1][$i]] = $matches[0][$i];
				$replaces[$matches[1][$i]] = $matches[2][$i];
			}
			//load all user to cache
			$users = HALOUserModel::init($userIds);

			//replace user tagging with user DisplayLink
			foreach ($searches as $id => $s) {
				$user = HALOUserModel::getUser($id);
				if ($user) {
					$r = $user->getDisplayLink();
				} else {
					$r = $replaces[$id];
				}
				$message = str_replace($s, $r, $message);
			}
		}

		return HALOOutputHelper::text2html($message);
	}

	/**
	 * return date time format based on configuration for a given date time string
	 * 
	 * @param  string $datetime 
	 * @return string
	 */
	public static function getDateTime($datetime) 
	{
		return date_create($datetime)->format(HALO_DATE_TIME_FORMAT);
	}

	/**
	 * tokenize a string 
	 * 
	 * @param  string $string
	 * @param  string $separator
	 * @return mixed
	 */
	public static function tokenize($string, $separator = '')
	{
		$key = 'tokenize_' . md5($string . '_' . $separator);
		return Cache::rememberForever($key,function() use($string, $separator){
			//$tokens = $string;
			$tokens = preg_split("/[\s,\|]+/",$string);
			if($separator == ''){
				//ouput an array
			} else {
				//ouput a string with separator
				$tokens = implode($separator,$tokens);
			}
			return $tokens;
		});
	}

	/*
	return elapsed time
	 */
	/**
	 * get Elapse Time
	 * 
	 * @param  string $datetime
	 * @return string
	 */
	public static function getElapsedTime($datetime) 
	{
		return self::getDiffForHumansTrans($datetime->diffForHumans());
	}
	/**
	 * get Diff For Humans Trans
	 * 
	 * @param  string $str 
	 * @return mixed
	 */
	public static function getDiffForHumansTrans($str) 
	{
		$patterns = array(
            '/seconds*/',
			'/minutes*/',
			'/hours/',
			'/days*/',
			'/weeks*/',
			'/months*/',
			'/years*/',
			'/ago/',
			'/after/',
			'/before/',
			'/from now/',
		);

		$replaces = array(
			__halotext('second'),
			__halotext('minute'),
			__halotext('hour'),
			__halotext('day'),
			__halotext('week'),
			__halotext('month'),
			__halotext('year'),
			__halotext('ago'),
			__halotext('after'),
			__halotext('before'),
			__halotext('from now'),
		);

		return preg_replace($patterns, $replaces, $str);
	}

	/**
	 * return timestamp for a given date time string
	 * 
	 * @param  string $datetime
	 * @return string 
	 */
	public static function getDataUTime($datetime) 
	{
		$date = date_create($datetime);
		//rule: only do live elapsed time update for the timediff below 1 day
		if ($datetime->diffInDays(Carbon::now()) >= 1) {
			return '';
		}

		return 'data-utime="' . $date->getTimestamp() . '"';
	}

	/**
	 * convert html input options string to array
	 * 
	 * @param  string $optionsString
	 * @return array
	 */
	public static function parseHtmlInputOption($optionsString) 
	{
		$options = array();
		$arr = json_decode($optionsString);
		if (is_array($arr) && count($arr) > 1) {
			$header = array_shift($arr);//get the header
			if (is_array($header)) {
				$titleInd = array_search('title', $header);
				$valueInd = array_search('value', $header);
				//@rule: if value index is not defined, use the same index for title and value
				if($valueInd === false) {
					$valueInd = $titleInd;
				}
				if ($titleInd !== false && $valueInd !== false) {
					foreach ($arr as $e) {
						$title = $e[$titleInd];
						$value = (!isset($e[$valueInd]) || $e[$valueInd] === '')?$title:$e[$valueInd];
						$options[] = HALOObject::getInstance(array('title' => $title, 'value' => $value));
					}
				}

			}
		}
		return $options;
	}


	/**
	 * convert string from camelCase to snake-case format
	 * 
	 * @param  string $input
	 * @param  string $sep
	 * @return string
	 */
	public static function camelCase2SnakeCase($input, $sep = '-') 
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode($sep, $ret);

	}


	/**
	 * convert string to alpha string
	 * 
	 * @param  string  $in 
	 * @param  bool $to_num 
	 * @param  bool $pad_up
	 * @param  string $pass_key 
	 * @return string 
	 */
	public static function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null) 
	{
		$out = '';
		$index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base = strlen($index);

		if ($pass_key !== null) {
			// Although this function's purpose is to just make the
			// ID short - and not so much secure,
			// with this patch by Simon Franz (http://blog.snaky.org/)
			// you can optionally supply a password to make it harder
			// to calculate the corresponding numeric ID

			for ($n = 0; $n < strlen($index); $n++) {
				$i[] = substr($index, $n, 1);
			}

			$pass_hash = hash('sha256', $pass_key);
			$pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

			for ($n = 0; $n < strlen($index); $n++) {
				$p[] = substr($pass_hash, $n, 1);
			}

			array_multisort($p, SORT_DESC, $i);
			$index = implode($i);
		}

		if ($to_num) {
			// Digital number  <<--  alphabet letter code
			$len = strlen($in) - 1;

			for ($t = $len; $t >= 0; $t--) {
				$bcp = bcpow($base, $len - $t);
				$out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
			}

			if (is_numeric($pad_up)) {
				$pad_up--;

				if ($pad_up > 0) {
					$out -= pow($base, $pad_up);
				}
			}
		} else {
			// Digital number  -->>  alphabet letter code
			if (is_numeric($pad_up)) {
				$pad_up--;

				if ($pad_up > 0) {
					$in += pow($base, $pad_up);
				}
			}

			for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
				$bcp = bcpow($base, $t);
				$a = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
				$in = $in-($a * $bcp);
			}
		}

		return $out;
	}

	/**
	 * return mine string of a file
	 * 
	 * @param  string $filename
	 * @return string
	 */
	public static function mime_content_type($filename) 
	{

		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = File::extension($filename);
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		} elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		} else {
			return 'application/octet-stream';
		}

	}

	/**
	 * perform file download
	 * 
	 * @param  string $file 
	 * @param  string $filename
	 * 
	 */
	public static function download($file, $filename) 
	{
		$type = self::mime_content_type($file);
		header("Content-type: $type");

		header("Content-Disposition: attachment;filename=$filename");

		header("Content-Transfer-Encoding: binary");

		header('Pragma: no-cache');

		header('Expires: 0');

		set_time_limit(0);

		readfile($file);
	}

	/**
	 * function to override first array values with passing parameters
	 * 
	 * @return array
	 */
	public static function array_override_recursive() 
	{
		$numargs = func_num_args();
		$arg_list = func_get_args();
		if ($numargs = 0) {
			return;
		} else if ($numargs == 1) {
			return $arg_list[0];
		} else {
			if (is_array($arg_list[0])) {
				foreach ($arg_list[0] as $key => $val) {
					$args = array($val);
					$params = array_slice($arg_list, 1);
					foreach ($params as $param) {
						if (isset($param[$key])) {
							$args[] = $param[$key];
						}
					}
					$arg_list[0][$key] = call_user_func_array(array('HALOUtilHelper', "array_override_recursive"), $args);
				}
			} else {
				$arg_list[0] = end($arg_list);
			}
			return $arg_list[0];
		}
	}

	/**
	 * join a HALOUserModel query builder with UserTable
	 * 
	 * @param  Illuminate\Database\Query\Builder $query
	 * @param  array  $columns 
	 * @return Illuminate\Database\Query\Builder
	 */
	public static function joinUserTable($query, $columns = array(HALO_USER_DISPLAY_NAME_COL => 'name')) 
	{
		if (!isset($query->_hasUserTable) || !$query->_hasUserTable) {
			$userModel = new UserModel();
			$userTable = $userModel->getTable();

			$query = $query->leftJoin($userTable, 'halo_users.id', '=', $userTable . '.id');

			//select columns
			$columns = (array) $columns;
			foreach ($columns as $key => $alias) {
				$col = is_int($key) ? $alias : $key;
				$query = $query->select($userTable . '.' . $col . ' as ' . $alias);
			}
			//users table id and halouser table id is ambiguous, so we get all halo_users table column
			$query = $query->select('halo_users.*');
			//then mark the query as joined to prevent duplicate join
			$query->_hasUserTable = true;
		}
		return $query;
	}


	/**
	 * create an random string
	 * 
	 * @param  int $length
	 * @return string
	 */
	public static function generateRandomString($length = 10) 
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}


	/**
	 * return slot of a value in array
	 * 
	 * @param  array  $search 
	 * @param  int $value 
	 * @return int
	 */
	public static function array_slot(array $search, $value) 
	{
		sort($search);
		$slot = 0;
		$i = 0;
		while (($i < count($search)) && ((int) $search[$i] <= $value)) {
			$slot++;
			$i++;
		}
		return $slot;
	}

	/**
	 * return a list of setting options in a namespace
	 * 
	 * @param  HALOObject $settings
	 * @param  string   $namespace
	 * @return array
	 */
	public static function getSettingList(HALOObject $settings, $namespace) 
	{
		$list = $settings->getNsValue($namespace);
		if (is_object($list)) {
			if (isset($list->_meta)) {
				//do not return the _meta item in the list
				unset($list->_meta);
			}
			return get_object_vars($list);
		}
		return array();
	}


	/**
	 * load social settings
	 * 
	 * @return HALOObject
	 */
	public static function getSocialSettings() 
	{
		static $meta = null;
		if (is_null($meta)) {
			$meta = new HALOObject();

			Event::fire('social.onLoadSettings', array(&$meta));
		}
		return $meta;
	}

	/**
	 * parse client browser
	 * 
	 * @return array
	 */
	public static function getBrowser() 
	{

		$u_agent = $_SERVER['HTTP_USER_AGENT'];

		$bname = 'Unknown';
		$platform = 'Unknown';
		$version = "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		} elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {

			$bname = 'Internet Explorer';

			$ub = "MSIE";

		} elseif (preg_match('/Firefox/i', $u_agent)) {

			$bname = 'Mozilla Firefox';

			$ub = "Firefox";

		} elseif (preg_match('/Chrome/i', $u_agent)) {

			$bname = 'Google Chrome';

			$ub = "Chrome";

		} elseif (preg_match('/Safari/i', $u_agent)) {

			$bname = 'Apple Safari';

			$ub = "Safari";

		} elseif (preg_match('/Opera/i', $u_agent)) {

			$bname = 'Opera';

			$ub = "Opera";

		} elseif (preg_match('/Netscape/i', $u_agent)) {

			$bname = 'Netscape';

			$ub = "Netscape";

		} else {
			$bname = 'Unknown';

			$ub = 'Unknown';
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
				$version = $matches['version'][0];
			} else {
				$version = isset($matches['version'][1]) ? $matches['version'][1] : 'Unknown';
			}
		} else {
			$version = $matches['version'][0];
		}

		// check if we have a number
		if ($version == null || $version == "") {$version = "?";}

		return array(
			'b_userAgent' => $u_agent,
			'b_name' => $bname,
			'b_version' => $version,
			'b_platform' => $platform,
			'b_pattern' => $pattern,
		);
	}


	/**
	 * curl a target url and return the response string
	 * 
	 * @param  string $url 
	 * @param  string $format 
	 * @return bool
	 */
	public static function getCurl($url, $format = 'json') 
	{
		if (!function_exists('curl_version')) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Curl is not enabled')));
			return null;
		}
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$return = curl_exec($curl);
			curl_close($curl);
			return json_decode($return, true);

		} catch (\Exception $e) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Error occurred while fetching URL content')));
			return null;
		}
	}

	/**
	 * return a uniqid integer value
	 * 
	 * @return int
	 */
	public static function uniqidInt() 
	{
		static $counter = 10;
		$period = 10000;//10 s uniqid period
		$sample = microtime(true);
		$sample = floor($sample * 1000);
		$val = (++$counter * $period) + $sample % 10000;

		return $val;
	}

	/**
	 * convert array list to select option format
	 * 
	 * @param  array  $collection
	 * @param  string  $valKey 
	 * @param  string  $titleKey 
	 * @param  bool $addBlank 
	 * @param  string  $blankText
	 * @return array
	 */
	public static function collection2options($collection, $valKey, $titleKey, $addBlank = true, $blankText = null) 
	{
		$options = array();
		if ($addBlank) {
			$blankText = ($blankText === null) ? __halotext('--Select--') : $blankText;
			$options[] = array('value' => '', 'title' => $blankText);
		}
		foreach ($collection as $option) {
			if(method_exists($option, $valKey)) {
				$value = strip_tags(call_user_func_array(array($option, $valKey), array()));
			} else {
				$value = strip_tags($option->$valKey);
			}
			if(method_exists($option, $titleKey)) {
				$title = strip_tags(call_user_func_array(array($option, $titleKey), array()));
			} else {
				$title = strip_tags($option->$titleKey);
			}
			$options[] = array('value' => $value, 'title' => $title);
		}
		return $options;
	}

	/**
	 * pagination help to do the lazy load
	 * 
	 * @param  object $paginator 
	 * @param  array $arr
	 * @return mixed
	 */
	public static function paginatorLoad($paginator, $arr) 
	{
		$tmp = new Illuminate\Database\Eloquent\Collection($paginator->getItems());
		return call_user_func_array(array($tmp, 'load'), $arr);
	}

	/**
	 * array model lazy load
	 * 
	 * @param  array  $models
	 * @param  array $arr
	 * @return array
	 */
	public static function lazyLoadArray(array $models, $arr) 
	{
		$tmp = new Illuminate\Database\Eloquent\Collection($models);
		return call_user_func_array(array($tmp, 'load'), $arr);

	}

	/**
	 * function to return raw query condition statement for text searching
	 * 
	 * @param  string $col
	 * @param  string $val
	 * @return string  
	 */
	public static function getTextSearchCondition($col, $val) 
	{
		if ($val !== '') {
			$val = DB::getPdo()->quote("%" . $val . "%");
			return "(lower(" . $col . ") like " . strtolower($val) . " or lower(" . $col . ") like " . strtolower($val) . ")";
		} else {
			return '1=1';
		}

	}


	/**
	 * function to sort category tree in alphabeta order
	 * 
	 * @param  array $categories
	 * @return array
	 */
	public static function sortCategoryAlphaBeta($categories) 
	{
		$rtn = array();
		foreach ($categories as $cat) {
			$char = ucfirst(substr(Str::slug($cat->name), 0, 1));
			if (!isset($rtn[$char])) {
				$rtn[$char] = array($cat);
			} else {
				$rtn[$char][] = $cat;
			}
		}
		ksort($rtn);
		return $rtn;
	}

	/**
	 * lazy load post counter of category tree
	 * 
	 * @param  array $categories 
	 * @return $collection 
	 */
	public static function loadCategoryPostCounter(&$categories) 
	{
		$tmpCats = array();
		$oldIndex = 0;
		$newIndex = 0;
		foreach ($categories as $cat) {
			$tmpCats[$newIndex++] = $cat;
		}
		while ($oldIndex != $newIndex) {
			$endIndex = $newIndex;
			for ($i = $oldIndex; $i < $endIndex; $i++) {
				if (isset($tmpCats[$i]->_children) && !empty($tmpCats[$i]->_children)) {
					foreach ($tmpCats[$i]->_children as $child) {
						$tmpCats[$newIndex++] = $child;
					}
				}
			}
			$oldIndex = $endIndex;
		}
		$collection = new Illuminate\Database\Eloquent\Collection($tmpCats);
		HALOModelHelper::loadRelationCounter($collection, array('posts'));
	}
	/*
	function to check if the uploaded file exceed the limiation size configuration
	 */
	/**
	 * function to check if the uploaded file exceed the limiation size configuration
	 * 
	 * @param  string $file
	 * @return bool
	 */
	public static function verifyFileSize($file) 
	{
		$maxFileSize = 8 * 1024 * 1024;
		return file_exists($file) && filesize($file) < $maxFileSize;
	}

	/**
	 * check if social setting is enabled
	 * 
	 * @param  object  $settings
	 * @return bool
	 */
	public static function isSocialEnabled($settings) 
	{
		$enabled = false;
		foreach (HALOUtilHelper::getSettingList($settings, 'social') as $secName => $secValue) {
			if ($settings->getNsValue('social.' . $secName . '.shareEnable.value', 0) && ($shareOptions = $settings->getNsValue('social.' . $secName . '.shareOptions'))) {
				$enable = true;
				return $enable;
			}
		}
		return $enabled;
	}

	/**
	 * function to convert a string to number
	 * 
	 * @param  string  $str
	 * @return bool
	 */
	public static function str2Number($str, $sep = ',', $dec = '.') 
	{
		$phpSep = '';
		$phpDec = '.';
		//replace sep character
		$str = str_replace($sep, $phpSep, $str);
		//replace dec character
		if($dec !== $phpDec){
			$str = str_replace($dec, $phpDec, $str);
		}
		try {
			return (float) $str;
		} catch(\Exception $e) {
			return 0;
		}
	}


	/**
	 * return site description string
	 * 
	 * @return string
	 */
	public static function getSiteDescription() 
	{
		return __halotext('Main page og description');
	}

	/**
	 * find an item in collection, return the first found item
	 * 
	 * @param  array $collection
	 * @param  string $value
	 * @param  string $key
	 * @return bool
	 */
	public static function findInCollection($collection, $value, $key = 'id') 
	{
		foreach ($collection as $index => $item) {
			if (isset($item->$key) && $item->$key == $value) {
				return $item;
			}
		}
		return null;
	}

	/**
	 * return best display view for a photo
	 * 
	 * @param  array $postData
	 * @param  object $target
	 * @return mixed
	 */
	public static function getSharePhotoViewOptions($photo) {
		$viewOptions = array();
		$photoView = '';
		$photoWidth = '';
		$photoHeight = '';
		$photoUrl = '';
		if($photo) {
			if($photo->getWidth() > HALO_PHOTO_THUMB_SIZE * 6 && $photo->getHeight() > HALO_PHOTO_THUMB_SIZE * 3){
				$photoView = 'img-full';
				$photoWidth = HALO_PHOTO_THUMB_SIZE * 12;
				$photoHeight = HALO_PHOTO_THUMB_SIZE * 6;
			} else {
				$photoWdith = 'center';
				$photoHeight = HALO_PHOTO_THUMB_SIZE * 4;
			}
			$photoUrl = $photo->getResizePhotoURL($photoWidth,$photoHeight);
		}
		return array('view' => $photoView, 'width' => $photoWidth, 'height' => $photoHeight, 'url' => $photoUrl);
	}

	
	/**
	 * parse share link function
	 * 
	 * @param  array $postData
	 * @param  object $target
	 * @return mixed
	 */
	public static function parseShareLink($postData, &$target) 
	{
		//reset share link params
		$target->clearParams('urlpreview', '');
		$target->clearParams('imagepreview_id', '');
		if (isset($postData['nopreview']) && $postData['nopreview']) {
			return;
		}
		//auto detect url content
		if (!isset($postData['urlpreview']) && isset($target->message)) {
			$urls = self::getUrls($target->message);
			//only fetch the first url
			if (!empty($urls)) {
				$url = array_shift($urls);
                $url = HALOUtilHelper::parseNCheckUrl($url);
				$info = HALOBrowseHelper::fetchUrl($url);
				if ($info) {
					$infoData = array();
					$infoData['url'] = $info->url;
					$infoData['title'] = $info->title;
					$infoData['description'] = $info->description;
					if (is_array($info->photos) && count($info->photos) > 0) {
						$photo = array_shift($info->photos);
						$photoViewOptions = HALOUtilHelper::getSharePhotoViewOptions($photo);
						$infoData['image_url'] = $photoViewOptions['url'];
						$infoData['image_view'] = $photoViewOptions['view'];
						$infoData['image_id'] = $photo->id;
					}
					$postData['urlpreview'] = $infoData;
				}
			}
		}
		if (isset($postData['urlpreview']) && isset($postData['urlpreview']['url'])) {
			$urlData = $postData['urlpreview'];
			$data = array();
			$data['url'] = $urlData['url'];
			$data['title'] = isset($urlData['title']) ? $urlData['title'] : '';
			$data['title'] = HALOOutputHelper::ellipsis($data['title'], 100);
			$data['description'] = isset($urlData['description']) ? $urlData['description'] : '';
			$data['description'] = HALOOutputHelper::ellipsis($data['description'], 400);
			$data['image_url'] = isset($urlData['image_url']) ? $urlData['image_url'] : '';
			$data['image_id'] = isset($urlData['image_id']) ? $urlData['image_id'] : '';
			$data['image_view'] = isset($urlData['image_view']) ? $urlData['image_view'] : '';
			$target->setParams('urlpreview', $data);
			//for params relationship loading
			if (isset($urlData['image_id'])) {
				$target->setParams('imagepreview_id', $urlData['image_id']);
			}
		}
	}

	/**
	 * function to get a list of url from a string
	 * 
	 * @param  string $text
	 * @return array
	 */
	public static function getUrls($text) 
	{
		$pattern = "/(((ftp|http|https):\/\/)|(www.))(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/";
		if (preg_match_all($pattern, $text, $matches)) {
			return $matches[0];
		};
		return array();
	}

	/**
	 * function to force pre-catch a url
	 * 
	 * @param  string $url url to pre-catch
	 * @return boolean true/false
	 */
	public static function preCatchFBShare($url) {
		if(function_exists('curl_init') && HALOConfig::get('social.facebook.shareEnable',0)){
			try {
				$app_id = HALOConfig::get('social.facebook.oauthClientId','');
				$app_secret = HALOConfig::get('social.facebook.oauthClientSecret','');
				if(!$app_id || !$app_secret) return ;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/?id='. urlencode($url)) . '&scrape=true';
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array(
								'access_token' => $app_id . '|' . $app_secret
							));
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$r = curl_exec($ch);
				return json_decode($r);
			} catch (\Exception $e) {
			
			}
		} 
		return null;
	}	
	/**
	 * return all style class options
	 * 
	 * @return array
	 */
	public static function getStyleOptions() 
	{
		return array(array('title' => 'Primary', 'value' => 'primary', 'data' => array('content' => "<span class='label label-primary'>Primary</span>"))
			, array('title' => 'Success', 'value' => 'success', 'data' => array('content' => "<span class='label label-success'>Success</span>"))
			, array('title' => 'Info', 'value' => 'info', 'data' => array('content' => "<span class='label label-info'>Info</span>"))
			, array('title' => 'Warning', 'value' => 'warning', 'data' => array('content' => "<span class='label label-warning'>Warning</span>"))
			, array('title' => 'Danger', 'value' => 'danger', 'data' => array('content' => "<span class='label label-danger'>Danger</span>")),
		);
	}


	/**
	 * return array sort of a collection
	 * @param  object $collection 
	 * @param  array $arrayIndex 
	 * @return object
	 */
	public static function sortCollectionByArray($collection, $arrayIndex) 
	{
		$items = array();
		foreach ($arrayIndex as $key => $index) {
			$item = $collection->find($index, null);
			if (!is_null($item)) {
				$items[] = $item;
			}
		}
		return new Illuminate\Database\Eloquent\Collection($items);
	}
	
	
	/*
		function to check for duplicated row by using input condition and insert a new one if not exists. 
		Return true on inserting and false if duplicated
	*/
	public static function insertNewIfNotExists($table, array $row, array $condition) {
		//check for existing
		$exists = DB::table($table)->where(function($query) use($row, $condition){
			foreach($condition as $cond) {
				if(isset($row[$cond])){
					$query->where($cond, $row[$cond]);
				}
			}
		})->count();
		if(!$exists) {
			DB::table($table)->insert(array($row));
			return true;
		} else {
			return false;
		}
	}
	
	/*
		function to return timezone array
	*/
	public static function getTimeZoneArray() {
		return array (
		'(GMT-11:00) Midway Island' => 'Pacific/Midway',
		'(GMT-11:00) Samoa' => 'Pacific/Samoa',
		'(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
		'(GMT-09:00) Alaska' => 'US/Alaska',
		'(GMT-08:00) Pacific Time (US &amp; Canada)' => 'America/Los_Angeles',
		'(GMT-08:00) Tijuana' => 'America/Tijuana',
		'(GMT-07:00) Arizona' => 'US/Arizona',
		'(GMT-07:00) Chihuahua' => 'America/Chihuahua',
		'(GMT-07:00) La Paz' => 'America/Chihuahua',
		'(GMT-07:00) Mazatlan' => 'America/Mazatlan',
		'(GMT-07:00) Mountain Time (US &amp; Canada)' => 'US/Mountain',
		'(GMT-06:00) Central America' => 'America/Managua',
		'(GMT-06:00) Central Time (US &amp; Canada)' => 'US/Central',
		'(GMT-06:00) Guadalajara' => 'America/Mexico_City',
		'(GMT-06:00) Mexico City' => 'America/Mexico_City',
		'(GMT-06:00) Monterrey' => 'America/Monterrey',
		'(GMT-06:00) Saskatchewan' => 'Canada/Saskatchewan',
		'(GMT-05:00) Bogota' => 'America/Bogota',
		'(GMT-05:00) Eastern Time (US &amp; Canada)' => 'US/Eastern',
		'(GMT-05:00) Indiana (East)' => 'US/East-Indiana',
		'(GMT-05:00) Lima' => 'America/Lima',
		'(GMT-05:00) Quito' => 'America/Bogota',
		'(GMT-04:00) Atlantic Time (Canada)' => 'Canada/Atlantic',
		'(GMT-04:30) Caracas' => 'America/Caracas',
		'(GMT-04:00) La Paz' => 'America/La_Paz',
		'(GMT-04:00) Santiago' => 'America/Santiago',
		'(GMT-03:30) Newfoundland' => 'Canada/Newfoundland',
		'(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
		'(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
		'(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
		'(GMT-03:00) Greenland' => 'America/Godthab',
		'(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
		'(GMT-01:00) Azores' => 'Atlantic/Azores',
		'(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
		'(GMT+00:00) Casablanca' => 'Africa/Casablanca',
		'(GMT+00:00) Edinburgh' => 'Europe/London',
		'(GMT+00:00) Greenwich Mean Time : Dublin' => 'Etc/Greenwich',
		'(GMT+00:00) Lisbon' => 'Europe/Lisbon',
		'(GMT+00:00) London' => 'Europe/London',
		'(GMT+00:00) Monrovia' => 'Africa/Monrovia',
		'(GMT+00:00) UTC' => 'UTC',
		'(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
		'(GMT+01:00) Belgrade' => 'Europe/Belgrade',
		'(GMT+01:00) Berlin' => 'Europe/Berlin',
		'(GMT+01:00) Bern' => 'Europe/Berlin',
		'(GMT+01:00) Bratislava' => 'Europe/Bratislava',
		'(GMT+01:00) Brussels' => 'Europe/Brussels',
		'(GMT+01:00) Budapest' => 'Europe/Budapest',
		'(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
		'(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
		'(GMT+01:00) Madrid' => 'Europe/Madrid',
		'(GMT+01:00) Paris' => 'Europe/Paris',
		'(GMT+01:00) Prague' => 'Europe/Prague',
		'(GMT+01:00) Rome' => 'Europe/Rome',
		'(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
		'(GMT+01:00) Skopje' => 'Europe/Skopje',
		'(GMT+01:00) Stockholm' => 'Europe/Stockholm',
		'(GMT+01:00) Vienna' => 'Europe/Vienna',
		'(GMT+01:00) Warsaw' => 'Europe/Warsaw',
		'(GMT+01:00) West Central Africa' => 'Africa/Lagos',
		'(GMT+01:00) Zagreb' => 'Europe/Zagreb',
		'(GMT+02:00) Athens' => 'Europe/Athens',
		'(GMT+02:00) Bucharest' => 'Europe/Bucharest',
		'(GMT+02:00) Cairo' => 'Africa/Cairo',
		'(GMT+02:00) Harare' => 'Africa/Harare',
		'(GMT+02:00) Helsinki' => 'Europe/Helsinki',
		'(GMT+02:00) Istanbul' => 'Europe/Istanbul',
		'(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
		'(GMT+02:00) Kyiv' => 'Europe/Helsinki',
		'(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
		'(GMT+02:00) Riga' => 'Europe/Riga',
		'(GMT+02:00) Sofia' => 'Europe/Sofia',
		'(GMT+02:00) Tallinn' => 'Europe/Tallinn',
		'(GMT+02:00) Vilnius' => 'Europe/Vilnius',
		'(GMT+03:00) Baghdad' => 'Asia/Baghdad',
		'(GMT+03:00) Kuwait' => 'Asia/Kuwait',
		'(GMT+03:00) Minsk' => 'Europe/Minsk',
		'(GMT+03:00) Nairobi' => 'Africa/Nairobi',
		'(GMT+03:00) Riyadh' => 'Asia/Riyadh',
		'(GMT+03:00) Volgograd' => 'Europe/Volgograd',
		'(GMT+03:30) Tehran' => 'Asia/Tehran',
		'(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
		'(GMT+04:00) Baku' => 'Asia/Baku',
		'(GMT+04:00) Moscow' => 'Europe/Moscow',
		'(GMT+04:00) Muscat' => 'Asia/Muscat',
		'(GMT+04:00) St. Petersburg' => 'Europe/Moscow',
		'(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
		'(GMT+04:00) Yerevan' => 'Asia/Yerevan',
		'(GMT+04:30) Kabul' => 'Asia/Kabul',
		'(GMT+05:00) Islamabad' => 'Asia/Karachi',
		'(GMT+05:00) Karachi' => 'Asia/Karachi',
		'(GMT+05:00) Tashkent' => 'Asia/Tashkent',
		'(GMT+05:30) Chennai' => 'Asia/Calcutta',
		'(GMT+05:30) Kolkata' => 'Asia/Kolkata',
		'(GMT+05:30) Mumbai' => 'Asia/Calcutta',
		'(GMT+05:30) New Delhi' => 'Asia/Calcutta',
		'(GMT+05:30) Sri Jayawardenepura' => 'Asia/Calcutta',
		'(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
		'(GMT+06:00) Almaty' => 'Asia/Almaty',
		'(GMT+06:00) Astana' => 'Asia/Dhaka',
		'(GMT+06:00) Dhaka' => 'Asia/Dhaka',
		'(GMT+06:00) Ekaterinburg' => 'Asia/Yekaterinburg',
		'(GMT+06:30) Rangoon' => 'Asia/Rangoon',
		'(GMT+07:00) Bangkok' => 'Asia/Bangkok',
		'(GMT+07:00) Hanoi' => 'Asia/Bangkok',
		'(GMT+07:00) Jakarta' => 'Asia/Jakarta',
		'(GMT+07:00) Novosibirsk' => 'Asia/Novosibirsk',
		'(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
		'(GMT+08:00) Chongqing' => 'Asia/Chongqing',
		'(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
		'(GMT+08:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
		'(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
		'(GMT+08:00) Perth' => 'Australia/Perth',
		'(GMT+08:00) Singapore' => 'Asia/Singapore',
		'(GMT+08:00) Taipei' => 'Asia/Taipei',
		'(GMT+08:00) Ulaan Bataar' => 'Asia/Ulan_Bator',
		'(GMT+08:00) Urumqi' => 'Asia/Urumqi',
		'(GMT+09:00) Irkutsk' => 'Asia/Irkutsk',
		'(GMT+09:00) Osaka' => 'Asia/Tokyo',
		'(GMT+09:00) Sapporo' => 'Asia/Tokyo',
		'(GMT+09:00) Seoul' => 'Asia/Seoul',
		'(GMT+09:00) Tokyo' => 'Asia/Tokyo',
		'(GMT+09:30) Adelaide' => 'Australia/Adelaide',
		'(GMT+09:30) Darwin' => 'Australia/Darwin',
		'(GMT+10:00) Brisbane' => 'Australia/Brisbane',
		'(GMT+10:00) Canberra' => 'Australia/Canberra',
		'(GMT+10:00) Guam' => 'Pacific/Guam',
		'(GMT+10:00) Hobart' => 'Australia/Hobart',
		'(GMT+10:00) Melbourne' => 'Australia/Melbourne',
		'(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
		'(GMT+10:00) Sydney' => 'Australia/Sydney',
		'(GMT+10:00) Yakutsk' => 'Asia/Yakutsk',
		'(GMT+11:00) Vladivostok' => 'Asia/Vladivostok',
		'(GMT+12:00) Auckland' => 'Pacific/Auckland',
		'(GMT+12:00) Fiji' => 'Pacific/Fiji',
		'(GMT+12:00) International Date Line West' => 'Pacific/Kwajalein',
		'(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
		'(GMT+12:00) Magadan' => 'Asia/Magadan',
		'(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
		'(GMT+12:00) New Caledonia' => 'Asia/Magadan',
		'(GMT+12:00) Solomon Is.' => 'Asia/Magadan',
		'(GMT+12:00) Wellington' => 'Pacific/Auckland',
		'(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
		);	
	}
	/*
		function to return timezone select option list
	*/
	public static function getTimeZoneOptions() {
		$timezoneArr = self::getTimeZoneArray();
		$options = array();
		foreach($timezoneArr as $key => $timezone) {
			$options[] = HALOObject::getInstance(array('title' => $key, 'value' => $timezone));
		}
        return $options;
		
	}
	/*
		function to return timezone array
	*/
	public static function getRecurringArray() {
		return array (
		__halotext('No') => '0',
		__halotext('Daily') => '1',
		__halotext('Weekly') => '7',
		__halotext('Monthly') => '30',
		);	
	}
	/*
		function to return timezone select option list
	*/
	public static function getRecurringOptions() {
		$timezoneArr = self::getRecurringArray();
		$options = array();
		foreach($timezoneArr as $key => $timezone) {
			$options[] = HALOObject::getInstance(array('title' => $key, 'value' => $timezone));
		}
        return $options;
		
	}
	
	/*
		check if time1 between time2 and time3 in daily basic
	*/
	public static function betweenDaily(Carbon $time1, Carbon $time2, Carbon $time3) {
		if( $time2->hour <= $time1->hour && $time3->hour >= $time1->hour
			&& $time2->minute <= $time1->minute && $time3->minute >= $time1->minute) {
			return true;
		}
		return false;
	}
	
	/*
		check if time1 between time2 and time3 in weekly basic
	*/
	public static function betweenWeekly(Carbon $time1, Carbon $time2, Carbon $time3) {
		if( $time2->dayOfWeek <= $time1->dayOfWeek && $time3->dayOfWeek >= $time1->dayOfWeek 
			&& self::betweenDaily($time1, $time2, $time3)) {
			return true;
		}
		return false;
	}
	
	/*
		check if time1 between time2 and time3 in monthly basic
	*/
	public static function betweenMonthly(Carbon $time1, Carbon $time2, Carbon $time3) {
		if( $time2->day <= $time1->day && $time3->day >= $time1->day 
			&& self::betweenDaily($time1, $time2, $time3)) {
			return true;
		}
		return false;
	}
	
	/*
		get the nth incurring of the startTime compare to currTime
	*/
	public static function getNthRecurring(Carbon $currTime, Carbon $startTime, $recurringType) {
		$diff = 0;
		
		if($currTime->lt($startTime)) return 0;
		
		switch($recurringType) {
			case 30:		//monthly
				$diff = $startTime->diffInMonths($currTime);
				$diff = ($diff > 0)? $diff : 0;
				break;
			case 7:		//weekly
				$diff = $startTime->diffInWeeks($currTime);
				$diff = ($diff > 0)? $diff : 0;
				break;
			case 1:		//weekly
				$diff = $startTime->diffInDays($currTime);
				$diff = ($diff > 0)? $diff : 0;
				break;
		}
		//adjustment 
		$adjTime = self::getNextRecurring($startTime, $diff, $recurringType);
		if($adjTime->lt($currTime)) {
			$diff++;
		}
		return $diff;
	}
	
	/*
		get the next incurring Carbon object of the startTime compare to currTime	
	*/
	public static function getNextRecurring(Carbon $startTime, $diff, $recurringType) {
		if(!$diff) return $startTime;
		
		$next = $startTime;
		
		switch($recurringType) {
			case 30:		//monthly
				$next = $startTime->copy()->addMonths($diff);
				break;
			case 7:		//weekly
				$next = $startTime->copy()->addWeeks($diff);
				break;
			case 1:		//weekly
				$next = $startTime->copy()->addDays($diff);
				break;
		}
		return $next;
	}
	
	/*
		format a given date by using date format string, shorten the output if Today, Tomorrow, Yesterday
	*/
	public static function formatDate(Carbon $date, $dateFormat) {
		if($date->isToday()) {
			return __halotext('Today');
		} else if($date->isYesterday()) {
			return __halotext('Yesterday');
		} else if($date->isTomorrow()) {
			return __halotext('Tomorrow');
		} else {
			return $date->formatLocalized($dateFormat);
		}
	}
	
    /**
     * get list of city/distrct having posts
     * 
     * @param  HALOParams $params 
     * @param  string   $uiType 
     * @return array           
     */
    public static function getCityDistrictList()
    {
		//get all avalable district
        $options = array();
		//Cache::forget('city_district_by_post_listing');
		$options = Cache::remember('city_district_by_post_listing', 60, function(){
			$rows = DB::table('halo_posts')->select(DB::raw('count(*) as post_count, 
						' . DB::getTablePrefix() . 'halo_locations.district_name as name, 
						' . DB::getTablePrefix() . 'halo_locations.city_name as city, 
						' . DB::getTablePrefix() . 'halo_locations.district_name as value'))
					->leftJoin('halo_locations', 'halo_posts.location_id', '=', 'halo_locations.id')
					->groupBy('halo_locations.city_name','halo_locations.district_name')
					->whereNotNull('halo_posts.location_id')
					->whereNotNull('halo_locations.district_name')
					->whereNotNull('halo_locations.city_name')
					->orderBy('post_count','desc')
					->get();
			$options = array();
			foreach($rows as $row){
				$key = $row->city;
				if(!isset($options[$row->city])){
					$city = new stdClass();
					$city->name = $row->city;
					$city->value = $key;
					$city->post_count = 0;
					$city->children = array();
					$options[$key] = $city;
				} else {
					$child = new stdClass();
					$child->name = $row->name;
					$child->value = $row->value;
					$child->post_count = $row->post_count;
				}
				$child = new stdClass();
				$child->name = $row->name;
				$child->value = $row->value;
				$options[$key]->children[] = $child;
				$options[$key]->post_count += $row->post_count;
			}
			//sort options arra by post_count
			usort($options, function($a, $b){
				if($a->post_count == $b->post_count) {
					return 0;
				}
				return ($a->post_count > $b->post_count) ? -1 : 1;
			});
			return $options;
		});
		
		return $options;

	}

	/*
		return basic slick options (without autoplay)
	*/
	public static function getSlickOptions(array $options = array()) {
		$slickOptions = array('slidesToShow' => 3,'slidesToScroll' => 2, 
							'autoplay' => true, 'autoplaySpeed' => 2000,
							'responsive' => array(array(
													'breakpoint'=> 1024,
													'settings'=> array(
														'slidesToShow'=> 6, 'slidesToScroll'=> 5,	'infinite'=> true,
													)
												),
												array(
													'breakpoint'=> 600,
													'settings'=> array(
														'slidesToShow'=> 4, 'slidesToScroll'=> 3,	'infinite'=> true,
													)
												),
												array(
													'breakpoint'=> 480,
													'settings'=> array(
														'slidesToShow'=> 2, 'slidesToScroll'=> 1,	'infinite'=> true,
													)
												)
							)
						);
		
		return array_merge($slickOptions, $options);
	}
	
	/*
		get external content by using curl
	*/
	public static function getImageContent($url) {
		$data = null;
		try {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$data = curl_exec($curl);
			curl_close($curl);	
		} catch (\Exception $e) {
			return null;
		}
		return $data;
	}
	
	/*
		return version info message
	*/
	public static function getVersionInfoMessage() {
		$license = self::___getPackageType();
		return sprintf('You are using %s. Please upgrade to get access to more features', $license);
	}
	
	/*
		
	*/
	public static function getVersionToolbar() {
		return HALOToolbar::addToolbar(HALOUtilHelper::getVersionInfoMessage(),'halo-btn-warning','http://halo.social/pricing','','bolt','_blank');
	}

	/*
		return package type. Please do not change/format
	*/
	public static function ___getPackageType() {
		$___haloPkgType = HALO_PLUGIN_PRODUCT_TYPE;
		return $___haloPkgType;
	}
	
	/*
		return package type. Please do not change/format
	*/
	public static function ___getFeatures() {
		static $___haloFeatures = null;
		if(is_null($___haloFeatures)) {
			$___haloFeaturesValue = '___000001000___';
			$features = array('profile', 'message', 'friend', 'follow', 'push', 'filter', 'plugin', 'label', 'acl');
			$___haloFeatures = array_flip($features);
			foreach($features as $index => $feature) {
				$val = intval(substr($___haloFeaturesValue, $index + 3, 1));
				$___haloFeatures[$feature] = $val;
			}
		}
		return $___haloFeatures;
	}
	
	public static function ___getProductName() {
		return EDD_HALOSOCIAL_ITEM_NAME;
	}
	
	public static function ___getStoreUrl() {
		return EDD_HALOSOCIAL_STORE_URL;
	}

    public static function parseNCheckUrl($s) {
        $regex = '/(((ftp|http|https):\/\/)|(www.))(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/';
        if (preg_match($regex, $s)) {
            if (preg_match('/^(www)\.{1}/', $s)) {
                return 'http://' . $s;
                // return preg_replace('/^(www)\.{1}/', 'http://', $s);
            }
            return $s;
        }
        return '';
    }
}
