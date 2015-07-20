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

class AdminProfilesController extends AdminController
{

    /**
     * Inject the models.
     * @param HALOProfileModel $profile
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a list of all the profiles.
     *
     * @return View
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Profile Managment');

		// Toolbar
		if(HALOAuth::can('feature.profile')){
			HALOToolbar::addToolbar('Add Profile','','','halo.profile.showEditProfileForm(0)','plus');
			HALOToolbar::addToolbar('Delete Profile','','',"halo.popup.confirmDelete('Delete Profile Confirm','Are you sure to delete this profile','halo.profile.deleteSelectedProfile()')",'times');
		} else {
			HALOUtilHelper::getVersionToolbar();
		}

        // Get activated plugins
        $plugins = HALOPluginModel::getActivePlugins();
        $parsedNames = array();
        $plugins->each(function($p) use (&$parsedNames) {
            $parsedNames[] = str_replace('halo', '', strtolower($p->name));
        });

        // Grab all the profiles on activated plugins
        $profiles = new HALOProfileModel();
        if (!empty($parsedNames)) {
            $profiles = $profiles->whereIn('type', $parsedNames);
        }
		$profiles = HALOPagination::getData($profiles);

        // Show the page
        return View::make('admin/profiles/index', compact('profiles', 'title'));
    }

    /**
     * Display the specified resource.
     *
     * @param $profileId
     * @return Response
     */
	public function getFields($profileId){
        // Grab the profile data
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//invalide profile ID
			return null;
		}
		$fields = HALOPagination::getData($profile->getFields());

        // Title
        $title = __halotext('Profile Field Management');

		// Toolbar
		if(HALOAuth::can('feature.profile')){
			HALOToolbar::addToolbar('Back','',Url::to('?app=admin&view=profiles'),'','undo');
			HALOToolbar::addToolbar('Attach Field','','','halo.profile.showAttachFieldForm('.$profileId.',0)','plus');
			HALOToolbar::addToolbar('Detach Field','','',"halo.popup.confirmDelete('Detach Field Confirm','Are you sure to detach these fields','halo.profile.detachSelectedField(".$profileId.")')",'times');
			HALOToolbar::addToolbar('Add New Field','','','halo.profile.showAddNewFieldForm('.$profileId.',0)','plus');
		} else {
			HALOUtilHelper::getVersionToolbar();
		}
		

