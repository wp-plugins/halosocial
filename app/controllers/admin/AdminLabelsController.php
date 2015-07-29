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

class AdminLabelsController extends AdminController
{

	/**
	 */
	public function __construct()
	{
			parent::__construct();
	}

	/**
	 * Show a list of all the labelGroups.
	 *
	 * @return View
	 */
	public function getIndex()
	{
		// Title
		$title = __halotext('Label Management');

		// Toolbar
		HALOToolbar::addToolbar('Add Label Group','halo-btn-success','','halo.label.showEditLabelGroupForm(0)','plus');
		HALOToolbar::addToolbar('Delete Label Group','halo-btn-danger','',"halo.popup.confirmDelete('Delete Label Group Confirm','Are you sure to delete this label group','halo.label.deleteSelectedLabelGroup()')",'times');
	
		// Grab all the labelGroups
		$labelGroups = new HALOLabelGroupModel();
		$labelGroups = HALOPagination::getData($labelGroups);

		// Show the page
		return View::make('admin/labels/index', compact('labelGroups', 'title'));
	}

	public function ajaxShowEditLabelGroupForm($labelGroupId=0){
		
		//form content
		$labelGroup = HALOLabelGroupModel::find($labelGroupId);
		if(is_null($labelGroup)){
			$labelGroup = new HALOLabelGroupModel();
			//setup default values
			$labelGroup->group_type = '';
		}
		$options = HALOLabelGroupModel::getGroupTypes();
		$options = array_merge(array(HALOObject::getInstance(array('value'=>'','title'=>'-- Select Type --'))),
								$options
								);
		$builder = HALOUIBuilder::getInstance('editField','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Label Group Name','value'=>$labelGroup->name)));
		if($labelGroup->group_code == 'HALO_SYSTEM_LABELS'){
			$builder->addUI('type', HALOUIBuilder::getInstance('','form.text',array('name'=>'group_type',
																							'title'=>'Label Group Type',
																							'disabled' => 'disabled',
																							'value'=>'System',
																							)
																	)
					);		
		} else {
			$builder->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'group_type',
																							'title'=>'Label Group Type',
																							'helptext'=>__halotext("Configure Label Group Type: Single - only one label is active for target content. Multiple - can set multiple labels for target content."),
																							'value'=>$labelGroup->group_type,
																							'options'=>$options
																							)
																	)
					);
		}
		if($labelGroupId){
			$builder->addUI('groupcode', HALOUIBuilder::getInstance('','form.text',array('name'=>'group_code','title'=>'Group Code', 'disabled' => 'disabled', 'placeholder'=>'Group Code','value'=>$labelGroup->getCode())));
		}
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.label.saveLabelGroup('".$labelGroupId."')","icon"=>"check"));
		$title = ($labelGroupId == 0)?"Add Label Gorup":"Edit Label Group";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxSaveLabelGroup($labelGroupId, $postData){
		//form content
		$labelGroup = HALOLabelGroupModel::find($labelGroupId);
		if(is_null($labelGroup)){
			$labelGroup = new HALOLabelGroupModel();
			//setup default values
			$labelGroup->group_type = '';
		}
		
		//validate data
		$validator = $labelGroup->bindData($postData)->validate();
		if($validator->fails()){
			HALOResponse::addMessage($validator->messages());
		} else {
			$labelGroup->save();
			//also update the group code
			$labelGroup->group_code = $labelGroup->getCode();
			$labelGroup->save();
			HALOResponse::refresh();
		}

        // Clear cached
        HALOConfig::clearCache();
        return HALOResponse::sendResponse();

	}
	
	public function ajaxDeleteLabelGroup($labelGroupIds){
		if(!is_array($labelGroupIds)){
			$labelGroupIds = array($labelGroupIds);
		}

		//delete labels
		$labelGroups = HALOLabelGroupModel::find($labelGroupIds);
		if($labelGroups){
			$labelGroups->load('labels');
			$labelIds = array();
			foreach($labelGroups as $group){
				$ids = $group->labels->lists('id');
				$labelIds = array_merge($labelIds,$ids);
			}
			if(!empty($labelIds)){
				HALOLabelModel::destroy($labelIds);
			}
			//delete pivot table
		}
		HALOLabelGroupModel::destroy($labelGroupIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Label Group was deleted', 'Label Groups were deleted', count($labelGroupIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');

        // Clear cached
        HALOConfig::clearCache();
        return HALOResponse::sendResponse();
		
	}

	public function ajaxShowEditLabelForm($labelId=0,$labelGroupId=0){
		
		//form content
		$label = HALOLabelModel::find($labelId);
		if(is_null($label)){
			$label = new HALOLabelModel();
			//setup default values
			$label->group_type = '';
		} else {
			$labelGroupId = $label->group_id;
		}
		$options = HALOLabelModel::getLabelTypes();
		$options = array_merge(array(HALOObject::getInstance(array('value'=>'','title'=>'-- Select Type --'))),
								$options
								);
		$roles = HALORoleModel::getRoleOptions('name');
		$builder = HALOUIBuilder::getInstance('editField','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Label Name','value'=>$label->name)))
					->addUI('group_id', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'group_id','value'=>$labelGroupId)));
		if($labelId) {
			$builder->addUI('labelcode', HALOUIBuilder::getInstance('','form.text',array('name'=>'label_code','disabled' => 'disabled', 'title'=>'Label Code','placeholder'=>'Label Code','value'=>$label->getCode())));
		}
		
		//do not allow to edit system label type
		$systemLabels = HALOLabelModel::getSystemLabels();
		$disabled = in_array($label->label_code, $systemLabels)?'disabled':'';
		if(in_array($label->label_code, $systemLabels)) {
			$builder->addUI('type', HALOUIBuilder::getInstance('','form.text',array('name'=>'label_type',
																							'title'=>'Label Type',
																							'disabled' => 'disabled',
																							'value'=>$label->label_type,
																							)
																	)
					);		
		} else {
			$builder->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'label_type',
																							'title'=>'Label Type',
																							'helptext'=>__halotext("Configure Label Type"),
																							'value'=>$label->label_type,
																							'onChange'=>"halo.label.showLabelTypeOpt(this.value);",
																							'options'=>$options
																							)
																	)
					);
		}			
		$builder->addUI('style', HALOUIBuilder::getInstance('','form.select_ext',array('name'=>'params[style]','title'=>__halotext('Label Style'),'helptext'=>__halotext('Configure style class for this label')
																																									,'value'=>$label->getParams('style','primary'),'options'=>HALOUtilHelper::getStyleOptions())))
					->addUI('allowedRoles', HALOUIBuilder::getInstance('','form.multiple_select',array('name'=>'params[allowedRoles]','title'=>__halotext('Who can use'),'helptext'=>__halotext('Configure who can use this label')
																																									,'value'=>$label->getParams('allowedRoles',array()),'options'=>$roles
																																									,'class'=>'label_type_opt opt_manual' . (($label->label_type == HALOLabelModel::LABEL_TYPE_MANUAL)?'':' hidden'))))
					->addUI('lifetime', HALOUIBuilder::getInstance('','form.text',array('name'=>'params[lifetime]','title'=>__halotext('Lifetime'),'helptext'=>__halotext('Configure label lifetime. Label automatically removed after lifetime exceeded')
																																									,'placeholder'=>__halotext('Lifetime in hours'),'value'=>$label->getParams('lifetime'),'options'=>$roles
																																									,'class'=>'label_type_opt opt_timer'. (($label->label_type == HALOLabelModel::LABEL_TYPE_TIMER)?'':' hidden'))))
				;	
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.label.saveLabel('".$labelId."')","icon"=>"check"));
		$title = ($labelId == 0)?"Add Label":"Edit Label";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxSaveLabel($labelId, $postData){
		//form content
		$label = HALOLabelModel::find($labelId);
		if(is_null($label)){
			$label = new HALOLabelModel();
			//setup default values
		}
		
		//validate data
		$validator = $label->bindData($postData)->validate();
		if($validator->fails()){
			HALOResponse::addMessage($validator->messages());
		} else {
			$label->save();
			//also update the group code
			$label->label_code = $label->getCode();
			$label->save();
			HALOResponse::refresh();
		}
		// Clear cached
        HALOConfig::clearCache();
		return HALOResponse::sendResponse();

	}
	
	public function ajaxDeleteLabel($labelId){
		if(!is_array($labelId)){
			$labelId = array($labelId);
		}

		//delete labels
		$labels = HALOLabelModel::find($labelId);
		if($labels){
			HALOLabelModel::destroy($labelId);
			//delete pivot table
		}
        // Clear cached
        HALOConfig::clearCache();
        return HALOResponse::sendResponse();
		
	}
		
	public function ajaxListLabelsInGroup($groupcode, $zone, $configName) {
		$labelGroup = HALOLabelGroupModel::where('group_code', $groupcode)->first();
		if($labelGroup) {
			$labels = $labelGroup->labels;
		} else {
			$labels = array();
		}

		$options = HALOUtilHelper::collection2options($labels, 'label_code', 'name');
		$newLabel = HALOUIBuilder::getInstance('', 'content', array('zone' => $zone))
					->addUI('user.label.new', HALOUIBuilder::getInstance('', 'form.select', array('name' => $configName,
								'title' => __halotext('Label for New Users'),
								'helptext' => __halotext('Status label that will be automatically be assigned for a new user'),
								'value' => HALOConfig::get($configName),
								'options' => $options)));
		HALOResponse::updateZone($newLabel->fetch());
		return HALOResponse::sendResponse();
	}
}
