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

class HALOCacheModel extends HALOModel
{
  protected $table = 'halo_cache_tree';

	protected $hidden = array('created_at', 'updated_at');
	protected $fillable = array('key','type');

	private $validator = null;
	
	private $_params = null;
	
	static $stack = array();
	
	static $trees = array();
	
	public function HALOCacheModel($key = '', $type = '')
	{
		$this->key = $key;
		$this->type = $type;
	}
	
	public static function addChild(HALOCacheNode &$parent,HALOCacheNode $child){
		$parent->children[] = $child;
		return $child;
	}
	
	public static function getCacheNode($key,$type){
		$node = new HALOCacheNode();
		$node->key = $key;
		$node->type = $type;
		return $node;
	}
	/*
		generate cache key from input params
	*/
	public static function getCacheKeyEx($key,$uiType){
		$cacheKey = $key . '.' . $uiType ;
		return $cacheKey;
	}
	/*
		start a caching node
	*/
	public static function beginCacheUI($key,$uiType){
		$cache = self::getCacheNode($key,$uiType);
		
		$parent = array_pop(self::$stack);
		if($parent){
			self::addChild($parent,$cache);
			//push back the parent to stack
			array_push(self::$stack,$parent);
		}
		array_push(self::$stack,$cache);
	}
	
	/*
		end a caching node and store cache tree structure to database if needed
	*/
	public static function endCacheUI($cacheKey=''){
		$cache = array_pop(self::$stack);
		if(empty(self::$stack) && $cache){
			self::addTree($cache);
		}
	}

	public static function addTree($cache){
		self::$trees[] = $cache;
	}
	
	public static function saveTrees(){
		if(!empty(self::$trees)){
			$insertArr = array();
			$now = Carbon::now();
			$deleteQuery = DB::table('halo_cache_tree')->where('id',0);
			foreach(self::$trees as $tree){
				$row = new HALOCacheModel($tree->key,$tree->type);
				$row->setParams('children',$tree->children);
				//insert array
				$insertArr[] = array('key'=>$row->key,'type'=>$row->type,'created_at'=>$now,'updated_at'=>$now,'params'=>$row->params);
				//delete array
				$deleteQuery->orWhere(function($query) use ($row){
					$query->where('key',$row->key)
								->where('type',$row->type);
				});
			}
			//delete old trees
			$deleteQuery->delete();
			//insert new trees
			DB::table('halo_cache_tree')->insert($insertArr);
			//empty tree list
			self::$trees = array();
		}
	}

	/*
		check if cache mode is enable
	*/
	public static function isEnabled(){
		return true;
	}
}

class HALOCacheNode {
	public $key;
	public $type;
	public $children = array();
}