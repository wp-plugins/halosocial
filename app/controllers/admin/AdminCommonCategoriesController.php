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

class AdminCommonCategoriesController extends AdminController {


    /**
     * User Model
     * @var User
     */
    protected $category;

    /**
     * Inject the models.
     * @param User $user
     * @param Role $role
     * @param Permission $permission
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
        $title = __halotext('Category Management');

		// Toolbar
		//HALOToolbar::addToolbar(__halotext('Delete Category'),'','',"halo.popup.confirmDelete('Delete Category Confirm','Are you sure to delete this category','halo.category.deleteSelectedCategory()')",'times');
		
        // Grab all the categories
        $categories = new HALOCommonCategoryModel();
		$categories = HALOPagination::getData($categories->orderBy('lft','asc'),'lft');
		$filterName = 'admin.categories.index';
		$viewName = '';
        // Show the page
        return View::make('admin/categories/common_index', compact('categories', 'title', 'filterName', 'viewName'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $user
     * @return Response
     */
    public function getEdit($categoryId)
    {
		//determind edit or create mode
		$mode = ($categoryId == 0)?'create':'edit';
		
		//init category model
		if($mode == 'create'){
			//initialize default category 
			$category = new HALOCommonCategoryModel();
			$category->id = 0;
			$category->published = 1;
			// Title
			$title = __halotext('Add New Category');
		
		} else {
			$category = HALOCommonCategoryModel::find($categoryId);
            // Title
        	$title = __halotext('Edit Category');
			if ( empty($category->id) ) {
				return Redirect::to('?app=admin&view=commonCategories')->with('error', __halotext('Category does not exist'));
			}
		}

		// Toolbar
		HALOToolbar::addToolbar('Save','','',"jQuery('#halo-form-repeat').val(2);halo.form.submit('halo-admin-form')",'save');
		HALOToolbar::addToolbar('Save & Back','','',"halo.form.submit('halo-admin-form')",'save');
		HALOToolbar::addToolbar('Save & Add New','','',"jQuery('#halo-form-repeat').val(1);halo.form.submit('halo-admin-form')",'save');
		HALOToolbar::addToolbar('Cancel','',URL::to('?app=admin&view=commonCategories'),'','undo');

		//get profile
		$profile = $category->getProfile();
		
		$profileFields = $category->getProfileFields()->get();
		
		//binding with old input data
		$category->bindData(Input::old());
		$profileFields->each(function($field){
								$field->toHALOField()->bindData(Input::old());
							});
							
		$root = HALOCommonCategoryModel::roots()->get()->first();
		$categories = $root->getDescendantsAndSelf();
		return View::make('admin/categories/common_create_edit', compact('category', 'title', 'mode', 'profile', 'profileFields','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $category
     * @return Response
     */
    public function postEdit($categoryId)
    {
		//determind edit or create mode
		$mode = ($categoryId == 0)?'create':'edit';
		//init category model
		if($mode == 'create'){
			//initialize default category 
			$category = new HALOCommonCategoryModel();
			$category->id = 0;		
		} else {
			$category = HALOCommonCategoryModel::find($categoryId);
			if ( empty($category->id) ) {
				return Redirect::to('?app=admin&view=commonCategories')->with('error', __halotext('Category does not exist'));		
			}
		}

		//validate data
		$postData = Input::all();
		$rules = $category->getValidateRule();
		
		$profileFields = $category->getProfileFields()->get();

		foreach($profileFields as $field){
		
			$rules = array_merge($rules,$field->toHALOField()->getValidateValueRule());
		}
		
    $validator = Validator::make(Input::all(), $rules);
		
		if($validator->fails()){
		//var_dump($rules);
			
			$error = $category->getValidateMessages($validator);
            return Redirect::to('?app=admin&view=commonCategories&task=edit&uid='.$category->id)->withErrors($validator)->withInput();
		} else {
			//enforcement create or edit mode
			if($mode == 'create') {	
				unset($category->id);
				$sucessMsg = __halotext('Category was created successfully');
			} else {
				$sucessMsg = __halotext('Category was saved successfully');			
			}
			
			$category->bindData($postData);
			
			//get parent node
			$parent_id = isset($postData['parent_id'])?$postData['parent_id']:0;
			$parent = HALOCommonCategoryModel::find($parent_id);
			if(!$parent){
				return Redirect::to('?app=admin&view=commonCategories')->with('error', __halotext('Parent node does not exist'));					
			}
			$category->save();
			$category->makeChildOf($parent);
			
			//save category profile
			$profile = $category->getProfile()->first();
			$profile->saveFieldValues($category,Input::get('field',array()),Input::get('access',array()));
			//clear cache
			HALOConfig::clearCache();
			if(!$postData['repeat']){
					return Redirect::to('?app=admin&view=commonCategories')->with('success', $sucessMsg);				
			} else {
				if($postData['repeat'] == 2){
					return Redirect::to('?app=admin&view=commonCategories&task=edit&uid='.$category->id)->with('success', $sucessMsg);
				} else {
					return Redirect::to('?app=admin&view=commonCategories&task=edit&uid=0')->with('success', $sucessMsg);
				}
			}
		}
		
    }

    /**
     * Remove category.
     *
     * @param array $categoryIds list of categories
     * @return JAXResponse
     */
	public function ajaxDeleteCategory($categoryIds){
		$categoryIds = (array)$categoryIds;
		
		//@rule: do not delete category having children
		
		//delete all category field values records. It is more simple by using eloquent relationship but need to loop through all category model. 
		// But it is faster by using delete slq query
		DB::table('halo_category_fields_values')->whereIn('category_id', $categoryIds)->delete();
		
		HALOCommonCategoryModel::destroy($categoryIds);
		//clear cache
		HALOConfig::clearCache();

		HALOResponse::addScriptCall('halo.popup.reset');
		HALOResponse::addScriptCall('halo.popup.setMessage', __halontext('Category was deleted', 'Categories were deleted', count($categoryIds)) , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}

	/*
		function to move a category node up
	*/
	public function ajaxMoveUp($categoryId,$categoryType){
		if($categoryId){
			$category = HALOModel::getCachedModel($categoryType,$categoryId);
			$category->moveLeft();
			HALOResponse::addScriptCall('halo.util.reload');
		}

		//clear cache
		HALOConfig::clearCache();
		return HALOResponse::sendResponse();
	}
	
	/*
		function to move a category node down
	*/
	public function ajaxMoveDown($categoryId,$categoryType){
		if($categoryId){
			$category = HALOModel::getCachedModel($categoryType,$categoryId);
			$category->moveRight();
			HALOResponse::addScriptCall('halo.util.reload');
		}
		//clear cache
		HALOConfig::clearCache();

		return HALOResponse::sendResponse();
	}
}
