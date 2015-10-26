<?php
use Illuminate\Database\Eloquent\Relations\Relation;
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
 
abstract class HALOModel extends Eloquent
{

    public $__haloCounters = array();
    public $__haloParamRelation = array();

    protected $toggleable = array();

    protected $sluggable = array(
        'build_from' => null,
        'save_to' => 'slug',
    );

    private $validator = null;

    private $_params = null;

	private static $cacheData = array();

	protected $_shortInfo = null;
	
	private static $cacheDuration = 1200;
    /**
     * Bind Data 
     * 
     * @param  array  $postData 
     * @return HALOModel           
     */
    public function bindData($postData = array())
    {
        $this->fill($postData);

        //extra convert and binding put here
        if (isset($postData['params'])) {
            $this->params = is_string($postData['params']) ? $postData['params'] : json_encode($postData['params']);
        }

        return $this;
    }

    /**
     * Get validate rule
     *  
     * @return array
     */
    public function getValidateRule()
    {
        return array();
    }

    public function getFieldValidateRule($field)
    {
        if (in_array($field, array_keys($this->getValidateRule()))) {
			$rule = $this->getValidateRule();
            return $rule[$field];
        }
        return '';
    }

    /**
     * Return slug string for this model
     * 
     * @return string
     */
    public function getSlug()
    {
        $saveTo = $this->sluggable['save_to'];
        return $this->$saveTo;
    }

    /**
     * Set slug string for this model
     * 
     * @param mixed $slug 
     */
    public function setSlug($slug = null)
    {
        $buildFrom = $this->sluggable['build_from'];
        $saveTo = $this->sluggable['save_to'];
        if (is_null($slug)) {
            if (!empty($buildFrom)) {
                $this->$saveTo = Str::slug($this->$buildFrom);
                return $this->$saveTo;
            } else {
                return null;
            }

        } else {
            $this->$saveTo = Str::slug($slug);
            return $this->$saveTo;
        }
    }

    /**
     * Return slug params
     * 
     * @return string
     */
    public function getSlugParam()
    {
        $slug = $this->getSlug();
        return empty($slug) ? '' : '&slug=' . $slug;
    }

    /**
     * Return slug edit html
     * 
     * @return string
     */
    public function getSlugEdit()
    {
        $slug = $this->getSlug();
        $html = HALOUIBuilder::getInstance('', 'slug_edit', array('target' => $this, 'url' => $this->getUrl()))->fetch();
        $this->setSlug($slug);
        return $html;
    }

    /**
     * Return slug display html
     * 
     * @return string
     */
    public function getSlugDisplay()
    {
        $html = HALOUIBuilder::getInstance('', 'slug_display', array('target' => $this))->fetch();
        return $html;
    }

    /**
     * Override save function
     * 
     * @param  array  $options 
     * @return object        
     */
    public function save(array $options = array())
    {
        //automatically update slug if the slug column is not set
        $saveTo = $this->sluggable['save_to'];
        if ($this->sluggable['build_from'] && (is_null($this->$saveTo) || $this->$saveTo == '')) {
            $this->setSlug();
        }
        return parent::save($options);
    }

    /**
     * Validate input data @Abstract API
     * 
     * @param  aray $data the input data to validate
     * @return Validator    the validator object
     */
    public function validate($data = null)
    {
        //validate with itself data
        if (is_null($data)) {
            $data = $this->toArray();
        }

        $rules = $this->getValidateRule();

        $validator = Validator::make(
            $data,
            $rules
        );
        $this->validator = $validator;
        return $validator;
    }

    /**
     * Get Params
     * 
     * @param  string $key     
     * @param  string $default 
     * @return string          
     */
    public function getParams($key, $default = '')
    {
        if (!isset($this->params)) {
            return $default;
        }
        if (is_null($this->_params)) {
            $this->_params = new HALOParams($this->params);
        }
        return $this->_params->get($key, $default);
    }

    /**
     * Set params
     * 
     * @param string $key   
     * @param string $value 
     * @return HALOModel
     */
    public function setParams($key, $value)
    {

        $json_str = isset($this->params) ? $this->params : '';
        if (is_null($this->_params)) {
            $this->_params = new HALOParams($json_str);
        }
        $this->_params->set($key, $value);
        $this->params = $this->_params->toString();
        return $this;
    }

