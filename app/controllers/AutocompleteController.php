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

class AutocompleteController extends BaseController
{

    /**
     * Inject the models.
     * 
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * process ajax search user
     *
     * @return JSON
     */

	public function ajaxSearchUsers()
	{
		
		//get the term string
		$term = Input::get('term');
		$filters = Input::get('filters');
		$param = Input::get('param');

		if ($param == 'all') {
			HALOConfig::set('user.suggestFriendsOnly', 0);	//temporary set the configure param for this request only
		}

		$result = HALOUserModel::getSearch($term, $filters);
		echo json_encode($result);
		exit;
				
	}
	
   /**
     * process ajax search location
     *
     * @return JSON
     */

	public function ajaxSearchLocations()
	{
		
		//get the term string
		$term = Input::get('term');
		$filters = Input::get('filters');
		
		$result = HALOLocationModel::getSearch($term, $filters);
		echo json_encode($result);
		exit;
				
	}
	
    /**
     * process ajax search hash tag
     *
     * @return JSON
     */

	public function ajaxSearchTags()
	{
		
		//get the term string
		$term = Input::get('term');
		$result = HALOHashTagModel::getSearch($term);
		echo json_encode($result);
		exit;
				
	}

/**
 * save tag to a target
 * 
 * @return JSON
 */
	public function ajaxPushTags()
	{
		//get the term string
		$tagNames = Input::get('tags');
		$targetId = Input::get('targetid');
		$context = Input::get('context');
		$target = HALOModel::getCachedModel($context, $targetId);
		if ($target) {
			if (method_exists($target, 'hashtags')) {
				//check permission
				if (!method_exists($target, 'canTag') || !$target->canTag()) {
					return;
				}
				$hashTags = $target->hashtags();
				$tagNameList = explode(',', $tagNames);
				$tags = array();
				$tagIds = array();
				
				//limit number of hashtag per target
				if (!empty($tagNameList) && count($tagNameList) > 20) {
					$tagNameList = array_splice($tagNameList, 0, 20);
				}
				
				foreach ($tagNameList as $tagName) {
					//sanitize $tagName
					if (strpos($tagName, '#') === 0) {
						$tagName = substr($tagName, 1);
					}
					//(#\p{L}+)/u
					//$valid = (strlen($tagName) > 0) && preg_match('/^[\w][\w ]*$/',$tagName,$matches);
					$valid = (strlen($tagName) > 0) && preg_match('/^[\p{L}0-9_][\p{L}0-9_&\+\- ]*$/u', $tagName, $matches);
					if ($valid) {
						$tag = HALOHashTagModel::firstOrCreate(array('name' => mb_strtolower($tagName, 'UTF-8')));
						$tags[] = $tag;
						$tagIds[] = $tag->id;
					}
				}
				//store tags to database
				$target->hashTags()->sync($tagIds);
				$target->clearCache();
			}
		}
		
	}
	
	
}
