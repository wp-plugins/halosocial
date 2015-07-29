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

class LabelController extends BaseController
{

    /**
     * Initializer.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax to show label assign form
     *
     * @param  string $context
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxShowAssignLabelForm($context, $target_id)
    {
        //get the target object

        $target = HALOModel::getCachedModel($context, $target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Invalid target object'))
            ->when(is_null($target->id) || !method_exists($target, 'labels'))
            ->apply();

        $user = HALOUserModel::getUser();
        //validate post data
        Redirect::ajaxError(__halotext('Login required'))
            ->when(empty($user->id))
            ->apply();

        $labelGroups = HALOLabelAPI::getLabelGroups($target, false);
        //get label groups for this target
        $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
            ->addUI('context', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'context', 'value' => $context)))
            ->addUI('target_id', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'target_id', 'value' => $target_id)))
			;
		if(count($labelGroups)) {
            $builder->addUI('labels', HALOUIBuilder::getInstance('', 'form.label_edit', array('name' => 'labelids', 'title' => __halotext('Labels')
						, 'helptext' => __halotext('Choose a label for this content object'), 'value' => $target->labels->lists('id')
                        , 'target' => $target
						, 'labelGroups' => $labelGroups)))
						;
		} else {
            $builder->addUI('labels', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('No labels available'), 'type' => 'warning')))
						;		
		}

        $content = $builder->fetch();
        $actionSave = HALOPopupHelper::getAction(array("name" => "Save", "onclick" => "halo.label.assignLabel()", "icon" => "check", 'class' => 'halo-btn-primary'));
        $title = __halotext('Set Labels');
        HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm');
        return HALOResponse::sendResponse();
    }
    /*

    /**
     * ajax to Assign Sicker
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxAssignLabel($postData)
    {
        $user = HALOUserModel::getUser();
        //get the target object
        $context = isset($postData['context']) ? $postData['context'] : '';
        $target_id = isset($postData['target_id']) ? $postData['target_id'] : '';
        $labelids = isset($postData['labelids']) ? $postData['labelids'] : array();
        if (empty($context) || empty($target_id) || !($target = HALOModel::getCachedModel($context, $target_id))) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Invalid target object')));
            return HALOResponse::sendResponse();
        }

        if (!HALOLabelAPI::assignLabels($target, $labelids, false)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Assign label error')));
            return false;

        }
        HALOResponse::refresh();
        return HALOResponse::sendResponse();

    }

}