    /**
     * Clear params
     * 
     * @param  stirng $key 
     * @return HALOModel     
     */
    public function clearParams($key)
    {

        $json_str = isset($this->params) ? $this->params : '';
        if (is_null($this->_params)) {
            $this->_params = new HALOParams($json_str);
        }
        $this->_params->clear($key);
        $this->params = $this->_params->toString();
        return $this;
    }

    /**
     * Get Validator
     * 
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Get Validate Messages
     * 
     * @param  string $validator 
     * @return string
     */
    public function getValidateMessages($validator = null)
    {
        $html = '';
        $validator = is_null($validator) ? $this->getValidator() : $validator;
        $messages = $validator->messages();
        foreach ($messages->all() as $message) {
            $html .= '<dt>' . $message . '</dt>';
        }

        return $html;
    }

    /**
     * Get HALOParams
     * 
     * @return HALOParams
     */
    public function getHALOParams()
    {
        if (is_null($this->_params)) {
            $this->_params = new HALOParams($this->params);
        }
        return $this->_params;
    }

    /**
     * Return context string for this model object
     * 
     * @return string
     */
    public function getContext()
    {
        $className = get_class($this);
        $context = '';
        if (preg_match('/^HALO(\w+)Model$/', $className, $matches)) {
            $context = $matches[1];
        }
        return lcfirst($context);
    }

    /**
     * ROM relationship, HALOFollowerModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany; 
     */
    public function followers()
    {
        return $this->morphMany('HALOFollowerModel', 'followable');
    }

    /**
     * Return assigned followers id for this model
     * 
     * @return string
     */
    public function getFollowers()
    {
        $followerIds = array();
        $followers = empty($this->followers) ? $this->followers()->get() : $this->followers;
        foreach ($followers as $follower) {
            $followerIds[] = $follower->follower_id;
        }
        return $followerIds;
    }

    /**
     * Return zone id for this model object
     * 
     * @param  int $level 
     * @return string      
     */
    public function getZone($level = null)
    {
        $levelZone = '';
        if (!is_null($level)) {
            $level = intval($level);
            for ($i = 1; $i <= $level; $i++) {
                $levelZone = $levelZone . '_l' . $i;
            }
        }
        return $this->getContext() . '.' . $this->id . $levelZone;
    }

    /**
     * Return comment Id for this object
     * 
     * @return string
     */
    public function getCommentId()
    {
        return $this->getContext() . $this->id;
    }

    /**
     * Return display name with link for this model
     * 
     * @param  string $class 
     * @return string 
     */
    public function getDisplayLink($class = '')
    {

        return '';
    }

    /**
     * Return display name with for link this model
     * 
     * @param  string $class 
     * @return string 
     */
    public function getNotifContent($class = '')
    {
        return $this->getDisplayLink($class);
    }

    /**
     * Return display name without link for this model
     * 
     * @return string
     */
    public function getDisplayName()
    {

        return '';
    }

    /**
     * Return brief data attrubutes for this model
     * 
     * @return string
     */
    public function getBriefDataAttribute()
    {
        return ' data-brief-context="' . $this->getContext() . '" data-brief-id="' . $this->id . '" ';
    }

    /**
     * return permission that current user can toggle a field
     * implementation need to override this method, by default, only admin is allowed
     *
     * @param  string $field
     * @return HALOAuth
     */
    public function canToggle($field)
    {
        return HALOAuth::can('backend.view');
    }

	/**
	 * Get view
	 * 
	 * @return Illuminate\Database\Eloquent\Relations\morphMany
	 */
	public function getViewCount($update=false){
		if($update){
			return HALOViewcounterModel::getCounter($this,true);
		} else {
			if($this->views){
				$counter = $this->views->first()?$this->views->first()->counter:0;
				return $counter;
			} else {
				return 0;
			}
		}
		
	}

    /**
     * Views
     *
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function views()
    {
        return $this->morphMany('HALOViewcounterModel', 'viewable');
    }

    /**
     * Return like count of this model
     * 
     * @return HALOLikeAPI
     */
    public function getLikeCount()
    {
        return HALOLikeAPI::getLikeDislikeHtml($this, true);
    }


	/*
		set relation by ref
	*/
	public function setRelationRef($relation,&$value){
		$this->relations[$relation] = &$value;
	}


