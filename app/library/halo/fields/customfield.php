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

class HALOField
{
    public static $configParams = array();
    protected $model = null;
    protected $type = '';

    /**
     * Get an specifc HALOField type object
     *
     * @param string $type HALOField type name
     * @param HALOFieldModel $model reference HALOFieldModel
     * @return HALOField type object
     */
    public static function getInstance($type, HALOFieldModel &$model = null)
    {
		//load all field
		static $loadFields = null;
		if(is_null($loadFields)) {
			$fieldList = self::getCustomFieldList();
			foreach($fieldList as $field) {
				if(isset($field->value)){
					$filename = dirname(__FILE__) . '/' . basename($field->value) . '.php';
					if (file_exists($filename)) {
						require_once($filename);
					}
				}
			}
		}
        $className = 'HALOField' . ucfirst($type);
        if (!class_exists($className)) {
            //rollback to default class
            $className = 'HALOField';
        }
        $field = new $className();
        $field->model = $model;
        $field->type = $type;
        return $field;
    }

    /**
     * Bind post data to this field value
     *
     * @param array $postData post data array
     * @return HALOField type object
     */
    public function bindData($postData = array())
    {
        if (isset($postData['field'][$this->id])) {
            $this->value = $postData['field'][$this->id];
        }
        if (isset($postData['access'][$this->id])) {
            $this->access = $postData['field'][$this->id];
        }
        return $this;
    }

    /**
     * Load the json configuration params data from .json file	
     * 
     * @param string $type HALOField type name
     * @return HALOParams params object
     */
    public static function getConfigParams($type)
    {
        if (!isset(self::$configParams[$type])) {
            //prevent dir traverse
            $filename = dirname(__FILE__) . '/' . basename($type) . '.config.php';
            $json_str = '';
            if (file_exists($filename)) {
                $json_str = (include $filename);
            }
            self::$configParams[$type] = new HALOParams($json_str);
        }
        return self::$configParams[$type];
    }

    /**
     * Read the customfieldlist.config.php file to return the list of custom field supported
     * 
     * @return array $fields
     */
    public static function getCustomFieldList()
    {
        $filename = dirname(__FILE__) . '/customfieldlist.config.php';
        $fields = array();
        if (file_exists($filename)) {
            $json_str = (include $filename);
            $fields = json_decode($json_str);
        }
        return $fields;

    }

    /**
     * Get the configuration UI for this field
     * 
     * @return string configuration html inputs for this field
     */
    public function getConfigUI()
    {
        //get configuration file
        $configParams = HALOField::getConfigParams($this->type);
        $uiArray = HALOUIBuilderArray::getInstanceFromJSON($configParams->params);
        //set init value
        foreach ($uiArray as $ui) {
            if (!empty($ui->name)) {
                $default = isset($ui->default) ? $ui->default : '';
                if ($this->model) {
                    $ui->value = $this->model->getParams($ui->name, $default);
                } else {
                    $ui->value = $default;
                }

                $ui->name = 'params[' . $ui->name . ']';
            }
        }
        return $uiArray;
    }

    /**
     * Display field  as readable html
     * 
     * @param  string $template
     * @param  array  $attr
     * @return Field html
     */
    public function getReadableUI($template = "form.readonly_field", array $attr = array())
    {
		//by default do not show field that doesn't have value
		if($template === "form.readonly_field" && ($this->value === '' || is_null($this->value))) {
			return '';
		}
		
        $defaultAttr = array('title' => $this->getTitleUI(),
            'value' => $this->getValueUI(),
            'type' => $this->type,
            'name' => $this->getFieldName());
        $attr = array_merge($defaultAttr, $attr);
        return HALOUIBuilder::getInstance('', $template, $attr)->fetch();
    }

    /**
     * Load the json configuration params data from .json file
     *
     * @return HALOParams params object
     */
    public function getEditableUI()
    {
        return '';
    }

    /**
     * Display field value html only
     *
     * @param  string $template
     * @return Field value html
     */

    public function getValueUI($template = "")
    {
        return ($this->value === '') ? __halotext("N/A") : HALOOutputHelper::text2html($this->value);
    }

