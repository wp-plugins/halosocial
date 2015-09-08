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

class AdminReportsController extends AdminController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a list of all the fields.
     *
     * @return View
     */
    public function getIndex()
    {
        // Title
        $title = __halotext('Report Management');

		// Toolbar
		HALOToolbar::addToolbar('Delete Report','halo-btn-danger','',"halo.popup.confirmDelete('Delete Report Confirm','Are you sure to delete this report?','halo.report.deleteSelectedReport()')",'times');
		
        // Grab all the fields
        $reports = new HALOReportModel();
		$reports = HALOPagination::getData($reports);

        // Show the page
        return View::make('admin/reports/index', compact('reports', 'title'));
    }
	
	/*
		ajax handler to delete reports
	*/
	public function ajaxDeleteReport($reportIds){
		$reportIds = (array) $reportIds;
		//loop on each field to delete
		
		HALOReportModel::destroy($reportIds);
		$message = (count($reportIds)>1)?__halotext('Reports were deleted'):__halotext('Report was deleted');
		HALOResponse::addScriptCall('halo.popup.setMessage', $message  , 'warning', true);
		HALOResponse::addScriptCall('halo.popup.resetFormAction');
		HALOResponse::addScriptCall('halo.popup.addFormAction', '{"name": "Done","onclick": "halo.util.reload()","href": "javascript:void(0);"}');
				
		return HALOResponse::sendResponse();
		
	}
		
}
