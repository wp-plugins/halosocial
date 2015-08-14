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

class HALOProfilefieldModel extends HALOModel
{
    protected $table = 'halo_profiles_fields';

    protected $fillable = array();

    protected $toggleable = array('published', 'required');

    private $validator = null;

    private $_params = null;

    /**
     * Return toggleable states of a field
     * 
     * @param  string $field 
     * @return array        
     */
    public function getStates($field)
    {
        if ($field == 'published') {
            return array(0 => array('title' => __halotext('Unpublished'),
                'icon' => 'times-circle text-danger'),
                1 => array('title' => __halotext('Published'),
                    'icon' => 'check-circle text-success')

            );
        } else if ($field == 'required') {
            return array(0 => array('title' => __halotext('Not Required'),
                'icon' => 'times-circle text-danger'),
                1 => array('title' => __halotext('Required'),
                    'icon' => 'check-circle text-success')

            );
        }
    }

}
