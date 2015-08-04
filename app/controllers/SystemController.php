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

class SystemController extends BaseController
{

    /**
     * Initializer.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handle to auto update the client content
     *
     * @return JSON
     */
    public function ajaxUpdateContent()
    {

        $numargs = func_num_args();
        $arg_list = func_get_args();

        //return HALOResponse::sendResponse();

        foreach ($arg_list as $updaterArgs) {
            //get the updater controller
            $context = isset($updaterArgs['context']) ? $updaterArgs['context'] : '';
            if ($context) {
                $controllerName = ucfirst($context) . "Controller";
                $func = "ContentUpdater";
                $args = array($updaterArgs);
                $controller = new $controllerName;
                call_user_func_array(array($controller, $func), $args);
            }
        }

        //keep the online state
        if (!HALOResponse::isAjax() && HALOAuth::can('site.view')) {
            HALOOnlineuserModel::online();
        }

        //set the interval update
        HALOResponse::addScriptCall('halo.updater.setInterval', HALOConfig::get('global.updateInterval'));
        return HALOResponse::sendResponse();

    }
    /**
     * [ajaxToggleState description]
     *
     * @param  int $id
     * @param  string $model
     * @param  string $field
     * @return JSON
     */
    public function ajaxToggleState($id, $model, $field)
    {
        //get model states
        $modelName = 'HALO' . ucfirst($model) . 'Model';

        $stateModel = new $modelName();
        $stateModel = $stateModel->find($id);
        if ($stateModel->id) {
            //permission checking
            if ($stateModel->canToggle($field)) {
                $newState = $stateModel->toggleState($field);
                $html = $stateModel->getStateHtml($field);
                HALOResponse::updateZone($html);
            }

        }

        return HALOResponse::sendResponse();
    }

    /**
     * return brief information html of a target
     *
     * @param  string $briefContext
     * @param  int $briefId
     * @return JSON
     */
    public function ajaxGetBrief($briefContext, $briefId)
    {
        //get model states
        $modelName = 'HALO' . ucfirst($briefContext) . 'Model';

        $target = new $modelName();
        $target = $target->find($briefId);
        Redirect::ajaxError(__halotext('Invalid target object'))
            ->when(is_null($target->id) || !method_exists($target, 'getBriefBuilder'))
            ->apply();
        $builder = $target->getBriefBuilder();
        return HALOResponse::addScriptCall('halo.brief.updateBrief', $briefContext, $briefId, $builder->fetch())
                                                                                                    ->sendResponse();
    }

    /**
     * return slug edit html of a target
     *
     * @param  Sstring $context
     * @param  int $id
     * @return JSON
     */
    public function ajaxEditSlug($context, $id)
    {
        $target = HALOModel::getCachedModel($context, $id);

        if ($target) {
            $html = $target->getSlugEdit();
            HALOResponse::updateZone($html);

        }
        return HALOResponse::sendResponse();
    }

    /**
     * save slug configure for a target
     *
     * @param  string $context
     * @param  int $id
     * @param  string $slug
     * @return JSON
     */
    public function ajaxSaveSlug($context, $id, $slug)
    {
        $target = HALOModel::getCachedModel($context, $id);

        if ($target) {
            if ($target->canEdit()) {
                $target->setSlug($slug);
                $target->save();
            }
            $html = $target->getSlugDisplay();
            HALOResponse::updateZone($html);
        }
        return HALOResponse::sendResponse();
    }

    /**
     * ajax to display a share link block
     *
     * @param  string $url
     * @param  string $halozone
     * @return JSON
     */
    public function ajaxAddShareLinkBlock($url, $halozone)
    {
        $info = HALOBrowseHelper::fetchUrl($url);
        if ($info) {
            $builder = HALOUIBuilder::getInstance('', 'share_link_block_preview', array('info' => $info, 'zone' => $halozone, 'url' => $url));
            HALOResponse::updateZone($builder->fetch());
            return HALOResponse::sendResponse();
        } else {
            HALOResponse::addMessage(HALOError::failed(__halotext('Fetch URL is not found.')));
            return HALOResponse::sendResponse();
        }

        return HALOResponse::sendResponse();
    }

