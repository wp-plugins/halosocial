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

class HALOFieldModel extends HALOModel
{
    const PRICE_FIELD_CODE = 'FIELD_PRICE';

    protected $table = 'halo_fields';

    protected $fillable = array('name', 'type', 'tips', 'fieldcode');

    private $validator = null;

    private $_params = null;

    private $_halofield = null;

    /**
     * To HALOField 
     * 
     * @return HALOField
     */
    public function toHALOField()
    {
        if (empty($this->_halofield)) {
            $this->_halofield = HALOField::getInstance($this->type, $this);
        }
        return $this->_halofield;
    }

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
        ), $this->toHALOField()->getValidateRule());

    }

    /**
     * Get validate value rule 
     * 
     * @return 
     */
    public function getValidateValueRule()
    {

    }

    /**
     * Get field code description
     * 
     * @return string
     */
    public function getFieldCode()
    {
        if ($this->id) {
            return empty($this->fieldcode) ? 'FIELD_' . $this->id : $this->fieldcode;
        } else {
            return '';
        }
    }
	
	public function getFieldType() {
		static $fieldList = null;
		if(is_null($fieldList)) {
			$fieldList = HALOField::getCustomFieldList();
		}
		$type = $this->type; 
		foreach($fieldList as $field) {
			if(isset($field->value) && isset($field->title) && $field->value == $type) {
				$type = $field->title;
			}
		}
		return $type;
	}
	
	/*
		check if this field is a display only field
	*/
	public function isReadOnly(){
		return HALOFieldModel::isReadOnlyField($this->type);
	}
	
	public static function isReadOnlyField($fieldType){
		$readOnlyTypes = array('tab', 'separator', 'group');
		return in_array($fieldType, $readOnlyTypes);
	}
	/*
		return an array of privacy value that 
	*/
	public static function getAllowedPrivacy($target) {
		$user = HALOUserModel::getUser();
		//public privacy
		$allowed = array(HALO_PRIVACY_PUBLIC);
		
		//member privacy
		if($user) {
			$allowed[] = HALO_PRIVACY_MEMBER;
		}
		
		//follower privacy
		if($user) {
			//special case, friends
			if(is_a($target, 'HALOUserModel') && $user->isFriend($target)){
				$allowed[] = HALO_PRIVACY_FOLLOWER;
			} else if(HALOAuth::hasRole('follower', $target)) {
				$allowed[] = HALO_PRIVACY_FOLLOWER;
			}
		}
		
		//only me
		if($user && is_a($target, 'HALOUserModel') && $user->id == $target->id) {
			$allowed[] = HALO_PRIVACY_ONLYME;
		}
		return $allowed;
	}
}
