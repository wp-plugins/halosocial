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
 
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    /**
     * Prepare permissions for display 
     * 
     * @param  array $permissions
     * @return array             
     */
    public function preparePermissionsForDisplay($permissions)
    {
        // Get all the available permissions
        $availablePermissions = $this->all()->toArray();

        foreach ($permissions as &$permission) {
            array_walk($availablePermissions, function (&$value) use (&$permission) {
                if ($permission->name == $value['name']) {
                    $value['checked'] = true;
                }
            });
        }
        return $availablePermissions;
    }

    /**
     * Convert from input array to savable array.
     * 
     * @param  array  $permissions
     * @return array
     */
    public function preparePermissionsForSave($permissions)
    {
        $availablePermissions = $this->all()->toArray();
        $preparedPermissions = array();
        foreach ($permissions as $permission => $value) {
            // If checkbox is selected
            if ($value == '1') {
                // If permission exists
                array_walk($availablePermissions, function (&$value) use ($permission, &$preparedPermissions) {
                    if ($permission == (int) $value['id']) {
                        $preparedPermissions[] = $permission;
                    }
                });
            }
        }
        return $preparedPermissions;
    }
}
