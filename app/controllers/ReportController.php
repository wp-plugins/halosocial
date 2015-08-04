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

class ReportController extends BaseController
{

    /**
     * Initializer.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handler to show the report form
     *
     * @param  string $context
     * @param  int $target_id
     * @return JSON
     */
    public function ajaxShowReport($context, $target_id)
    {

        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Login required'));
        }

        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('This content cannot be reported'))
            ->when(is_null($target->id) || !method_exists($target, 'reports') || !method_exists($target, 'owner'))
            ->apply();

        //only one report per target is allowed
        Debugbar::addMessage($target, "id user");
        $reports = $target->whereHas('reports', function ($q) use ($my, $target) {
            $q->where('actor_id', $my->id)
            ->where('reportable_id', $target->id)
            ->where('owner_id', $target->owner->id);
        });
        
        if ($reports->count()) {
            //build the report form
            $builder = HALOUIBuilder::getInstance('reportForm', 'form.form', array('name' => 'popupForm'))
                ->addUI('message', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('You reported this content'), 'type' => 'warning')))
            ;
            $content = $builder->fetch();
            $title = __halotext('Report');
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Done'), "onclick" => "halo.popup.close()", 'class' => 'halo-btn halo-btn-primary'));
            return HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.showForm')
                ->sendResponse();
        }
        //build the report form
        $builder = HALOUIBuilder::getInstance('reportForm', 'form.form', array('name' => 'popupForm'))
            ->addUI('type', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'type', 'title' => __halotext('What do you want to report?'), 'value' => Input::old('type', ''),
            'options' => array(array('value' => 'Spam', 'title' => __halotext('Spam')),
                array('value' => 'Invalid', 'title' => __halotext('Invalid')),
                array('value' => 'Other', 'title' => __halotext('Other')),
            )
        )))
            ->addUI('message', HALOUIBuilder::getInstance('', 'form.textarea', array('name' => 'message', 'title' => __halotext('Content'), 'value' => Input::old('message', ''))))
            ->addUI('context', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'context', 'value' => $context)))
            ->addUI('target_id', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'target_id', 'value' => $target_id)))
        ;
        $content = $builder->fetch();
        $title = __halotext('Report');
        $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Report'), "onclick" => "halo.report.submitReport()", "icon" => "exclamation-circle", 'class' => 'halo-btn halo-btn-primary'));
        HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
            ->addScriptCall('halo.popup.setFormContent', $content)
            ->addScriptCall('halo.popup.addFormAction', $actionSave)
            ->addScriptCall('halo.popup.addFormActionCancel')
            ->addScriptCall('halo.popup.showForm');
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handler to submit a report
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSubmitReport($postData)
    {

        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Login required'));
        }

        $context = $postData['context'];
        $target_id = $postData['target_id'];
        Input::merge($postData);
        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);
        //validate post data
        Redirect::ajaxError(__halotext('This content cannot be reported'))
            ->when(is_null($target->id) || !method_exists($target, 'reports') || !method_exists($target, 'owner'))
            ->apply();
        //only one report per target is allowed
        $reports = $target->whereHas('reports', function ($q) use ($my, $target) {
            $q->where('actor_id', $my->id)
            ->where('reportable_id', $target->id)
            ->where('owner_id', $target->owner->id);
        });
        if ($reports->count()) {
            //build the report form
            $builder = HALOUIBuilder::getInstance('reportForm', 'form.form', array('name' => 'popupForm'))
                ->addUI('message', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('You reported this content'), 'type' => 'warning')))
            ;
            $content = $builder->fetch();
            $title = __halotext('Report Form');
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Done'), "onclick" => "halo.popup.close()", 'class' => 'halo-btn-primary'));
            return HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.showForm')
                ->sendResponse();
        }

        $report = new HALOReportModel();
        //validate data
        $report->bindData($postData);
        $report->actor_id = $my->id;
        $report->owner_id = $target->owner->id;
        $report->status = 0;
        if ($report->validate()->fails()) {
			$msg = $report->getValidator()->messages();
			HALOResponse::addMessage($msg);
			return HALOResponse::sendResponse();
        } else {
            //trigger before adding activity event
            if (Event::fire('report.onBeforeAdding', array($target, $report), true) === false) {
                //error occur, return
                return HALOResponse::sendResponse();
            }

            $target->reports()->save($report);
            //trigger event on report submitted
            Event::fire('report.onAfterAdding', array($target, $report));

            //build the report form
            $builder = HALOUIBuilder::getInstance('reportForm', 'form.form', array('name' => 'popupForm'))
                ->addUI('message', HALOUIBuilder::getInstance('', 'form.alert', array('title' => __halotext('Report has been submitted and sent to admin'), 'type' => 'warning')));
            $content = $builder->fetch();
            $title = __halotext('Report Submitted');
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Done'), "onclick" => "halo.popup.close()", 'class' => 'halo-btn-primary'));
            HALOResponse::addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.showForm');
        }

        return HALOResponse::sendResponse();

    }

}