    /**
     * ajax to remove link preview associated with a target object
     *
     * @param  string $context
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxRemoveLinkPreview($context, $target_id)
    {
        $target = HALOModel::getCachedModel($context, $target_id);
        if ($target && method_exists($target, 'canEditPreview') && $target->canEditPreview()) {
            $target->clearParams('urlpreview', '');
            $target->clearParams('imagepreview_id', '');
            $target->save();
        }

        return HALOResponse::sendResponse();
    }

    /**
     * function to display/save custom filter form
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSaveCustomFilter($postData)
    {
        $my = HALOUserModel::getUser();

        if (!$my) {
            return HALOResponse::sendResponse();
        }
        $filterId = $postData['filter_id'];
        //detect save or display save form
        $customFilters = $my->getCustomFilters($filterId);
        $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'));
        if (isset($postData['id']) || isset($postData['name'])) {
            if (isset($postData['id']) && !empty($postData['id'])) {
                //override the existing filter
                $customFilter = HALOCustomFilterModel::find($postData['id']);
                if (!$customFilter) {
                    HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Filter')));
                    return HALOResponse::sendResponse();
                }
                if (empty($postData['name'])) {
                    $postData['name'] = $customFilter->name;
                }
            } else if ($customFilters->count() >= 3) {
                //only accept 3 custom filters
                $customFilter = $customFilters->first();
            } else {
                //new filter
                $customFilter = new HALOCustomFilterModel();
            }

            $customFilter->bindData($postData);
            if ($customFilter->validate()->fails()) {
                $msg = $customFilter->getValidator()->messages();
                HALOResponse::addMessage($msg);
                return HALOResponse::sendResponse();
            }
            $customFilter->filter_str = json_encode($postData['filters']);
            $customFilter->filter_id = $filterId;
            $customFilter->creator_id = $my->id;

            if (!$customFilter->save()) {
                HALOResponse::addMessage(HALOError::failed(__halotext('Could not save custom filter')));
                return HALOResponse::sendResponse();

            }
            $builder->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('Custom filter was saved succesfully'))));
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Done'), "onclick" => "halo.filter.redraw()", "icon" => "check", 'class' => 'halo-btn-primary'));
        } else {
            //show the form
            if ($customFilters->count()) {
                $builder->addUI('id', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'id', 'value' => '', 'title' => __halotext('Override Old Filter')
                    , 'validation' => 'required', 'options' => HALOUtilHelper::collection2options($customFilters, 'id', 'name'))))
                    ->addUI('name', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'name', 'value' => '', 'title' => __halotext('Or Save As New Filter'), 'validation' => 'required')));
            } else {
                $builder->addUI('name', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'name', 'value' => '', 'title' => __halotext('Save New Filter'), 'validation' => 'required')))
                ;
            }
            $formId = $postData['formId'];
            $builder->addUI('filter_id', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'filter_id', 'value' => '')));
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Save'), "onclick" => "halo.filter.saveCustomFilter('" . $formId . "')", "icon" => "check", 'class' => 'halo-btn-primary'));
        }
        $content = $builder->fetch();
        $title = __halotext('Save custom filter');
        HALOResponse::addScriptCall('halo.popup.reset')
            ->addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm');
        return HALOResponse::sendResponse();
    }

    /**
     * update inline location form field
     * @param  string $context
     * @param  int $targetId
     * @return JSON
     */
    public function ajaxRefreshInlineLocation($context, $targetId)
    {
        $target = HALOModel::getCachedModel($context, $targetId);
        if ($target && method_exists($target, 'location')) {
            $builder = HALOUIBuilder::getInstance('', 'form.inline_location', array('location' => $target->location, 'target' => $target,
                'class' => 'loc-' . $target->getContext()
            ));
            HALOResponse::updateZone($builder->fetch());
        }
        return HALOResponse::sendResponse();
    }
	
	/*
		function to return ui settings
	*/
	public function ajaxLoadUISettings($pkg) {
		$data = array();
		if($pkg == 'pkg1') {	//for core package
			$data['promoteToggle'] = array('func' => 'replace', 
											'sVal'	=> array('halo.bookmark.add', __halotext('Add Bookmark')),
											'nVal'	=> array('halo.bookmark.remove', __halotext('Remove Bookmark'))
										);
		}
		
		//trigger events
		Event::fire('system.loadUISettings', array($pkg, &$data));
		HALOResponse::addScriptCall('halo.uitoggle.savePkg', $pkg, json_encode($data));
		return HALOResponse::sendResponse();
	}

}