    /**
     * Return new state of a toggleable field
     * 
     * @param  stirng $field 
     * @return bool 
     */
    public function toggleState($field)
    {
        //toggle is only for int field and in toggleable list
        if (in_array($field, $this->toggleable)) {
            $val = intval($this->getFieldState($field));
            $val++;
            $states = $this->getStates($field);
            $num = count($states);
            if ($num > 0) {
                $val = $val % $num;
            }
            $this->setFieldState($field, $val);
            $this->save();
            return $this->getFieldState($field);
        } else {
            return null;
        }
    }

    /**
     * Return toggleable states a field
     * 
     * @param  string $field 
     * @return array
     */
    public function getStates($field)
    {
        //by default only enable/disable state provided. For additional states, need to override this method
        return array(0 => array('title' => __halotext('Disabled'),
            'icon' => 'times-circle text-danger'),
            1 => array('title' => __halotext('Enabled'),
                'icon' => 'check-circle text-success')

        );
    }

    /**
     * Return current field state value
     * 
     * @param  string $field
     * @return string
     */
    public function getFieldState($field)
    {
        return isset($this->$field) ? $this->$field : '';
    }

    /**
     * Set current field state value
     * 
     * @param string $field 
     * @param string $value 
     * @return string
     */
    public function setFieldState($field, $value)
    {
        $this->$field = $value;
    }

    /**
     * Return table name for this model
     * 
     * @return object
     */
    public function getTable()
    {
        return $this->table;
    }
    /**
     * Return current toggeable state html
     * 
     * @param  string  $field 
     * @return string
     */
    public function getStateHtml($field)
    {
        $states = $this->getStates($field);
        if (isset($states[$this->getFieldState($field)])) {
            $currState = $states[$this->getFieldState($field)];
            $model = $this->getContext();
            $builder = HALOUIBuilder::getInstance('', 'form.toggleState', array('state' => $currState, 'id' => $this->id,
                'zone' => $model . '_' . $field . '_' . $this->id,
                'model' => $model, 'field' => $field));
            return $builder->fetch();
        } else {
            return '';
        }
    }

    /******** Magic function define ***************/

    /**
     * return local cache value
     * 
     * @param  string  $key	key used for caching
     * @param  string  $callback callback if not cached
     * @return mixed
     */
    public function localCache($key, Closure $callback) {
		static $localCache = array();
		if ( isset($localCache[$key])) {
			return $localCache[$key];
		}

		$localCache[$key] = $callback();

		return $localCache[$key];
	}

    /**
     * cache a model
     * 
     * @param  mixed  $model
     * @param  boolean  $force force to update the cache data with $model
     * @return mxied
     */
    public static function setCachedModel($model,$force=false)
	{
		//collection caching
		if(is_a($model,'Illuminate\Database\Eloquent\Collection') || is_array($model)){
			foreach($model as $m){
				HALOModel::setCachedModel($m,$force);
			}
			return true;
		}
		//$model must be subclass of HALOModel
		if(!is_a($model,'HALOModel')) {
			return false;
		}
		$context = $model->getContext();
		if(!isset(HALOModel::$cacheData[$context])){
			HALOModel::$cacheData[$context] = array();
		}
		if($force || !isset(HALOModel::$cacheData[$context][$model->id])){
			HALOModel::$cacheData[$context][$model->id] = $model;
		}
	}
    /**
     * Return model list based on context and context id, cached solution is provided.
     * 
     * @param  string  $context  
     * @param  string  $ids      
     * @param  boolean $cachable 
     * @return array            
     */
    public static function getCachedModel($context, $ids, $cachable = true)
    {
        //the return value is based on input $ids type
        $returnArray = is_array($ids) ? 1 : 0;
        //for user model, call HALOUserModel::init instead
        if (lcfirst($context) == 'user') {
            $models = HALOUserModel::init((array) $ids, $cachable);
            return $returnArray ? $models : (isset($models[$ids]) ? $models[$ids] : null);
        } else {
            if (!isset(HALOModel::$cacheData[$context])) {
                HALOModel::$cacheData[$context] = array();
            }
            //only load models not in cache
            $loadedIds = array_keys(HALOModel::$cacheData[$context]);
            if ($cachable) {
                $loadingIds = array_diff((array) $ids, $loadedIds);
            } else {
                $loadingIds = (array) $ids;
            }
            if (!empty($loadingIds)) {
                $models = array();
                $class = 'HALO' . ucfirst($context) . 'Model';
                if (method_exists($class, 'find')) {
                    $models = call_user_func_array($class . '::find', array($loadingIds));
                }
                //then update to the cache
                foreach ($models as $model) {
                    HALOModel::$cacheData[$context][$model->id] = $model;
                }
            }

            $rtn = array_intersect_key(HALOModel::$cacheData[$context], array_flip((array) $ids));

            //make sure the output array order is as the same as the input, otherwise sorting function will not work
            if ($returnArray) {
                $arr = array();
                foreach ($ids as $id) {
                    if (isset($rtn[$id])) {
                        $arr[$id] = $rtn[$id];
                    }
                }
                return $arr;

            } else {
                return (isset($rtn[$ids]) ? $rtn[$ids] : null);
            }
        }
    }

