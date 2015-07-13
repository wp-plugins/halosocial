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

class HALOProfileModel extends HALOModel
{
    protected $table = 'halo_profiles';

    protected $fillable = array('name', 'type', 'published', 'default', 'description');

    protected $toggleable = array('published', 'default');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('name' => 'required', 'type' => 'required');

    }

    /**
     * Return a list of configured profiles for a specific type 
     * 
     * @param  array  $type   
     * @param  boolean $header 
     * @return array          
     */
    public static function getProfileListOption($type, $header = false)
    {
		$profiles = HALOProfileModel::where('type', '=', $type)->where('published', 1)->get();
		
        $options = array_map(function ($profile) {
            $profile_group = $profile["name"];
            return array('value' => $profile['id'], 'title' => __halotext($profile_group), 'description' => $profile['description']);
        }, $profiles->toArray());
		
        if ($header) {
            //insert the header
            $headerOption = array('value' => '', 'title' => __halotext('-- Select --'));
            array_unshift($options, $headerOption);
        }

        return $options;
    }

    /**
     * Verify if the $profileId is existing
     * 
     * @param  int  $type      
     * @param  int  $profileId 
     * @return boolean            
     */
    public static function isExists($type, $profileId)
    {
        return HALOProfileModel::where('type', '=', $type)->where('id', '=', $profileId)->count();
    }

    /**
     * Return profile id by given profile name and profile type
     * 
     * @param  int  $name
     * @param  int  $type 
     * @param  int  $default 
     * @return boolean            
     */
    public static function getProfileId($name, $type, $default = 1)
    {
		$profile = HALOProfileModel::where('name', $name)->where('type', $type)->first();
		return $profile?$profile->id:$default;
    }

    /**
     * Save profile values
     * 
     * @param  array $owner  
     * @param  string $values 
     * @param  array  $access 
     * @param  array  $params 
     * @return bool          
     */
    public function saveFieldValues($owner, $values, $access = array(), $params = array())
    {

        //only field value in profile fields list are saved
        $fields = $this->getFields()->get();
        $acceptedValues = array();
        foreach ($fields as $field) {
            if (isset($values[$field->id])) {
                $acceptedValues[$field->id] = $field->toHALOField()->toPivotArray(array('value' => $values[$field->id], 'access' => isset($access[$field->id]) ? $access[$field->id] : 0, 'params' => isset($params[$field->id]) ? $params[$field->id] : ''));
            }
        }

        //get Profile field values relationship
        $owner->getProfileFieldValues()->sync($acceptedValues, false);
        return true;
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOProfileModel, HALOFIeldModel: many to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function getFields()
    {
        return $this->belongsToMany('HALOFieldModel', 'halo_profiles_fields', 'profile_id', 'field_id')
                    ->withPivot('ordering', 'required', 'published', 'id', 'field_id', 'params')
                    ->orderBy('ordering', 'asc')
                    ->orderBy('halo_profiles_fields.updated_at', 'desc')
                    ->withTimestamps();
    }

    /**
     * Get Fiel Values 
     * 
     * @param  array $ownerId 
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsToMany          
     */
    public function getFieldValues($ownerId)
    {
        if (empty($this->type)) {
            return null;
        }
        $params = array('ownerId' => $ownerId, 'type' => $this->type);
        $valueTableName = 'halo_' . $params['type'] . '_fields_values';
        $rtn = $this->belongsToMany('HALOFieldModel', 'halo_profiles_fields', 'profile_id', 'field_id')
                    ->withPivot('ordering', 'required', 'published', 'id')
                    ->leftJoin($valueTableName, function ($join) use ($params, $ownerId) {
            $valueTableName = 'halo_' . $params['type'] . '_fields_values';
            $ownerIdField = $params['type'] . '_id';
            $join->on($valueTableName . '.' . $ownerIdField, '=', DB::raw($ownerId));
            $join->on($valueTableName . '.field_id', '=', 'halo_profiles_fields.field_id');
        })
            ->select('halo_fields.*', 'ordering as pivot_ordering', 'required as pivot_required', 'published as pivot_published',
            'halo_profiles_fields.profile_id as pivot_profile_id', 'halo_profiles_fields.params as pivot_fparams',
            'halo_profiles_fields.field_id as pivot_field_id', 'value as pivot_value', 'access as pivot_access', $valueTableName . '.params as pivot_params')
            ->orderBy('ordering', 'asc')
            ->withTimestamps()
			->where('published', 1);
        return $rtn;
    }

    /**
     * Rebuild field ordering
     * 
     */
    public function rebuildFieldOrdering()
    {
        $fields = $this->getFields()->get();
        $syncFields = array();
        $ordering = 0;
        foreach ($fields as $field) {
            $syncFields[$field->pivot->field_id] = array('ordering' => $ordering
                , 'required' => $field->pivot->required
                , 'published' => $field->pivot->published
                , 'params' => $field->pivot->params);
            $ordering++;

        }
//            var_dump($syncFields);

        if (!empty($syncFields)) {
            $this->getFields()->sync($syncFields);
        }
    }

    /**
     * Apply filter on input query to show activity for a specific user stored in $value
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  string $value  
     * @param  string $params 
     * @return bool          
     */
    public static function userStreamApplyFilter(&$query, $value, $params)
    {
        try {
            $query = $query->where(function ($q) use ($value) {
                $q->orWhere('actor_id', '=', $value)
                    ->orWhere(function ($t) use ($value) {
                    $t->where('context', '=', 'profile')
                        ->where('target_id', '=', $value);
                });
            });
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Apply filter on input query to show activity for a category list stored in $value
     * 
     * @param  Illuminate\Database\Query\Builder $query  
     * @param  string $value  
     * @param  string $params 
     * @return bool         
     */
    public static function categoryStreamApplyFilter(&$query, $value, $params)
    {
        if (empty($value)) {
            //nothing to apply on the value
            return true;
        }
        $categoryIds = explode(',', $value);
        $categories = HALOCommonCategoryModel::getDescendantsAndSelfOfNodes($categoryIds);
        $ids = array();
        foreach ($categories as $cat) {
            $ids[] = $cat->id;
        }
        try {
            $query = $query->where('context', '=', 'category')
                           ->whereIn('target_id', $ids);
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    //displayfilter to display list of category for profile stream selection
    public static function getCategoriesDisplayFilter($params, $uiType)
    {
        $categoryId = Input::get('categoryid');
        $parent = null;
        if (!empty($categoryId) && ($category = HALOCommonCategoryModel::find($categoryId))) {
            $parent = $category;
        }

        $root = HALOCommonCategoryModel::buildCategoriesTree($parent);
        $options = empty($root->_children) ? array() : $root->_children;
        return $options;
    }

    /**
     * Return toggleable states of a field
     * 
     * @param  string $field
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
        if ($field == 'default') {
            return array(0 => array('title' => __halotext('Make Default'),
                'icon' => 'star-o text-danger'),
                1 => array('title' => __halotext('Default'),
                    'icon' => 'star text-danger')

            );
        }
    }

    /**
     * Return new state of a toggleable field
     * 
     * @param  string $field 
     * @return object       
     */
    public function toggleState($field)
    {
        if ($field == 'default') {
            //make sure this profile is in publish state
            if (!$this->published) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Only a published profile can be set as the default')));
                return null;
            }
            //make sure only one profile is set as default for the same type
            $profileIds = HALOProfileModel::where('type', $this->type)->lists('id');
            if (!empty($profileIds)) {
                DB::table('halo_profiles')->whereIn('id', $profileIds)
                                        ->update(array('default' => 0));
            }
            //refresh the current page to update the default list
            HALOResponse::refresh();
            //clear default profile cache
            Cache::forget('defaultProfiles');
            return parent::toggleState($field);
        } else {
            //refresh the current page to update the default list
            HALOResponse::refresh();
            return parent::toggleState($field);
        }
    }

    /**
     * Return default profile id for a specific profile type
     * 
     * @param  array $profileType 
     * @return bool              
     */
    public static function getDefaultProfileId($profileType)
    {
        static $cache = array();
        if (isset($cache[$profileType])) {
            return $cache[$profileType];
        }

        $defaultProfiles = Cache::rememberForever('defaultProfiles', function () {
            return HALOProfileModel::where('default', 1)->get();
        });
        if (!empty($defaultProfiles)) {
            foreach ($defaultProfiles as $default) {
                if ($default->type == $profileType) {
                    $cache[$profileType] = $default->id;
                    return $cache[$profileType];
                }
            }
        }
        return null;
    }

    /**
     * Return list of available  profile type options
     * 
     * @return array
     */
    public static function getProfileTypeOptions()
    {
        $default = array(
            array('value' => 'user', 'title' => 'User'),
            array('value' => 'category', 'title' => 'Category')
        );
        Event::fire('system.getProfileTypeOptions', array(&$default));
        return $default;
    }
	
	/*
	*	add fields to a profile by using input array
	*/
	public static function addFieldsToProfileArray($profile) {
		if(isset($profile['name']) && isset($profile['type'])  && isset($profile['fields'])) {
			$profileModel = HALOProfileModel::where('name', $profile['name'])
									->where('type', $profile['type'])
									->first();
			if($profileModel) {
				$fields = $profile['fields'];
				$syncData = array();
				foreach($fields as $ordering => $field) {
					$model = HALOFieldModel::where('fieldcode', $field['fieldcode'])->first();
					if($model) {
						$data = $field;
						unset($data['fieldcode']);
						$data['ordering'] = $ordering;
						$data['created_at'] = new DateTime;
						$data['updated_at'] = new DateTime;
						$syncData[$model->id] = $data;
					}
				}
				$profileModel->getFields()->sync($syncData);
				return true;
			}
			return false;
		}
		return false;
	}

    /**
     * Return the the name for displaying
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * Return the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

	
	/*
		return admin link for this profile
	*/
	public function getAdminLink($class = '') {
        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . 'href="' . $this->getAdminUrl() . '">' . $this->getDisplayName() . '</a>';
	}
	
    /**
     * return admin url for this profile 
     * 
     * @param  array  $params 
     * @return string
     */
    public function getAdminUrl(array $params = array())
    {
        if (!empty($this->id)) {
            $pString = '';
            if (!empty($params)) {
                $pString = '&' . http_build_query($params);
            }
            return URL::to('?app=admin&view=profiles&task=fields&uid=' . $this->id . $pString);
        }
        return '';
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
			switch($field) {
				case 'default':
					if($this->getFieldState($field) == 1) {	//only show readonly state for default profile
						$builder = HALOUIBuilder::getInstance('', 'form.readonlyState', array('state' => $currState, 'id' => $this->id,
							'zone' => $model . '_' . $field . '_' . $this->id,
							'model' => $model, 'field' => $field));			
						break;
					}
				case 'published':
					//@rule: if this is the only published profile of this type, show readonly state
					if($this->getFieldState($field) == 1 && (HALOProfileModel::where('type', $this->type)->where('published', 1)->count() <= 1) ) {
						$currState['title'] = sprintf(__halotext('This is the only published profile for %s'), $this->type);
						$builder = HALOUIBuilder::getInstance('', 'form.readonlyState', array('state' => $currState, 'id' => $this->id,
							'zone' => $model . '_' . $field . '_' . $this->id,
							'model' => $model, 'field' => $field));			
						break;						
					}
					//@rule: cant unpublished default profile
					if($this->getFieldState($field) == 1 && $this->getFieldState('default')) {
						$currState['title'] = __halotext('Default profile must be published');
						$builder = HALOUIBuilder::getInstance('', 'form.readonlyState', array('state' => $currState, 'id' => $this->id,
							'zone' => $model . '_' . $field . '_' . $this->id,
							'model' => $model, 'field' => $field));			
						break;					
					}
				default:
				$builder = HALOUIBuilder::getInstance('', 'form.toggleState', array('state' => $currState, 'id' => $this->id,
					'zone' => $model . '_' . $field . '_' . $this->id,
					'model' => $model, 'field' => $field));			
			}
            return $builder->fetch();
        } else {
            return '';
        }
    }
	
}
