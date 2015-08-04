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
 
abstract class HALONestedModel extends Baum\Node
{
    public $__haloCounters = array();

    public $__haloParamRelation = array();

    public $_children = array();

    public $_parent = null;

    protected $toggleable = array();

    protected $sluggable = array(
        'build_from' => null,
        'save_to' => 'slug',
    );

    // 'parent_id' column name
    protected $parentColumn = 'parent_id';

    // 'lft' column name
    protected $leftColumn = 'lidx';

    // 'rgt' column name
    protected $rightColumn = 'ridx';

    // 'depth' column name
    protected $depthColumn = 'depth';

    // guard attributes from mass-assignment
    protected $guarded = array('id', 'parent_id', 'lidx', 'ridx', 'depth');

    private $validator = null;

    private $_params = null;

	protected $_shortInfo = null;
    /**
     * Return all descendant node ò an aray nodeId
     *
     * @param  array $nodeIds
     * @return array
     */
    public static function getDescendantsAndSelfOfNodes($nodeIds)
    {
        $rtn = array();
        $nodeIds = (array) $nodeIds;
        $nodes = self::find($nodeIds);
        if (!empty($nodes)) {
            $node = $nodes[0];
            $query = $node->newNestedSetQuery();
			$query->where(function($query) use($nodes) {
				foreach ($nodes as $node) {
					$query->orWhere(function ($q) use ($node) {
						$q->where($node->getLeftColumnName(), '>=', $node->getLeft())
							->where($node->getRightColumnName(), '<=', $node->getRight());

					});

				}
				return $query;
			});
            $rtn = $query->get();
        }
        return $rtn;
    }

    /**
     * Binding put
     *
     * @param  array  $postData
     * @return HALONestedModel
     */
    public function bindData($postData = array())
    {
        $this->fill($postData);

        //extra convert and binding put here
        if (isset($postData['params'])) {
            $this->params = json_encode($postData['params']);
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
    /**
     * Validate input data @Abstract API
     * @param   array    $data    the input data to validate
     * @return  Validator    the validator object
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
     * Get params
     *
     * @param  string $key
     * @param  string $default
     * @return HALOParams
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
     * Set Params
     *
     * @param string  $key
     * @param string  $value
     * @return HALONestedModel
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
     * Clear Params
     *
     * @param  string $key
     * @return  HALONestedModel
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
     * @return string
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
     * Return context strign for this model object
     *
     * @return strign
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
     * Return zone id for this model object
     *
     * @param  int $level
     * @return int
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
     * Return display name without link for this model
     * @return string
     */
    public function getDisplayName()
    {

        return '';
    }

    /**
     * ORM relationship, HALOFollowerModel: polymorphic
     *
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function followers()
    {
        return $this->morphMany('HALOFollowerModel', 'followable');
    }

    /**
     * Return assigned followers id for this model
     *
     * @return array
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
     * Return brief data attributes for this model
     *
     * @return string
     */

    /**
     * Return view count
     * @return integer view count
     */
    public function getViewCount($update = false)
    {
        if ($update) {
            return HALOViewcounterModel::getCounter($this, true);
        } else {
            if ($this->views) {
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
     * @return int
     */
    public function getLikeCount()
    {
        return 0;
    }

    /**
     * Set relation by ref
     * 
     * @param Illuminate\Database\Eloquent\Relations $relation 
     * @param string $value    
     */
    public function setRelationRef($relation, &$value)
    {
        $this->relations[$relation] = &$value;
    }

    /**
     * Return new state of a toggleable field
     *
     * @param  string $field
     * @return bool
     */
    public function toggleState($field)
    {
        //toggle is only for int field and in toggleable list
        if (in_array($field, $this->toggleable)) {
            $val = intval($this->$field);
            $val++;
            $states = $this->getStates($field);
            $num = count($states);
            if ($num > 0) {
                $val = $val % $num;
            }
            $this->$field = $val;
            $this->save();
            return $this->$field;
        } else {
            return null;
        }
    }

    /**
     * Return toggleable states a field
     *
     * @param  string  $field
     * @return string
     */
    public function getStates($field)
    {
        //by default only enable/disable state provided. For additional states, need to override this method
        return array(0 => array('title' => __halotext('Disabled'),
            'icon' => 'times-circle text-danger'),
            1 => array('title' => __halotext('Enabled'),
                'icon' => 'check-circle text-success'),

        );
    }

    /**
     * Return table name for this model
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Return current toggleable state html
     *
     * @param  string $field
     * @return string
     */
    public function getStateHtml($field)
    {
        $states = $this->getStates($field);
        if (isset($states[$this->$field])) {
            $currState = $states[$this->$field];
            $model = $this->getContext();
            $builder = HALOUIBuilder::getInstance('', 'form.toggleState', array('state' => $currState, 'id' => $this->id,
                'zone' => $model . '_' . $field . '_' . $this->id,
                'model' => $model, 'field' => $field));
            return $builder->fetch();
        } else {
            return '';
        }
    }

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
     * @param string
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
     * Set relation counter attribute of this model
     *
     * @param string $relationName
     * @param int    $counter
     */
    public function setRelationCounter($relationName, $counter)
    {
        //init if not set
        $this->__haloCounters[$relationName] = $counter;
    }

    /**
     * Get relation counter attribute of this model
     *
     * @param  string  $relationName
     * @param  integer $default
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
     * Set param ralation model
     *
     * @param string $relationName
     * @param string $relation
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
     * Get cover ratio
     * 
     * @return int
     */
    public function getCoverRatio()
    {
        return '8:3';
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
}
