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

class LogEventHandler
{

	/**
	 * Subscribe 
	 * 
	 * @param  array $events
	 */
    public function subscribe($events)
    {
        //configuration settings
        $events->listen('config.loadSettings', 'LogEventHandler@onLoadConfigurationSettings');

        if (HALOConfig::get('actlog.enable', 0)) {

            $actionsStr = HALOConfig::get('actlog.actions', '');
            $actionsArray = explode("\n", $actionsStr);
            //

            //$actionsArray = array('activity.onRender');
            if (!empty($actionsArray)) {
                foreach ($actionsArray as $action) {
                    $action = trim($action);
                    if ($action != '' && strpos(';', $action) !== 0) {
                        $handler = str_replace('.', '____', $action);
                        $events->listen($action, 'LogEventHandler@' . $handler);
                    }
                }
            }
        }
    }

    /**
     * OnLoad Configuration settings 
     * 
     * @param  array $settings   
     */
    public function onLoadConfigurationSettings($settings)
    {
        $logCfg = new stdClass();
        $logCfg->title = "Action Log Settings";
        $logCfg->icon = "";
        $logCfg->builder = HALOUIBuilder::getInstance('actlog_cfg', 'config.group', array('name' => 'actlog_cfg'));

        //actionlog settings section
        $section = HALOUIBuilder::getInstance('actionlog', 'config.section', array('title' => __halotext('Action Log Settings')))
            ->addUI('actlog.enable', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'actlog.enable',
            'title' => __halotext('Enable action log'),
            'options' => array(HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
                HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No'))),
            ),
            'value' => HALOConfig::get('actlog.enable', 0))))
                ->addUI('actlog.actions', HALOUIBuilder::getInstance('', 'form.textarea', array('name' => 'actlog.actions',
            'title' => __halotext('Actions to log'),
            'value' => HALOConfig::get('actlog.actions', ''))));
        $logCfg->builder->addUI('section@array', $section);

        // $settings->config['actlog'] = $logCfg;

    }

    /**
     * Call 
     * @param  string $name      
     * @param  string $arguments  
     */
    public function __call($name, $arguments)
    {
        $action = str_replace('____', '.', $name);
        HALOLogModel::addLog($action, $arguments);
    }

}
