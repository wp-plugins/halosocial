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

class BanEventHandler
{

	/**
	 * Subscribe 
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        //configuration settings
        $events->listen('config.loadSettings', 'BanEventHandler@onLoadConfigurationSettings');
        $enable = HALOConfig::get('ban.enable');
        if ($enable) {
            //check role
            $events->listen('auth.onBeforeCheckingRole', 'BanEventHandler@onCheckRole');

            //check permission
            $events->listen('auth.onBeforeCheckingPermission', 'BanEventHandler@onCheckPermission');

            //on submit ban
            $events->listen('ban.onSubmit', 'BanEventHandler@onSubmit');

            //event handler for getting notification settings
            $events->listen('notification.onLoadingSettings', 'BanEventHandler@onNotificationLoading');

            //event handler for notification rendering
            $events->listen('notification.onRender', 'BanEventHandler@onNotificationRender');

            //event handler for notification rendering
            $events->listen('notification.onRenderEmail', 'BanEventHandler@onNotificationRenderEmail');

            //event handler for notification rendering
            $events->listen('user.loadResponseActions', 'BanEventHandler@onLoadUserResponseActions');
        }
    }

    /**
     * On Check Role 
     * 
     * @param  string $role          
     * @param  array $assetInstance 
     * @param  HALOUserModel $user_id       
     * @return bool               
     */
    public function onCheckRole($role, $assetInstance, $user_id)
    {
        $user = HALOUserModel::getUser($user_id);
        $settings = HALOBanModel::getSettings($user);
        foreach ($settings as $setting) {
            if (isset($setting['value']) && isset($setting['roles']) && in_array(strtolower($role->name), $setting['roles'])) {
                return false;
            }
        }
    }

    /**
     * On check permission 
     * 
     * @param  string $permission    
     * @param  mixed  $assetInstance 
     * @param  int $user_id
     * @return bool                
     */
    public function onCheckPermission($permission, $assetInstance, $user_id)
    {
        $user = HALOUserModel::getUser($user_id);
        $settings = HALOBanModel::getSettings($user);
        foreach ($settings as $setting) {
            if (isset($setting['value']) && isset($setting['permissions']) && in_array($permission, $setting['permissions'])) {
                return false;
            }
        }
    }

    /**
     * On Load User Response Actions 
     * 
     * @param  int $user 
     * @return HALOUIBuilder
     */
    public function onLoadUserResponseActions($user)
    {
        if (HALOBanModel::canBan($user)) {
            echo HALOUIBuilder::getInstance('', 'user.responseActionItem', array('title' => __halotext('Restrictions'), 'icon' => 'minus-circle',
                'onClick' => "halo.user.banForm('" . $user->id . "')"))->fetch();
        }
    }

    /**
     * event handler to load default notification settings
     * 
     * @param  HALOObject $settings            
     */
    public function onNotificationLoading(HALOObject $settings)
    {
        //new group join request
        $settings->setNsValue('ban.submit.i', 1);
        $settings->setNsValue('ban.submit.e', 1);
        $settings->setNsValue('ban.submit.d', __halotext('Restrict user from an action'));

    }

    /**
     * On Submit 
     * 
     * @param  array $banActions             
     */
    public function onSubmit($banActions)
    {
        foreach ($banActions as $banAction) {
            //prepare data
            $options = array();
            $options['action'] = 'ban.submit';
            $options['context'] = $banAction->getContext();
            $options['target_id'] = $banAction->id;
            $params = HALOParams::getInstance();

            $options['params'] = $params->toString();
            //receivers
            $options['receivers'] = $banAction->target_id;

            $rtn = HALONotificationAPI::add($options, false);
        }
    }

    /**
     * Event handler to render notification
     * 
     * @param  string $notification                
     */
    public function onNotificationRender($notification)
    {
        switch ($notification->action) {
            case 'ban.submit':
                //prepare data
                $this->renderBanSubmit($notification);
                break;
            default:

        }

    }