    /**
     * Display field title html only
     *
     * @param  string $template
     * @return Field title html
     */
    public function getTitleUI($template = "")
    {
        return $this->name;
    }

    /**
     * get file name 
     * 
     * @return return field name
     */
    public function getFieldName()
    {
        return 'field[' . $this->id . ']';
    }

    /**
     * @param  array $data
     * @return array return pivot values array that ready for saving
     */
    public function toPivotArray($data)
    {
        //make sure all data elements in string format
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $data[$key] = json_encode($value);
            }
        }
        return $data;
    }

    /**
     * Return field Id from field code @todo: query in db to get the correct field Id
     * 
     * @param  string $fieldCode 
     * @return array valiation rule
     */
    public static function getFieldId($fieldCode)
    {
        //check $fieldCode format
        $prefix = 'FIELD_';
        if (strpos($fieldCode, $prefix) !== false && (int) substr($fieldCode, strlen($prefix)) > 0) {
            return (int) substr($fieldCode, strlen($prefix));
        } else {
            static $inited = null;
            static $fields;
            if (is_null($inited)) {
                $fields = HALOFieldModel::select('id', 'fieldcode')->get();
                $inited = true;
            }
            //check in the fields collection for the matched field
            foreach ($fields as $field) {
                if ($field->getFieldCode() == $fieldCode) {
                    return $field->id;
                }
            }
        }
        return 0;
    }

    /**
     * Return validation rule for this fields
     *
     * @return array valiation rule
     */
    public function getValidateRule()
    {
        return array();
    }

    /**
     * Return validation rule for value of this field
     *
     * @return array valiation value rule
     */
    public function getValidateValueRule()
    {
        //the validate value rule is stored in params as name validate_rule
        $rule = $this->getValidateValueString();
        return array('field.' . $this->model->id => $rule);
    }

    /**
     * Return validation string for value of this field
     *
     * @return array valiation value rule
     */
    public function getValidateValueString()
    {
        $rule = $this->model->getParams('validate_rule', '');
        //combine with required status in the profile field configuration
        if ($this->model->pivot->required && strpos($rule, 'required') === false) {
            if ($rule != '') {
                $rule = $rule . '|required';
            } else {
                $rule = 'required';
            }
        }
        return $rule;
    }

    /**
     * check if privacy setting is enabled on this field
     * 
     * @return bool
     */
    public function isEnabledPrivacy()
    {
        return HALOParams::getInstance($this->model->pivot->fparams)->get('enablePrivacy', 0);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key == 'value' || $key == 'access') {
            //from pivot
            return $this->model->pivot->$key;
        } else if ($key == 'vparams') {
            return $this->model->pivot->params;
        }
        return $this->model->$key;
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if ($key == 'value' || $key == 'access') {
            //from pivot
            $this->model->pivot->$key = $value;
        } else if ($key == 'vparams') {
            $this->model->pivot->params = $value;
        }
        $this->model->$key = $value;
    }

    /**
     * a wrapper function that return privacy configuration UI for the input field
     * 
     * @return mixed
     */
	public static function renderPrivacyConfigHtml($field) {
		if($field) {
			return $field->getPrivacyConfigHtml();
		}
		return '';
	}
    /**
     * return privacy config UI for this field
     * 
     * @return mixed
     */
    public function getPrivacyConfigHtml()
    {
        if ($this->isEnabledPrivacy()) {
            return HALOUIBuilder::getInstance('', 'form.privacy', array('name' => 'access[' . $this->id . ']',
                'value' => $this->access,
                'class' => '', 'id' => 'access_' . $this->id))->fetch();
        } else {
            return '';
        }
    }

    /**
     * find field by fieldcode from an array of fields
     * 
     * @param  string $fields 
     * @param  int $fieldCode
     * @return mixed
     */
    public static function findField($fields, $fieldCode)
    {
        foreach ($fields as $field) {
            if ($field->getFieldCode() == $fieldCode) {
                return $field;
            }
        }
        return null;
    }
}
