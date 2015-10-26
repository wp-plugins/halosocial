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

class HALOLikeAPI 
{

	/**
	 * add the current login user to the like list
	 * @param  object $target
	 * @return mixed
	 */
	public static function like($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return;
		}

		//trigger before event
		if (Event::fire('like.onBeforeLike', array($target), true) === false) {
			//error occur, return
			return false;
		}

		if (!method_exists($target, 'likes')) {
			return;
		}
		//target must be likeable
		$like = $target->likes->first();

		if (!$like) {
			//this is the first like for this target
			$like = new HALOLikeModel();
		}

		$likeStr = trim($like->like);
		$likeArr = empty($likeStr) ? array() : explode(',', $likeStr);
		if (!in_array($user->user_id, $likeArr)) {
			$likeArr[] = $user->user_id;
			//update like list
			$like->like = implode(',', $likeArr);
		}
		//remove from dislike list if set
		$dislikeStr = trim($like->dislike);
		$dislikeArr = empty($dislikeStr) ? array() : explode(',', $dislikeStr);
		if (($index = array_search($user->user_id, $dislikeArr)) !== false) {
			unset($dislikeArr[$index]);
			//update like list
			$like->dislike = implode(',', $dislikeArr);
		}
		$target->likes()->save($like);

		//trigger event
		Event::fire('like.onAfterLike', array($target, $like));
	}

	/**
	 * remove the current login user from the like list
	 * 
	 * @param  object $target
	 * @return mixed
	 */
	public static function unlike($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return;
		}

		if (!method_exists($target, 'likes')) {
			return;
		}
		//target must be likeable

		$like = $target->likes->first();

		if (!$like) {
			return;
		}

		$likeArr = explode(',', trim($like->like));
		if (($index = array_search($user->user_id, $likeArr)) !== false) {
			unset($likeArr[$index]);
			//update like list
			$like->like = implode(',', $likeArr);
		}

		$target->likes()->save($like);
		//trigger event
		Event::fire('like.onAfterUnLike', array($target, $like));
	}

	/**
	 * return html for the like action on a specific target model
	 * 
	 * @param  object  $target
	 * @param  bool $numberOnly
	 * @return HALOUIBuilder
	 */
	public static function getLikeHtml($target, $numberOnly = false) 
	{
		$my = HALOUserModel::getUser();

		$numberOnly = (int) $numberOnly;

		$context = lcfirst($target->getContext());
		$zone = 'like.' . $context . '.' . $target->id;
		$like = $target->likes->first();
		$likeList = array();
		if ($like) {
			$likeList = $like->getLikeList();
		}

		if (self::isLike($target)) {
			$builder = HALOUIBuilder::getInstance('', 'like', array('title' => 'unlike', 'likeList' => $likeList, 'onClick' => "halo.like.unlike('" . $context . "','" . $target->id . "','" . $numberOnly . "')",
				'zone' => $zone,
				'liked' => true,
				'numberOnly' => $numberOnly,
				'like' => 'like',
				'icon' => 'thumbs-up'));

		} else {
			$builder = HALOUIBuilder::getInstance('', 'like', array('title' => 'like', 'likeList' => $likeList, 'onClick' => "halo.like.like('" . $context . "','" . $target->id . "','" . $numberOnly . "')",
				'zone' => $zone,
				'liked' => false,
				'numberOnly' => $numberOnly,
				'like' => 'like',
				'icon' => 'thumbs-o-up'));
		}
		return $builder->fetch();
	}

	/**
	 * check whether if current login user like this target model or not
	 * 
	 * @param  object  $target
	 * @return bool
	 */
	public static function isLike($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return false;
		}

		if (count($target->likes) == 0) {
			return false;
		}

		$like = $target->likes->first();
		$likeArr = explode(',', $like->like);
		return in_array($user->user_id, $likeArr);

	}
	/**
	 * add the current login user to the dislike list
	 * 
	 * @param  object $target
	 * @return mixed
	 */
	public static function dislike($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return;
		}

		//trigger before event
		if (Event::fire('like.onBeforeDisLike', array($target), true) === false) {
			//error occur, return
			return false;
		}

		if (!method_exists($target, 'likes')) {
			return;
		}
		//target must be likeable
		$like = $target->likes->first();

		if (!$like) {
			//this is the first like for this target
			$like = new HALOLikeModel();
		}

		$dislikeStr = trim($like->dislike);
		$dislikeArr = empty($dislikeStr) ? array() : explode(',', $dislikeStr);
		if (!in_array($user->user_id, $dislikeArr)) {
			$dislikeArr[] = $user->user_id;
			//update like list
			$like->dislike = implode(',', $dislikeArr);
		}
		//remove from like list if set
		$likeStr = trim($like->like);
		$likeArr = empty($likeStr) ? array() : explode(',', $likeStr);
		if (($index = array_search($user->user_id, $likeArr)) !== false) {
			unset($likeArr[$index]);
			//update like list
			$like->like = implode(',', $likeArr);
		}

		$target->likes()->save($like);

		//trigger event
		Event::fire('like.onAfterDisLike', array($target, $like));
	}

	/**
	 * remove the current login user from the dislike list
	 * 
	 * @param  object $target
	 * @return mixed
	 */
	public static function undislike($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return;
		}

		if (!method_exists($target, 'likes')) {
			return;
		}
		//target must be likeable

		$like = $target->likes->first();

		if (!$like) {
			return;
		}

		$dislikeArr = explode(',', trim($like->dislike));
		if (($index = array_search($user->user_id, $dislikeArr)) !== false) {
			unset($dislikeArr[$index]);
			//update dislike list
			$like->dislike = implode(',', $dislikeArr);
		}

		$target->likes()->save($like);
		//trigger event
		Event::fire('like.onAfterUnDisLike', array($target, $like));

	}

	/**
	 * return html for the dislike action on a specific target model
	 * 
	 * @param  object  $target 
	 * @param  bool $numberOnly
	 * @return HALOUIBuilder
	 */
	public static function getDislikeHtml($target, $numberOnly = false) 
	{
		$my = HALOUserModel::getUser();

		$numberOnly = (int) $numberOnly;

		$context = lcfirst($target->getContext());
		$zone = 'dislike.' . $context . '.' . $target->id;
		$like = $target->likes->first();
		$likeList = array();
		if ($like) {
			$likeList = $like->getDisLikeList();
		}
		if (self::isDislike($target)) {
			$builder = HALOUIBuilder::getInstance('', 'like', array('title' => 'unDislike', 'likeList' => $likeList, 'onClick' => "halo.like.undislike('" . $context . "','" . $target->id . "','" . $numberOnly . "')",
				'zone' => $zone,
				'liked' => true,
				'numberOnly' => $numberOnly,
				'like' => 'dislike',
				'icon' => 'thumbs-down'));
		} else {
			$builder = HALOUIBuilder::getInstance('', 'like', array('title' => 'dislike', 'likeList' => $likeList, 'onClick' => "halo.like.dislike('" . $context . "','" . $target->id . "','" . $numberOnly . "')",
				'zone' => $zone,
				'liked' => false,
				'numberOnly' => $numberOnly,
				'like' => 'dislike',
				'icon' => 'thumbs-o-down'));

		}
		return $builder->fetch();
	}

	/**
	 * check whether if current login user like this target model or not
	 * 
	 * @param  object $target
	 * @return bool
	 */
	public static function isDislike($target) 
	{
		$user = HALOUserModel::getUser();
		if (!$user) {
			return false;
		}

		if (count($target->likes) == 0) {
			return false;
		}

		$like = $target->likes->first();
		$dislikeArr = explode(',', $like->dislike);
		return in_array($user->user_id, $dislikeArr);

	}

	/**
	 * return html for the dislike action on a specific target model
	 * 
	 * @param  object $target
	 * @param  bool $numberOnly
	 * @return string
	 */
	public static function getLikeDislikeHtml($target, $numberOnly = false) 
	{
		$html = array();
		//check if user can do like/dislike action
		$user = HALOUserModel::getUser();
		//if(!$user) return '';
		if (HALOConfig::get('global.enableLike')) {
			$html['like'] = self::getLikeHtml($target, $numberOnly);
		}
		if (HALOConfig::get('global.enableDisLike')) {
			$html['dislike'] = self::getDisLikeHtml($target, $numberOnly);
		}
		return implode(' ', $html);
	}

}