    /**
     * Event handler to display comment create notification
     * 
     * @param  stdClass $notification 
     * @return stdClass               
     */
    public function renderBanSubmit($notification)
    {
        $attachment = new stdClass();

        $my = HALOUserModel::getUser();
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //remove myself from the actor list
        $actorIds = array_diff($actorIds, array($my->id));

        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s restricted you from "%s" for %s'), $actorsHtml, $target->getDescription(), '<span class="halo-countdown" data-countdown="' . $target->getDuration() . '"><span>');
            //render response actions
            $attachment->content = '';
        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render notification
     * 
     * @param  stdClass $notification 
     * @param  stdClass $to           
     * @param  stdClass $mailqueue                 
     */
    public function onNotificationRenderEmail($notification, $to, $mailqueue)
    {
        switch ($notification->action) {
            default:
        }

    }

    /**
     * Event handler to load ban settings
     * 
     * @param  mixed $settings          
     */
    public function onLoadConfigurationSettings($settings)
    {
        $banCfg = new stdClass();
        $banCfg->title = __halotext("User Restrictions");
        $banCfg->icon = "";
        $banCfg->builder = HALOUIBuilder::getInstance('ban_cfg', 'config.group', array('name' => 'ban_cfg'));

        //settings section
        $roles = array(array('value' => '', 'title' => __halotext('-- select --')));
        $roles = array_merge($roles, HALOAuth::getRoleOptions('name'));
		$systemRoles = HALOAuth::getRoleOptions('name', $systemOnly = true);

        // $permissions = HALOAuth::getPermissionOptions();
        $permissions = array(HALOObject::getInstance(array('value' => '', 'title' => __halotext('-- select --'))));
        $permissions = array_merge($permissions, HALOPermissionModel::getPermissionOptions('name'));

        foreach ($roles as $idx => $role) {
            $roles[$idx]['value'] = strtolower($roles[$idx]['value']);
        }
		
        foreach ($systemRoles as $idx => $role) {
            $systemRoles[$idx]['value'] = strtolower($systemRoles[$idx]['value']);
        }
		
        $section = HALOUIBuilder::getInstance('bansettings', 'config.section', array('title' => __halotext('Settings')))
            ->addUI('ban.enable', HALOUIBuilder::getInstance('', 'form.radio', array('name' => 'ban.enable',
            'title' => __halotext('Enable restrictions feature'),
            'options' => array(HALOObject::getInstance(array('value' => 1, 'title' => __halotext('Yes'))),
                HALOObject::getInstance(array('value' => 0, 'title' => __halotext('No')))
            ),
            'value' => HALOConfig::get('ban.enable', 0))))
                ->addUI('ban.roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Who can restrict?'),
            'options' => $systemRoles,
            'value' => HALOConfig::get('ban.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

                //ban level 1
        $section = HALOUIBuilder::getInstance('banlevel1', 'config.section', array('title' => __halotext('Level 1 Restrictions')))
                    ->addUI('ban.level1_desc', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'ban.level1.description',
            'title' => __halotext('Description'),
            'value' => HALOConfig::get('ban.level1.description', __halotext('Restriction level 1')))))
                        ->addUI('ban.level1_permissions', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level1.permissions',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions permissions'),
            'options' => $permissions,
            'data' => array('empty' => 'true'),
            'value' => HALOConfig::get('ban.level1.permissions', ''))))
                            ->addUI('ban.level1_roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level1.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions roles'),
            'data' => array('empty' => 'true'),
            'options' => $roles,
            'value' => HALOConfig::get('ban.level1.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

                            //ban level 2
        $section = HALOUIBuilder::getInstance('banlevel2', 'config.section', array('title' => __halotext('Level 2 Restrictions')))
                                ->addUI('ban.level2_desc', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'ban.level2.description',
            'title' => __halotext('Description'),
            'value' => HALOConfig::get('ban.level2.description', __halotext('Restriction level 2')))))
                                    ->addUI('ban.level2_permissions', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level2.permissions',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions permissions'),
            'data' => array('empty' => 'true'),
            'options' => $permissions,
            'value' => HALOConfig::get('ban.level2.permissions', ''))))
                                        ->addUI('ban.level2_roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level2.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions roles'),
            'data' => array('empty' => 'true'),
            'options' => $roles,
            'value' => HALOConfig::get('ban.level2.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

                                        //ban level 3
        $section = HALOUIBuilder::getInstance('banlevel3', 'config.section', array('title' => __halotext('Level 3 Restrictions')))
                                            ->addUI('ban.level3_desc', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'ban.level3.description',
            'title' => __halotext('Description'),
            'value' => HALOConfig::get('ban.level3.description', __halotext('Restriction level 3')))))
                                                ->addUI('ban.level3_permissions', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level3.permissions',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions permissions'),
            'data' => array('empty' => 'true'),
            'options' => $permissions,
            'value' => HALOConfig::get('ban.level3.permissions', ''))))
                                                    ->addUI('ban.level3_roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level3.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions roles'),
            'data' => array('empty' => 'true'),
            'options' => $roles,
            'value' => HALOConfig::get('ban.level3.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

                                                    //ban level 4
        $section = HALOUIBuilder::getInstance('banlevel4', 'config.section', array('title' => __halotext('Level 4 Restrictions')))
                                                        ->addUI('ban.level4_desc', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'ban.level4.description',
            'title' => __halotext('Description'),
            'value' => HALOConfig::get('ban.level4.description', __halotext('Restriction level 4')))))
                                                            ->addUI('ban.level4_permissions', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level4.permissions',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions permissions'),
            'data' => array('empty' => 'true'),
            'options' => $permissions,
            'value' => HALOConfig::get('ban.level4.permissions', ''))))
                                                                ->addUI('ban.level4_roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level4.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions roles'),
            'data' => array('empty' => 'true'),
            'options' => $roles,
            'value' => HALOConfig::get('ban.level4.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

                                                                //ban level 5
        $section = HALOUIBuilder::getInstance('banlevel5', 'config.section', array('title' => __halotext('Level 5 Restrictions')))
                                                                    ->addUI('ban.level5_desc', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'ban.level5.description',
            'title' => __halotext('Description'),
            'value' => HALOConfig::get('ban.level5.description', __halotext('Restriction level 5')))))
                                                                        ->addUI('ban.level5_permissions', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level5.permissions',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions permissions'),
            'data' => array('empty' => 'true'),
            'options' => $permissions,
            'value' => HALOConfig::get('ban.level5.permissions', ''))))
                                                                            ->addUI('ban.level5_roles', HALOUIBuilder::getInstance('', 'form.multiple_select', array('name' => 'ban.level5.roles',
            'class' => 'selectpicker',
            'title' => __halotext('Restrictions roles'),
            'data' => array('empty' => 'true'),
            'options' => $roles,
            'value' => HALOConfig::get('ban.level5.roles', ''))));
        $banCfg->builder->addUI('section@array', $section);

        $settings->config['banlog'] = $banCfg;

    }

}