    /**
     * Check if user can edit this model
     * 
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * Set relation counter attribute of this model
     * 
     * @param mixed $relationName 
     * @param int $counter      
     * @return int
     */
    public function setRelationCounter($relationName, $counter)
    {
        //init if not set
        $this->__haloCounters[$relationName] = $counter;
    }

    /**
     * Get relation counter attribute of this model
     * 
     * @param  mixed  $relationName 
     * @param  int    $default      
     * @return array                
     */
    public function getRelationCounter($relationName, $default = 0)
    {
        //check for lazy load counter
        if (isset($this->__haloCounters[$relationName])) {
            return $this->__haloCounters[$relationName];
        }

        if (HALOModelHelper::relationExists($this, $relationName)) {
            return call_user_func_array(array($this, $relationName), array())->count();
        }
    }

    /**
     * Set param relation model
     * 
     * @param  string  	$relationName 
     * @param  string   $relation     
     */
    public function setParamRelation($relationName, $relation)
    {
        //init if not set
        $this->__haloParamRelation[$relationName] = $relation;
    }

    /**
     * Get param relation model
     * 
     * @param  string $relationName 
     * @param  string $context      
     * @param  string $param        
     * @param  string $default      
     * @return string      
     */
    public function getParamRelation($relationName, $context, $param, $default)
    {
        if (isset($this->__haloParamRelation[$relationName])) {
            //try to reload relation
            $models = new Illuminate\Database\Eloquent\Collection(array($this));
            HALOModelHelper::loadParamRelation($models, array('name' => $relationName, 'context' => $context, 'param' => $param));
        }
        if (isset($this->__haloParamRelation[$relationName])) {
            return $this->__haloParamRelation[$relationName];
        }

        return $default;
    }

    /**
     * Define a param relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasParam($related, $paramKey, $foreignKey = null)
    {
        $foreignKey = $foreignKey ?: 'id';

        $instance = new $related;

        return new HasParam($instance->newQuery(), $this, $paramKey, $foreignKey);
    }

    /**
     * Get Cover Ratio
     * 
     * @return int
     */
    public function getCoverRatio()
    {
        return '8:3';
    }
	
	
	/**
	 * Get a relationship value from a method.
	 *
	 * @param  string  $key
	 * @param  string  $camelKey
	 * @return mixed
	 *
	 * @throws \LogicException
	 */
	protected function getRelationshipFromMethod($key, $camelKey)
	{
		$relations = $this->$camelKey();
		if ( ! $relations instanceof Relation)
		{
			throw new LogicException('Relationship method must return an object of type '
				. 'Illuminate\Database\Eloquent\Relations\Relation');
		}
		$related = $relations->getRelated();
		$relationName = get_class($relations);
		if($related instanceof HALOModel || $related instanceof HALONestedModel) {
			$context = null;
			$cacheId = null;
			switch($relationName) {
				case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
					$context = $related->getContext();
					$cacheId = $this->{$relations->getForeignKey()};
					break;
			}
			//empty cacheId, just return null value
			if(!is_null($context) && empty($cacheId)){
				return $this->relations[$key] = null;
			}
			if(isset(HALOModel::$cacheData[$context][$cacheId])){
				$result = HALOModel::$cacheData[$context][$cacheId];
				return $this->relations[$key] = $result;
			}
			//cache new model
			$this->relations[$key] = $relations->getResults();
			if($context && $cacheId) {
				HALOModel::$cacheData[$context][$cacheId] = & $this->relations[$key];
			}
			return $this->relations[$key];
		} else {
			$this->relations[$key] = $relations->getResults();
			return $this->relations[$key];
		}
		
	}

