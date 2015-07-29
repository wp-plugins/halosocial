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

class HALOToolbar
{
    public static $_toolbar = array();
    /**
     * Construction function
     * 
     * @return    string    The photo path with size postfix
     * @param    string    $paramStr    the param in string format
     * @param    string    $type        format type
     * @return   stdClass
     */
    public static function addToolbar($title, $class = '', $href = '', $onClick = '', $icon = '', $target = '')
    {
        $toolbar = new stdClass();
        $toolbar->title = $title;
        $toolbar->class = $class;
        $toolbar->href = $href;
        $toolbar->onClick = $onClick;
        $toolbar->icon = $icon;
        $toolbar->target = $target;

        self::$_toolbar[] = $toolbar;

    }

    /**
     * render 
     * 
     * @return HALOUIBuilder
     */
    public static function render()
    {
        $builder = HALOUIBuilder::getInstance('', 'group_button', array('class' => 'halo-pull-right'));
        foreach (self::$_toolbar as $toolbar) {
            $builder->addUI('button@array', HALOUIBuilder::getInstance('', '', array('title' => $toolbar->title,
                'class' => 'halo-btn ' . $toolbar->class,
                'onClick' => $toolbar->onClick,
                'icon' => $toolbar->icon,
                'url' => $toolbar->href,
                'target' => $toolbar->target,

            )
            ));
        }
        return $builder->fetch();

    }

}
