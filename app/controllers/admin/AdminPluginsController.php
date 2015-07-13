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

class AdminPluginsController extends AdminController
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
        $title = __halotext('Plugin Management');
		// Toolbar
		HALOToolbar::addToolbar('Install Plugin','','','halo.plugin.showInstallPluginForm()','plus');
		HALOToolbar::addToolbar('Uninstall Plugin','','',"halo.popup.confirmDelete('Uninstall Plugin Confirm','Are you sure to uninstall this plugin?','halo.plugin.deleteSelectedPlugin()')",'times');
		
        // Grab all the plugins
        $plugins = new HALOPluginModel();
		$plugins = HALOPagination::getData($plugins);

        // Show the page
        return View::make('admin/plugins/index', compact('plugins', 'title'));
    }

	public function installPkg(){
		//@todo: check permission
		
		if(!HALOAuth::can('plugin.install')){
		
		}
		
		//plugin pkg is not provided
		if(!isset($_FILES['plugin_pkg']['tmp_name'])){
			return Redirect::to('?app=admin&view=plugins')->with('error', __halotext('Please select the plugin file to proceed'));
		}
		$tmp_file = $_FILES['plugin_pkg']['tmp_name'];
		if(!file_exists($tmp_file)){
			return Redirect::to('?app=admin&view=plugins')->with('error', __halotext('The plugin file does not exists in the file system'));
		}
		$pkg_name = $_FILES['plugin_pkg']['name'];
		$pkg_parts = pathinfo(strtolower($pkg_name));
		$path_parts = pathinfo(strtolower($tmp_file));
		$pkg_tmp = $path_parts['dirname'] . '/' . $path_parts['filename'] . '.' . $pkg_parts['extension'];
		$allowed_ext = array('zip');
		if(!in_array($pkg_parts['extension'],$allowed_ext)){
			return Redirect::to('?app=admin&view=plugins')->with('error', __halotext('Wrong plugin extension.'));
		}
		
		File::move($tmp_file,$pkg_tmp);
		
		$rtn = HALOPlugin::install($pkg_tmp);
		if($rtn->any()){
			return Redirect::to('?app=admin&view=plugins')->withErrors($rtn);
		} else {
			return Redirect::to('?app=admin&view=plugins')->with('success', __halotext('The plugin was installed successfully.'));
		}
	}

	public function ajaxShowInstallPluginForm(){
		
		//form content
						
		$builder = HALOUIBuilder::getInstance('installPlugin','form.form_data',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.file',array('name'=>'plugin_pkg','title'=>'Plugin Package')))
					->addUI('csrf', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'_token','value'=>csrf_token())))
					->addUI('task', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'task','value'=>'installPkg')));
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Install","onclick"=>"halo.form.submit('popupForm')","icon"=>"check"));
		$title = "Install New Plugin";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}
	
	public function ajaxShowEditPluginForm($fieldId=0){
		
		//form content
		$field = HALOFieldModel::find($fieldId);
		if(is_null($field)){
			$field = new HALOFieldModel();
			//setup default values
			$field->published = 1;
			$field->required = 0;
		}
						
		$builder = HALOUIBuilder::getInstance('editField','form.edit_field',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Field Name','value'=>$field->name)))
					->addUI('type', HALOUIBuilder::getInstance('','form.select',array('name'=>'type',
																							'title'=>'Field Type',
																							'value'=>$field->type,
																							'onChange'=>"halo.field.getFieldConfig(this.value);",
																							'options'=>array(array('value'=>'','title'=>'-- Select Type --'),
																											array('value'=>'text','title'=>'Text'),
																											array('value'=>'select','title'=>'Select')
																											)
																							)
																	)
					)
					->addUI('tooltip', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'tips','title'=>'Tooltip','placeholder'=>'Tooltip','value'=>$field->tips)))
					->addUI('config', HALOUIBuilder::getInstance('','grid.rows',array(''))
															->addUI('field_config@array',$field->toHALOField()->getConfigUI()));
		$content = 	$builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.field.saveField('".$fieldId."')","icon"=>"check"));
		$title = ($fieldId == 0)?"Add New Field":"Edit Field";
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}
	
	public function ajaxUninstallPlugin($ids){
		if(!is_array($ids)){
			$ids = array($ids);
		}
		//loop on each field to delete
		$messages = HALOError::getMessageBag();
		foreach($ids as $id){
			$plugin = HALOPluginModel::getInstance($id);
			if(!$plugin){
				$messages = HALOError::failed(sprintf(__halotext('Invalid Plugin Id: %s'),$id),$messages);
			} else {
				$rtn = $plugin->uninstall();
				if($rtn->any()){
					$messages->merge($rtn->getMessages());
				}
			}
		}
		if($messages->any()){
			HALOResponse::addScriptCall('halo.popup.setMessage', $messages->all() , 'danger', true);
		
		} else {
			HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Plugin was uninstalled', 'Plugins were uninstalled', count($ids)) , 'warning', true);
		
		}
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}
	/*
		ajax call to change field ordering
	*/
	public function ajaxChangeOrdering($pluginId, $diff){
		$plugin = HALOPluginModel::find($pluginId);
		if(is_null($plugin)){
			//error return with message
			$error = 'Unknow Plugin ID';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		
		//tip: to move up, we need to decrement the diff 1 value
		$diff = ($diff < 0)?($diff):($diff + 1);
		$plugin->ordering = $plugin->ordering + $diff;
		$plugin->save();
		//update ordering for all fields
		HALOPluginModel::rebuildOrdering();
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
		
	}
		
}
