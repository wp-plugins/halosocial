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

class HALOCommonCategoryModel extends HALONestedModel
{
    protected $table = 'halo_common_categories';

    protected $toggleable = array('published');

    /**
     * Column name which stores reference to parent's node.
     *
     * @var int
     */
    protected $parentColumn = 'parent_id';

    /**
     * Column name for the left index.
     *
     * @var int
     */
    protected $leftColumn = 'lft';

    /**
     * Column name for the right index.
     *
     * @var int
     */
    protected $rightColumn = 'rgt';

    /**
     * Column name for the depth field.
     *
     * @var int
     */
    protected $depthColumn = 'depth';

    /**
     * With Baum, all NestedSet-related fields are guarded from mass-assignment
     * by default.
     *
     * @var array
     */
    protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');

    protected $hidden = array('created_at', 'updated_at', 'published');

    protected $fillable = array('name', 'description', 'published', 'profile_id', 'scope_id');

    private $validator = null;

    private $_params = null;

    /**
     * Get scope_id value configured for this class object
     * 
     * @return int
     */
    public function getScopeValue()
    {
        return empty($this->scoped) ? 0 : $this->scoped['scope_id'];
    }
  
    /**
     * Get tree structure for categories
     * 
     * @param  array $root
     * @return array 
     */
    public static function buildCategoriesTree($root = null)
    {
        if (is_null($root)) {
            $root = static::root();
        }

		if(!$root) {
			return null;
		}
        $root = Cache::rememberForever('category.' . $root->getContext() . '.' . $root->id, function () use ($root) {
            $categories = $root->getDescendants();
            $catOptions = array();
            $curr = &$root;

            foreach ($categories as &$cat) {
                //move up until in the same level
                while ($cat->parent_id != $curr->id) {
                    $curr = $curr->_parent;
                }

                //move down 1 level
                $cat->_parent = &$curr;
                $cat->value = $cat->id;
                //only attaach published cat
                if ($cat->published) {
                    $curr->_children[] = $cat;
                }
                $curr = &$cat;
            }
            return $root;
        });
        return $root;
    }

    /******************** override parent functions ****************************/
    /**
     * Get a new "scoped" query builder for the Node's model.
     *
     * @param  bool  $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newNestedSetQuery($excludeDeleted = true)
    {
        $builder = $this->newQuery($excludeDeleted)->orderBy($this->getLeftColumnName());

        if (!empty($this->scoped)) {
            foreach ($this->scoped as $scopeFld => $scopeVal) {
                $builder->where($scopeFld, '=', $scopeVal);
            }
        }

        return $builder;
    }

    /**
     * Checkes if the given node is in the same scope as the current one.
     * 
     * @param  array $other 
     * @return bool
     */
    public function inSameScope($other)
    {
        foreach ((array) $this->scoped as $fld => $val) {
            if ($this->$fld != $other->$fld) {
                return false;
            }
        }

        return true;
    }

    /**
     * save category
     */
    public function save(array $options = array())
    {
        if (!empty($this->scoped)) {
            $this->scope_id = $this->getScopeValue();
        }
        return parent::save($options);
    }

    /******************** override parent functions ****************************/

    //////////////////////////////////// Define Relationships /////////////////////////
    /**
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parent()
    {
        return $this->belongsTo(get_class($this), 'parent_id');
    }
    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('name' => 'required', 'published' => 'required', 'parent_id' => 'required');

    }

    /**
     * Get profile 
     * 
     * @return Illuminate\Database\Query\Builder
     */
    public function getProfile()
    {

        $query = $this->belongsTo('HALOProfileModel', 'profile_id', 'id');

        if (empty($this->profile_id) || $query->count() == 0) {
            //fallback to default profile_id setting if doesn't exist
            $query = HALOProfileModel::where('id', '=', HALOConfig::get('category.defaultProfile', HALOProfileModel::getDefaultProfileId('category')));
        }

        return $query;
    }

    /**
     * Get Profile fields
     * 
     * @return object
     */
    public function getProfileFields()
    {
        $profile = $this->getProfile()->first();
        return $profile ? $profile->getFieldValues($this->id) : $this->getProfileFieldValues();
    }