    /**
     * return cache key for this model
     * 
     * @return string cacheKey
     */
    public function getCacheKey($level)
    {
        $cacheKey = $this->getCachePrefix() . '.level_' . $level;
		//add this cacheKey to key manager
		$this->addCacheKey($cacheKey);
		return $cacheKey;
    }
	
    /**
     * add a cache key to model cache manager
     * 
     * @return string cacheKey
     */
	public function addCacheKey($cacheKey){
		$managerKey = $this->getCachePrefix() . '_keys';
		//store this generated key to easy to clean up later
		$cacheKeys = Cache::remember($managerKey, HALOModel::$cacheDuration, function(){
			return array();
		});
		
		if(!isset($cacheKeys[$cacheKey])){
			$cacheKeys[$cacheKey] = 1;
			Cache::put($managerKey, $cacheKeys, HALOModel::$cacheDuration);
		}
	}
	
    /**
     * return cache key prefix for this model
     * 
     * @return string cacheKey
     */
    public function getCachePrefix()
    {
        $prefix = 'context_' . $this->getContext() . '.id_' . $this->id;
		return $prefix;
    }
	
    /**
     * clear all cache that associate to this model
     * 
     * @return string cacheKey
     */
	public function clearCache(){
		$managerKey = $this->getCachePrefix() . '_keys';
		$cacheKeys = Cache::get($managerKey, array());
		foreach($cacheKeys as $key => $val) {
			Cache::forget($key);
		}
	}
	
    /**
     * get short information
     * 
     * @return string shortInfo
     */
	public function getShortInfo(){
		if(is_null($this->_shortInfo)) $this->_shortInfo = new HALOQueue();
		return $this->_shortInfo;
	}
	
    /**
     * set short information
     * 
     * @return void
     */
	public function setShortInfo(HALOQueue $shortInfo){
		return $this->_shortInfo = $shortInfo;
	}
	
    /**
     * add short information
     * 
     * @return string cacheKey
     */
	public function insertShortInfo($data, $priority = 100){
		if(is_null($this->_shortInfo)) $this->_shortInfo = new HALOQueue();
		$this->_shortInfo[$priority] = $data;
	}
	
    /**
     * scopeByLabels
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @param  array  $params
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeByLabel($query, $params = array())
    {
		$labelIds = isset($params['label_id'])?(array) $params['label_id']: array();
		$labelCodes = isset($params['label_code'])?(array) $params['label_code']: array();
		$labelSlugs = isset($params['label_slug'])?(array) $params['label_slug']: array();

		$modelName = get_class($this);
		$tableName = $this->getTable();
		$query->leftJoin('halo_labelables', function($join) use($modelName, $tableName) {
			$join->on('halo_labelables.labelable_id', '=', $tableName . '.id')
				->on('halo_labelables.labelable_type', '=', DB::raw("'{$modelName}'"));
		});
		
		//convert input params to labelIds array
		if(empty($labelIds)){
			if(!empty($labelCodes)) {	//by label code
				$labelIds = HALOLabelModel::whereIn('label_code', $labelCodes)->lists('id');
			} else if(!empty($labelSlugs)) {	//by label slug
				$labelGroups = HALOLabelAPI::getLabelGroups($this, false, false);				
				foreach($labelGroups as $labelGroup) {
					foreach($labelGroup->labels as $label) {
						if (in_array(Str::slug($label->name), $labelSlugs)) {
							$labelIds[] = $label->id;
						}
					}
				}
			}
		}
		if(!empty($labelIds)) {	//by label Id
			$query->whereIn('halo_labelables.label_id', $labelIds);
		}
		// var_dump($labelIds, $query->getBindings(), $query->count(), $query->toSql());
		
		return $query;
    }

    /**
     * scopeByFilters
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @param  array  $params
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeByFilters($query, $params = array())
    {
		$filters = isset($params['filters'])?(array) $params['filters']: array();
		
		foreach($filters as $filterName => $filterValue) {
			$filter = HALOFilter::getFilterByName($filterName);
			//init filter value
			foreach($filter as $f){
				$f->value = $filterValue;
			}
			HALOFilter::applyFilters($query, $filter);
		}
		return $query;
	
	}
}
