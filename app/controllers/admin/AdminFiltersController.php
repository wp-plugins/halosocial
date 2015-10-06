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

class AdminFiltersController extends AdminController {


    /**
     * Filter Model
     */
    protected $filter;

    /**
     * Init the models.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Filter Management');

		// Toolbar
		HALOToolbar::addToolbar(__halotext('Reset Filters'),'','','halo.filter.resetFilters()','refresh');
		HALOToolbar::addToolbar(__halotext('Add Filter'),'','','halo.filter.showEditFilterForm(0)','plus');
		HALOToolbar::addToolbar(__halotext('Delete Filter'),'','',"halo.popup.confirmDelete('Delete Filter Confirm','Are you sure to delete this filter?','halo.filter.deleteSelectedFilter()')",'times');
		
        // Grab all the filters
        $filters = new HALOFilterModel();
		$filters = HALOPagination::getData($filters);

        // Show the page
        return View::make('admin/filters/index', compact('filters', 'title'));

    }

    /**
     * Show popup to edit/create filter
     *
     * @param $filterId
     * @return Response
     */
	public function ajaxShowEditFilterForm($filterId=0){
		
		//determind edit or create mode
		$mode = ($filterId == 0)?'create':'edit';

		//init filter model
		if($mode == 'create'){
			//initialize default category 
			$filter = new HALOFilterModel();
			$filter->id = 0;
			// Title
			$title = __halotext('Create New Filter');
		} else {
			$filter = HALOFilterModel::find($filterId);

            // Title
        	$title = __halotext('Edit Filter');
			//redirect if filter doesn not exits
			Redirect::ajaxError(__halotext('Filter does not exists'))
					->when(empty($filter->id))
					->apply();
		}
						
		$builder = HALOUIBuilder::getInstance('editFilter','form.form',array('name'=>'popupForm'))
					->addUI('name', HALOUIBuilder::getInstance('','form.text',array('name'=>'name','title'=>'Name','placeholder'=>'Filter Name','value'=>$filter->name)))
					->addUI('type', HALOUIBuilder::getInstance('','form.text',array('name'=>'type','title'=>'Type','placeholder'=>'Filter Type','value'=>$filter->type)))
					->addUI('description', HALOUIBuilder::getInstance('','form.textarea',array('name'=>'description','title'=>'Description','placeholder'=>'Description','value'=>$filter->description)))
					->addUI('on_display_handler', HALOUIBuilder::getInstance('','form.text',array('name'=>'on_display_handler','title'=>'Display Handler','placeholder'=>'Display Handler','value'=>$filter->on_display_handler)))
					->addUI('on_apply_handler', HALOUIBuilder::getInstance('','form.text',array('name'=>'on_apply_handler','title'=>'Apply Handler','placeholder'=>'Apply Handler','value'=>$filter->on_apply_handler)))
					->addUI('published', HALOUIBuilder::getInstance('','form.radio',array('name'=>'published','title'=>'Published',
																							'value'=>$filter->published,
																							'options'=>array(array('value'=>'1','title'=>'Yes'),
																											array('value'=>'0','title'=>'No')
																											)
																							)
																	)
					)
					->addUI('params', HALOUIBuilder::getInstance('','form.text',array('name'=>'params','title'=>'Params String','placeholder'=>'Params String','value'=>HALOParams::getInstance($filter->params)->toQuery(','))))
					;
		$content = $builder->fetch();
		$actionSave = HALOPopupHelper::getAction(array("name"=>"Save","onclick"=>"halo.filter.saveFilter('".$filterId."')","icon"=>"check"));
		HALOResponse::addScriptCall('halo.popup.setFormTitle', $title )
					->addScriptCall('halo.popup.setFormContent', $content )
					->addScriptCall('halo.popup.addFormAction', $actionSave )
					->addScriptCall('halo.popup.addFormActionCancel')
					->addScriptCall('halo.popup.showForm' );
		return HALOResponse::sendResponse();

	}