    /**
     * Get profile field values
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function getProfileFieldValues()
    {
        return $this->belongsToMany('HALOFieldModel', 'halo_category_fields_values', 'category_id', 'field_id')
                    ->withPivot('value', 'access', 'params')
                    ->withTimestamps();
    }

    /**
     * Return the the name for display, either username of name based on backend config
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * Return the the name with link for display, either username of name based on backend config
     * @return  string
     */
    public function getDisplayLink($class = '')
    {

        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . ' href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Get url for this group
     * 
     * @return string
     */
    public function getUrl(array $params = array())
    {
        $pString = '';
        if (!empty($params)) {
            $pString = '&' . http_build_query($params);
        }
		//cache data
		HALOModel::setCachedModel($this);
        return URL::to('?view=category&task=show&uid=' . $this->id);
    }
	
    /**
     * return path string of this category
     *
     * @param  string $separator separator to join parent category
     * @param  boolean $self include this category name to string or not
     * @param  integer $depth how many parent category included in the path
     * @param  string $slug slug delimiter character
     * @param  boolean $force use cache or query database to get parent categories
     * @return string
     */
    public function getFullPath($separator = '', $self = false, $depth = 0, $slug = '', $force = false)
    {
		$ancestors = $this->getParams('ancestors',null);
		//check if query database required
        if($force || is_null($ancestors)){
			if($this->getRoot()){
				$ancestors = $this->ancestors()->withoutRoot()->where('published',1)->lists('name');
			} else {
				$ancestors = $this->ancestors()->where('published',1)->lists('name');
			}
			$this->setParams('ancestors',$ancestors);
			$this->save();
		}
		//check depth level
		if($depth && $depth < count($ancestors)){
			$nAncestors = array();
			//reverse ancestors to get correct depth level
			$ancestors = array_reverse($ancestors);
			for($i = 0; $i < $depth; $i++){
				$nAncestors[] = array_shift($ancestors);
			}
			//reverse to old ordering
			$ancestors = array_reverse($nAncestors);
		}
		//include self to the path
		if($self){
			$ancestors[] = $this->name;
		}
		//check if slug required
		if($slug){
			foreach($ancestors as $key => $parent){
				$ancestors[$key] = Str::slug($parent,$slug);
			}
		}
		//join $ancestors to return full path
		return implode($separator,$ancestors);
    }
    /**
     * function to rebuild of this category and its related category
     * 
     * @return boolean
     */
	public function rebuildParams($param){
		$children = $this->getDescendants();
		if($param == 'ancestors'){
			foreach($children as $child){
				$child->getFullPath('', false, 0, '', true);	//force to update param
			}
			$this->getFullPath('', false, 0, '', true);
		}
	}
	
    /**
     * Get toggleable states of a filed
     * 
     * @param  array $field 
     * @return array        
     */
    public function getStates($field)
    {
        if ($field == 'published') {
            return array(0 => array('title' => __halotext('Unpublished'),
                'icon' => 'times-circle text-danger'),
                1 => array('title' => __halotext('Published'),
                    'icon' => 'check-circle text-success')

            );
        }
    }

	/*
		check if can move this node to left
	*/
	public function canMoveLeft() {
		return $this->parent && $this->parent->getLeft() < $this->getLeft() - 1;
	}
	
	/*
		check if can move this node to right
	*/
	public function canMoveRight() {
		return $this->parent && $this->parent->getRight() > $this->getRight() + 1;
	}
	
	/*
		convert this common category model to detail category model
	*/
	public function toDetail($modelName) {
		if(class_exists($modelName)) {
			$detail = new $modelName();
			foreach($this->getAttributes() as $key => $guarded) {
				$detail->$key = $guarded;
			}
			return $detail;
		} else {
			return null;
		}
	}
	
	/*
		convert this detail category model to common category model
	*/
	public function toCommon() {
		$common = new HALOCommonCategoryModel();
		foreach($this->getAttributes() as $key => $guarded) {
			$common->$key = $guarded;
		}
		return $common;
	}
	
	/*
		process to bind/map category to target. This function check for multiple category condition and process it accordingly
	*/
	public static function bindCategories(&$target, &$data) {
		//check for multiple cateogry configure. Configure key must be context.multiplecategory
		$key = $target->getContext() . '.multiplecategory';
		if(HALOConfig::get($key, 0)) {
			//find the first category id in the array and map it to category_id
			if(isset($data['category_ids']) && is_array($data['category_ids'])) {
				$arr = array_slice($data['category_ids'], 0, 1);
				$target->category_id = array_shift($arr);
				$data['category_id'] = $target->category_id;
			}
		}
	}
	
	/*
		process to sync multiple categories to target.
	*/
	public static function syncCategories(&$target, &$data) {
		//check for multiple cateogry configure. Configure key must be context.multiplecategory
		$key = $target->getContext() . '.multiplecategory';
		if(HALOConfig::get($key, 0)) {
			//find the first category id in the array and map it to category_id
			if(isset($data['category_ids']) && is_array($data['category_ids'])) {
				$target->categories()->sync($data['category_ids']);
			}
		}
	}

	/*
		return display link of target categories
	*/
	public static function getDisplayLinks($target, array $params = array()) {
		//check for multiple cateogry configure. Configure key must be context.multiplecategory
		$key = $target->getContext() . '.multiplecategory';
		$class = isset($params['class'])?$params['class']:'';
		if(HALOConfig::get($key, 0)) {
			$arr = array();
			$categories = $target->categories;
			foreach($categories as $key => $category) {
				$category = $category->toDetail(get_class($target->category));
				if($category) {
					$arr[] = $category->getDisplayLink($class);
				}
			}
			return implode(' - ', $arr);
		} else {
			return $target->category->getDisplayLink($class);
		}
	}
}
