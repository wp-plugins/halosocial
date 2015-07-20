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

class HALOFilterModel extends HALOModel
{
    public $value = null;//applying value for this filter
    public $uiType = null;//the uitype used for rendering this filter

    protected $table = 'halo_filters';

    protected $fillable = array('name', 'type', 'description', 'on_display_handler', 'on_apply_handler', 'published');

    protected $toggleable = array('published');

    /**
     * Get validate rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array_merge(array(
            'name' => 'required',
            'type' => 'required',
            'on_apply_handler' => 'required',
        ));

    }

    /**
     * Get bind Data 
     * @param  array  $postData 
     * @return HALOFilterModel           
     */
    public function bindData($postData = array())
    {
        $this->fill($postData);

        //extra converting and binding put here

        if (isset($postData['params'])) {
            $this->params = new HALOParams($postData['params'], 'query');
            $this->params = $this->params->toString();
        }

        return $this;
    }

    /**
     * Return display UI for this filter by calling the configured on_display_handler
     * 
     * @param  string  $uiType 
     * @param  boolean $force  
     * @return HALOUIBuilder     
     */
    public function getDisplayUI($uiType = 'form.filter_select', $force = false)
    {

        $orgUiType = $uiType;
        $params = new HALOParams($this->params);
		//check if event is defined
		$event = $this->getParams('displayEvent',null);
		//use on_display_handler as event if it starts with @
		if(!$event && strpos($this->on_display_handler, '@') === 0) {
			$event = substr($this->on_display_handler, 1);
		}
		if($event){
			$dataOptions = new stdClass();
			$dataOptions->options = array();
			Event::fire('filter.display.' . $event, array(&$dataOptions, &$params, &$uiType, $this), true);
			$options = $dataOptions->options;
		} else {
			//add postfix DisplayFilter to the handler
			$function = $this->on_display_handler . 'DisplayFilter';

			//if on display handler is not found
			$arr = explode('::', $function);
			if (!function_exists($function) && !(count($arr) == 2 && method_exists($arr[0], $arr[1]))) {
				$options = array();
			} else {
				//get the return options by calling DispalyFilter handler.
				//The display handler might decide what uiType is used to render the filter
				$options = call_user_func_array($function, array(&$params, &$uiType, $this));
			}
		}
		
        if ($force) {
            $this->uiType = $orgUiType;
        } else {
            $this->uiType = $params->get('uiType', $uiType);
        }
        //value
        if (is_null($this->value)) {
            //trying to get value from http request
            $this->value = Input::get('filters.' . $this->id, '');

        }

        //the options must be in the select options format
        $ui = HALOUIBuilder::getInstance('', $this->uiType, array('name' => $this->getInputName(),
            'title' => $params->get('title'),
            'value' => $this->value,
            'onChange' => $params->get('onchange'),
            'options' => $options,
            'filter' => $this,
            'params' => $params
        ));

        return $ui;
    }

    /**
     * Apply filter to the handler
     * 
     * @param  Illuminate\Database\Query $query 
     * @param  string $value 
     * @return Illuminate\Database\Query    
     */
    public function applyFilter(&$query, $value)
    {
        $params = new HALOParams($this->params);
		$event = $this->getParams('applyEvent',null);
		//use on_apply_handler as event if it starts with @
		if(!$event && strpos($this->on_apply_handler, '@') === 0) {
			$event = substr($this->on_apply_handler, 1);
		}
		if(!$event){
			//add postfix ApplyFilter to the handler
			$function = $this->on_apply_handler . 'ApplyFilter';
			//if the handler is not found, just return $query
			$arr = explode('::', $function);
			if (!function_exists($function) && !(count($arr) == 2 && method_exists($arr[0], $arr[1]))) {

				return $query;
			}

			$response = call_user_func_array($function, array(&$query, $value, $params));
		} else {
			$data = new stdClass();
			$data->query = $query;
			Event::fire('filter.apply.' . $event, array(&$data, $value, $params), true);
			$query = $data->query;
		}
        return $query;

    }

    /**
     * Generate meta string for this filter
     * 
     * @param  string $value 
     * @return string meta string for this filter
     */
    public function getMetaTags(&$metaTags, $value)
    {
		//the meta tag callback is defined via metaCb parameter
		if(($cbFunc = $this->getParams('metaCb',null))){
			//add postfix MetaFilter to the handler
			$function = $cbFunc . 'MetaFilter';
			//if the handler is not found, just return emty string
			$arr = explode('::', $function);
			if (!function_exists($function) && !(count($arr) == 2 && method_exists($arr[0], $arr[1]))) {

				return false;
			}

			try {
				call_user_func_array($function, array(&$metaTags, $value, $this));
			} catch (\Exception $e){
				return false;
			}
		}
		return true;

    }

    /**
     * Return this filter name used for html input
     * 
     * @return string
     */
    public function getInputName()
    {
        return 'filters[' . $this->id . ']';
    }

    /**
     * Return toggleable states ofa field
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

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return HALOFilter
     */
    public function save(array $options = array())
    {
        parent::save($options);
        //reload filter to cache
        HALOFilter::loadFilters(true);

    }

    /**
     * Rebuild filter ordering
     * 
     * @return HALOFilterModel
     */
    public static function rebuildFilterOrdering()
    {
        $filters = HALOFilterModel::orderBy('ordering', 'asc')
            ->orderBy('updated_at', 'desc')    ->get();
        if ($filters) {
            $ordering = 0;
            foreach ($filters as $filter) {
                $newOrdering = ++$ordering;
                if ($filter->ordering != $newOrdering) {
                    $filter->ordering = $newOrdering;
                    $filter->save();
                }
            }
        }
    }

    /**
     * Return current value from the input
     * 
     * @return tring
     */
    public function getInputValue()
    {
        $values = Input::get('filters');
        if (is_array($values) && isset($values[$this->id])) {
            return $values[$this->id];
        }
        return '';
    }
}
