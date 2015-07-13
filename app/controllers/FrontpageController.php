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

class HALOFrontpageController extends BaseController
{

    /**
     * Inject the models.
     * @param HALOProfileModel $profile
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a list of all the profiles.
     *
     * @return View
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Frontpage');
        // Toolbar
        HALOToolbar::addToolbar('Add Profile', '', '', 'halo.profile.showEditProfileForm(0)', 'plus');
        HALOToolbar::addToolbar('Delete Profile', '', '', "halo.popup.confirmDialog('Delete Profile Confirm', 'Are you sure to delete this profile', 'halo.profile.deleteSelectedProfile()')", 'times'); 

        // Grab all the profiles
        $profiles = new HALOProfileModel();
        $profiles = HALOPagination::getData($profiles);

        // Show the page
        return View::make('admin/profiles/index', compact('profiles', 'title'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $profileId
     * @return View
     */
    public function getProfileFieldIndex($profileId)
    {
        // Grab the profile data
        $profile = HALOProfileModel::find($profileId);
        if (is_null($profile)) {
            //invalide profile ID
            return null;
        }
        $fields = HALOPagination::getData($profile->getFields());

        // Title
        $title = __halotext('general.profile_management');

        // Toolbar
        HALOToolbar::addToolbar('Back', '', 'admin/profiles', '', 'undo');
        HALOToolbar::addToolbar('Attach Field', '', '', 'halo.profile.showAttachFieldForm(' . $profileId . ', 0)', 'plus');
        HALOToolbar::addToolbar('Detach Field', '', '', "halo.popup.confirmDialog('Detach Field Confirm', 'Are you sure to detach these fields', 'halo.profile.detachSelectedField(" . $profileId . ")')", 'times'); 

        // Show the page
        return View::make('admin/fields/indexProfile', compact('profile', 'fields', 'title'));

    }

}