        // Show the page
        return View::make('admin/fields/indexProfile', compact('profile','fields', 'title'));
	
	}
	
	public function ajaxShowEditProfileForm($profileId=0){
		
		//form content
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//new mode
			$profile = new HALOProfileModel();
			//setup default values
			$profile->published = 1;
			$editPublished = '';
			$editType = '';
		} else {
			//edit mode
			$editPublished = (HALOProfileModel::where('type', $profile->type)->where('published', 1)->count() > 1)?'':'disabled';
			$editType = 'disabled';
		}
		
		
						
		$builder = HALOUIBuilder::getInstance('editProfile','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Profile Name','value'=>$profile->name)))
					->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'type','helptext'=>__halotext('Configure profile type'),
																							'title'=>'Profile Type',
																							'value'=>$profile->type,
																							'disabled' => $editType,
																							'options'=>array_merge(array(array('value'=>'','title'=>'-- Select Type --')),HALOProfileModel::getProfileTypeOptions())
																							)
																	)
					)
					->addUI('published', HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','title'=>'Published',
																							'value'=>$profile->published,
																							'disabled' => $editPublished,
																							'options'=>array(array('value'=>'1','title'=>'Yes'),
																											array('value'=>'0','title'=>'No')
																											)
																							)
																	)
					)
					->addUI('description', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'description','title'=>'Description','placeholder'=>'Description','value'=>$profile->description)));
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.profile.saveProfile('".$profileId."')","icon"=>"check"));
		$title = ($profileId == 0)?"Add New Profile":"Edit Profile";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxSaveProfile($profileId, $postData){
		
		//form content
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			$profile = new HALOProfileModel();
		}
		
		//validate data
		if($profile->bindData($postData)->validate()->fails()){
			
			$error = $profile->getValidateMessages();
			//ajax redirect to display function to display error
			Redirect::ajaxError(__halotext('Save Profile failed'))
					->setArgs(array($profileId))
					->withErrors($profile->getValidator())
					->with('errorMsg',$error)
					->apply();
		} else {
			$profile->save();
			HALOResponse::refresh();
			
		}
				
		return HALOResponse::sendResponse();

	}
	
	public function ajaxDeleteProfile($profileIds){
		
		$profileIds = (array)$profileIds;
		//loop on each profile to delete
		
		HALOProfileModel::destroy($profileIds);
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Profile was deleted', 'Profiles were deleted', count($profileIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}

	public function ajaxShowAddNewFieldForm($profileId){
		
		

		$profile = HALOProfileModel::find($profileId);
		Redirect::ajaxError(__halotext('Invalid profile ID'))
				->when(is_null($profile))
				->apply();
		
		//form content
		$field = new HALOFieldModel();
		//setup default values
		$field->published = 1;
		$field->required = 0;

		//prepare field ordering options
		$currFields = $profile->getFields()->orderBy('ordering','asc')->get();
		$orderingListOptions = array();
		foreach($currFields as $f){
			$orderingListOptions[] = array('value'=>$f->pivot->ordering,'title'=>$f->name);
		}

		$options = HALOField::getCustomFieldList();
		$options = array_merge(array(HALOObject::getInstance(array('value'=>'','title'=>'-- Select Type --'))),
								$options
								);
		$builder = HALOUIBuilder::getInstance('editField','form.edit_field',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Field Name','value'=>$field->name)))
					->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'type',
																							'title'=>'Field Type',
																							'helptext'=>__halotext("Configure field type. Each field type will have different display layout. You can also edit format."),
																							'value'=>$field->type,
																							'onChange'=>"halo.field.getFieldConfig(this.value);",
																							'options'=>$options
																							)
																	)
					)
					->addUI('fieldcode', HALOUIBuilder::getInstance('','form.text',array('name'=>'fieldcode','title'=>'Field Code','helptext'=>__halotext('Configure field code for this field. If not configured, a default field code will be generated by using the field ID.'),'placeholder'=>'Field Code','value'=>$field->getFieldCode())))
					->addUI('tooltip', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'tips','title'=>'Tooltip','placeholder'=>'Tooltip will be shown as a help icon next to the field title','value'=>$field->tips)))
					->addUI('config', HALOUIBuilder::getInstance('','grid.wrapper',array(''))
															->addUI('field_config@array',$field->toHALOField()->getConfigUI()))
					->addUI('ordering', HALOUIBuilder::getInstance('','form.select',array('name'=>'ordering',
																							'title'=>'Ordering Before',
																							'helptext'=>__halotext('Set field ordering when displaying fields in profile'),
																							'value'=>0,
																							'options'=>$orderingListOptions
																							)
																	)
					)
					->addUI('published', HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','title'=>'Published','size'=>6,
																							'value'=>$field->published,
																							'helptext'=>__halotext('Publish or Unpublish this field'),
																							'options'=>array(array('value'=>'1','title'=>'Yes'),
																											array('value'=>'0','title'=>'No')
																											)
																							)
																	)
					)
					->addUI('required', HALOUIBuilder::getInstance('','form.radio',array('name'=>'required','title'=>'Required','size'=>6,'helptext'=>__halotext('The required field forces user to enter its value when edting'),
																							'value'=>$field->required,
																							'options'=>array(array('value'=>'1','title'=>'Yes'),
																											array('value'=>'0','title'=>'No')
																											)
																							)
																	)
					)
					->addUI('highlight', HALOUIBuilder::getInstance('','form.radio',array('name'=>'params[highlight]','title'=>__halotext('Highlight'),'size'=>6,
																							'value'=>0,'helptext'=>__halotext('Highlight this field'),
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					)
					->addUI('privacy', HALOUIBuilder::getInstance('','form.radio',array('name'=>'params[enablePrivacy]','title'=>__halotext('Enable Privacy'),'size'=>6,
																							'value'=>0,
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					)
					;
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.profile.addNewProfileField('". $profileId ."')","icon"=>"check"));
		$title = __halotext("Add New Profile Field");
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();
		
	}

	/*
		function to save new field to a profile
	*/
	public function ajaxSaveNewProfileField($profileId, $postData){
		
		//form content
		$profile = HALOProfileModel::find($profileId);
		Redirect::ajaxError(__halotext('Invalid profile ID'))
				->when(is_null($profile))
				->apply();
		
		$field = new HALOFieldModel();
		
		//validate data
		if($field->bindData($postData)->validate()->fails()){
			
			$error = $field->getValidateMessages();
			//ajax redirect to display function to display error
			Redirect::ajaxError(__halotext('Save profile field failed'))
					->setArgs(array($profileId))
					->withErrors($field->getValidator())
					->with('errorMsg',$error)
					->apply();
		} else {
			Redirect::ajaxError(__halotext('Error occur while saving field'))
				->when(!$field->save())
				->apply();
			
			//redirect to the ajax function to attach field to form
			$postData['field_id'] = $field->id;
			return $this->ajaxAttachFieldToProfile($profileId, $postData);
			
		}
				
		return HALOResponse::sendResponse();

	}
	
	public function ajaxShowAttachFieldForm($profileId,$id=0){

		//form content
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//error return with message
			return  null;
		}
		$field = $profile->getFields()->where('halo_profiles_fields.field_id', '=', (int)$id)->get()->first();
		if(is_null($field)){
			//for new attach, just init default field values
			$field = new stdClass(); $field->pivot = new stdClass();
			$field->pivot->published = 1;
			$field->pivot->required = 0;
			$field->pivot->ordering = 0;
			$field->pivot->params = '';
			$field->id = '';
		}
		//prepare field ordering options
		$currFields = $profile->getFields()->orderBy('ordering','asc')->get();
		$orderingListOptions = array();
		foreach($currFields as $f){
			$orderingListOptions[] = array('value'=>$f->pivot->ordering,'title'=>$f->name);
		}
		
		//prepare field list options
		$fieldList = HALOFieldModel::all();
		
		$currentFieldIds = $profile->getFields()->get()->modelKeys();

		$fieldListOptions = array();
		foreach ($fieldList as $f){
			if(!in_array($f->id,$currentFieldIds) || $id == $f->id){
				//do not show fields existing in the current profile except the editing one
				$fieldListOptions[] = array('value'=>$f->id,'title'=>$f->name);
			}
		}
		
		//disable field type select if this is not new attach
		$readonly = empty($id)?'':'readonly';
		$disabled = empty($id)?'':'disabled';
		$privacyUI = ($readonly && $field->isReadOnly())?'form.hidden':'form.radio';
				
		$orderingOptions = array();
						
		$builder = HALOUIBuilder::getInstance('attachProfileField','form.form',array('name'=>'popupForm'))
					->addUI('fied_id', HALOUIBuilder::getInstance('','form.select',array('name'=>'field_id',
																							'title'=>__halotext('Select Profile Field'),
																							'value'=>$field->id,
																							'options'=>$fieldListOptions,
																							'readonly'=>$readonly,
																							'disabled'=>$disabled
																							)
																	)
					)
					->addUI('ordering', HALOUIBuilder::getInstance('','form.select',array('name'=>'ordering',
																							'title'=>__halotext('Ordering Before'),
																							'value'=>$field->pivot->ordering,
																							'options'=>$orderingListOptions
																							)
																	)
					)
					->addUI('published', HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','title'=>__halotext('Published'),'size'=>6,
																							'value'=>$field->pivot->published,
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					)
					->addUI('required', HALOUIBuilder::getInstance('',$privacyUI,array('name'=>'required','title'=>__halotext('Required'),'size'=>6,
																							'value'=>$field->pivot->required,'helptext'=>__halotext('The required field forces user to enter its value when edting'),
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					)
					->addUI('highlight', HALOUIBuilder::getInstance('',$privacyUI,array('name'=>'params[highlight]','title'=>__halotext('Highlight'),'size'=>6,
																							'value'=>HALOParams::getInstance($field->pivot->params)->get('highlight',0),'helptext'=>__halotext('Highlight this field'),
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					)
					->addUI('privacy', HALOUIBuilder::getInstance('',$privacyUI,array('name'=>'params[enablePrivacy]','title'=>__halotext('Enable Privacy'),'size'=>6,
																							'value'=>HALOParams::getInstance($field->pivot->params)->get('enablePrivacy',0),
																							'options'=>array(array('value'=>'1','title'=>__halotext('Yes')),
																											array('value'=>'0','title'=>__halotext('No'))
																											)
																							)
																	)
					);
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.profile.attachField('".$profileId."')","icon"=>"check"));
		$title = __halotext("Attach Field To Profile");
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

	public function ajaxAttachFieldToProfile($profileId, $postData){
		
		//form content
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//error return with message
			$error = 'Unknow Profile ID';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		
		if(!isset($postData['field_id'])){
			$error = 'Profile Field must be selected';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		
		$fieldId = $postData['field_id'];
		//prepare pivot data
		$ordering = isset($postData['ordering'])?$postData['ordering']:0;
		$published = isset($postData['published'])?$postData['published']:0;
		$required = isset($postData['required'])?$postData['required']:0;
		$params = isset($postData['params'])?$postData['params']:array();
		
		//set highlight fields array to profile params
		$highlight = $profile->getParams('highlight',array());
		if(!empty($postData['params']['highlight'])){
			$highlight[] = $fieldId;
		} else {
			$highlight = array_values(array_diff($highlight,(array)$fieldId));
		}
		$profile->setParams('highlight',$highlight);		
		$profile->save();
		
		//sync fields to profile
		$profile->getFields()->sync(array($fieldId => array('ordering' => $ordering,
															'published' => $published,
															'required' => $required,
															'params'=>json_encode($params))),false);

		//update ordering for all other field
		$profile->rebuildFieldOrdering();
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();

	}
	
	public function ajaxDetachFieldFromProfile($profileId,$fieldIds){
		
	
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//error return with message
			$error = 'Unknow Profile ID';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		
		//try to convert to array
		$fieldIds = (array) $fieldIds;

		//set highlight fields array to profile params
		$highlight = $profile->getParams('highlight',array());
		$highlight = array_values(array_diff($highlight,$fieldIds));
		$profile->setParams('highlight',$highlight);		
		$profile->save();
		
		$profile->getFields()->detach($fieldIds);
		//loop on each profile to delete
		//update field orderings
		$profile->rebuildFieldOrdering();
		HALOResponse::addScriptCall('halo.popup.setMessage', 'Fields are detached' , 'warning');
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}

	/*
		ajax call to change field ordering
	*/
	public function ajaxChangeFieldOrdering($profileId, $fieldId, $diff){
		$profile = HALOProfileModel::find($profileId);
		if(is_null($profile)){
			//error return with message
			$error = 'Unknow Profile ID';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		$field = $profile->getFields()->where('halo_profiles_fields.field_id', '=', (int)$fieldId)->get()->first();
		Redirect::ajaxError('Invalid field Id')
				->when(!$field)
				->apply();
		
		//tip: to move up, we need to decrement the diff 1 value
		$diff = ($diff < 0)?($diff):($diff + 1);
		$profile->getFields()->sync(array($fieldId => array('ordering' => $field->pivot->ordering + $diff,
															'published' => $field->pivot->published,
															'required' => $field->pivot->required,
															'params'=>$field->pivot->params)),false);
		//update ordering for all fields
		$profile->rebuildFieldOrdering();
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
		
	}
}
