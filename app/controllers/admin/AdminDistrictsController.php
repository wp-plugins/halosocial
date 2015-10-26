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

class AdminDistrictsController extends AdminController
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
        $title = __halotext('District Management');

		// Toolbar
		HALOToolbar::addToolbar('Add District','','','halo.location.showEditDistrictForm(0)','plus');
		HALOToolbar::addToolbar('Delete District','','',"halo.popup.confirmDelete('Delete District Confirm','Are you sure to delete this district','halo.location.deleteSelectedDistrict()')",'times');
		
        // Grab all the fields
        $districts = new HALODistrictModel();
		$districts = HALOPagination::getData($districts);

        // Show the page
        return View::make('admin/location/districts', compact('districts', 'title'));
    }

	
	public function ajaxShowEditDistrictForm($districtId=0){
		
		//form content
		$district = HALODistrictModel::find($districtId);
		if(is_null($district)){
			$district = new HALODistrictModel();
		}
		$options = HALOCityModel::all();
		$options = HALOUtilHelper::collection2options($options,'id','name');
		$builder = HALOUIBuilder::getInstance('editField','form.edit_field',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Distrcit Name','value'=>$district->name)))
					->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'city_id',
																							'title'=>'City',
																							'value'=>$district->city_id,
																							'options'=>$options
																							)
																	)
					)
					;
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.location.saveDistrict('".$districtId."')","icon"=>"check"));
		$title = ($districtId == 0)?"Add New District":"Edit District";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxSaveDistrict($districtId, $postData){
		//form content
		$district = HALODistrictModel::find($districtId);
		if(is_null($district)){
			$district = new HALODistrictModel();
		}
		//validate data
		$validator = $district->bindData($postData)->validate();
		if($validator->fails()){			
			HALOResponse::addMessage($validator->messages());
		} else {
			$district->save();
			HALOResponse::refresh();			
		}
				
		return HALOResponse::sendResponse();

	}
	
	public function ajaxDeleteDistrict($districtIds){
		if(!is_array($districtIds)){
			$districtIds = array($districtIds);
		}
		//loop on each field to delete
		
		HALODistrictModel::destroy($districtIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('District is deleted', 'Districts are deleted', count($districtIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}
		
}
