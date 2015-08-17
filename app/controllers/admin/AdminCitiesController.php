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

class AdminCitiesController extends AdminController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a list of all the fields.
     *
     * @return View
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Cities Management');

		// Toolbar
		HALOToolbar::addToolbar('Add City','','','halo.location.showEditCityForm(0)','plus');
		HALOToolbar::addToolbar('Delete City','','',"halo.popup.confirmDelete('Delete City Confirm','Are you sure to delete this city','halo.location.deleteSelectedCity()')",'times');
		
        // Grab all the fields
        $cities = new HALOCityModel();
		$cities = HALOPagination::getData($cities);

        // Show the page
        return View::make('admin/location/cities', compact('cities', 'title'));
    }

	public function ajaxShowEditCityForm($cityId=0){
		
		//form content
		$city = HALOCityModel::find($cityId);
		if(is_null($city)){
			$city = new HALOCityModel();
		}
		$builder = HALOUIBuilder::getInstance('editField','form.edit_field',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'City Name','value'=>$city->name)))
					;
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.location.saveCity('".$cityId."')","icon"=>"check"));
		$title = ($cityId == 0)?"Add New City":"Edit City";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxSaveCity($cityId, $postData){
		//form content
		$city = HALOCityModel::find($cityId);
		if(is_null($city)){
			$city = new HALOCityModel();
		}
		//validate data
		$validator = $city->bindData($postData)->validate();
		if($validator->fails()){			
			HALOResponse::addMessage($validator->messages());
		} else {
			$city->save();
			HALOResponse::refresh();			
		}
				
		return HALOResponse::sendResponse();

	}
	
	public function ajaxDeleteCity($cityIds){
		if(!is_array($cityIds)){
			$cityIds = array($cityIds);
		}
		//loop on each field to delete
		
		HALOCityModel::destroy($cityIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('City is deleted', 'Cities are deleted', count($cityIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}
		
}