    /**
     * Save filters
     *
     * @param $filterId
     * @param $postData
     * @return Response
     */
	public function ajaxSaveFilter($filterId, $postData){
		//form content
		$filter = HALOFilterModel::find($filterId);
		if(is_null($filter)){
			$filter = new HALOFilterModel();
		}
		
		//validate data
		if($filter->bindData($postData)->validate()->fails()){
			
			$error = $filter->getValidateMessages();
			Redirect::ajaxError(__halotext('Save filter failed'))
					->setArgs(array($filterId))
					->withErrors($filter->getValidator())
					->with('errorMsg',$error)
					->apply();
		} else {
			$filter->save();
			HALOResponse::refresh();
			
		}
				
		return HALOResponse::sendResponse();

	}

    /**
     * Remove filter.
     *
     * @param array $categoryIds list of categories
     * @return JAXResponse
     */
	public function ajaxDeleteFilter($filterIds){
		$filterIds = (array)$filterIds;
		
		//@rule: do not delete category having children
				
		HALOFilterModel::destroy($filterIds);
		//reload filter to cache
		HALOFilter::loadFilters(true);

		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Filter was deleted', 'Filters were deleted', count($filterIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}


	/*
		ajax handler to reset filters
	*/
	public function ajaxResetFilters($postData){
		$confirm = isset($postData['confirm'])?$postData['confirm']:0;
		$title = __halotext('Reset Filters');
		if($confirm){
			//reset table filter before seeding
			require_once(dirname(__FILE__).'/../../database/migrations/2013_12_22_003514_create_halo_filters_table.php');
			$table = new CreateHaloFiltersTable();
			$table->down();
			$table->up();

			$seeder =  new FiltersTableSeeder();
			$seeder->run();
			//trigger filter reset
			Event::fire('system.resetFilters', array());

			//reload filter to cache
			HALOFilter::loadFilters(true);
			
			HALOResponse::addScriptCall('halo.popup.reset')
				->addScriptCall('halo.popup.setFormTitle', $title )
				->addScriptCall('halo.popup.setMessage', __halotext('Filters have been reset') , 'success', true)
				->addScriptCall('halo.popup.addFormAction', '{"name": "'.__halotext('Reload').'","onclick": "halo.util.reload()","href": "javascript:void(0);"}')
				->addScriptCall('halo.popup.showForm' );
			return HALOResponse::sendResponse();

		} else {
			//show confirm dialog
			$builder = HALOUIBuilder::getInstance('editProfile','form.form',array('name'=>'popupForm'))
						->addUI('confirm', HALOUIBuilder::getInstance('','form.hidden',array('name'=>'confirm','value'=>1)));
			$content = 	$builder->fetch();
			HALOResponse::addScriptCall('halo.popup.reset')
				->addScriptCall('halo.popup.setFormTitle', $title )
				->addScriptCall('halo.popup.setMessage', __halotext('Are you sure to reset all current filter settings?') , 'error')
				->addScriptCall('halo.popup.setFormContent', $content )
				->addScriptCall('halo.popup.addFormAction', '{"name": "'.__halotext('Yes').'","onclick": "halo.filter.resetFilters()","href": "javascript:void(0);"}')
				->addScriptCall('halo.popup.addFormActionCancel')
				->addScriptCall('halo.popup.showForm' );
					
			return HALOResponse::sendResponse();
		
		}
	}

	/*
		ajax call to change filter ordering
	*/
	public function ajaxChangeOrdering($filterId, $diff){
		$filter = HALOFilterModel::find($filterId);
		if(is_null($filter)){
			//error return with message
			$error = 'Unknow Filter';
			HALOResponse::addScriptCall('halo.popup.setMessage', $error , 'warning');
			return HALOResponse::sendResponse();		
		}
		
		//tip: to move up, we need to decrement the diff 1 value
		$diff = ($diff < 0)?($diff):($diff + 1);
		$filter->ordering = $filter->ordering + $diff;
		$filter->save();
		//update ordering for all fields
		HALOFilterModel::rebuildFilterOrdering();
		
		HALOResponse::refresh();
		return HALOResponse::sendResponse();
		
	}
	
}
