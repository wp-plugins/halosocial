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

class StreamController extends BaseController
{

    /**
     * Initializer.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a single activity
     *
     * @param  int $actId
     * @return View
     */
    public function getShow($actId)
    {
		return $this->getActivity($actId);
	}
    /**
     * Show a single activity
     *
     * @param  int $actId
     * @return View
     */
    public function getActivity($actId)
    {
        $user = HALOUserModel::getUser();

        //get filters
        $streamFilters = HALOFilter::getFilterByName('activity.single.*');
        //configure filters value
        foreach ($streamFilters as $filter) {
            //for other filter, get from http request or session data
            $filter->value = $actId;
        }

        //stream content
        $acts = HALOActivityModel::getActivities(array('filters' => $streamFilters, 'actid' => array($actId)));
        $showLoadMore = false;
        return View::make('site/stream/index', compact('user', 'acts', 'streamFilters', 'showLoadMore', 'actId'));
    }

    /**
     * ajax handle to load older activitie
     *
     * @param  int $lastId
     * @param  array $filterForm
     * @return JSON
     */
    public function ajaxLoadOlder($lastId, $filterForm)
    {
        //apply filter to query activities
        $filtersArr = isset($filterForm['filters']) ? $filterForm['filters'] : array();

        //load filters model
        $filters = HALOFilter::getFilterByIds(array_keys($filtersArr));
        //update filters values
        foreach ($filters as $filter) {
            $filter->value = isset($filtersArr[$filter->id]) ? $filtersArr[$filter->id] : '';
        }
        //stream content
        $acts = HALOActivityModel::getActivities(array('after' => $lastId, 'filters' => $filters));
        if (count($acts)) {
            $html = '';

            foreach ($acts as $act) {
                $html .= $act->render();
            }

            //update zone
            HALOResponse::insertZone('stream_content', $html);
        } else {
            //no older activity, remove the load more button
            HALOResponse::addScriptCall("halo.template.removeStreamLoadMore");
        }

        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to refresh stream with new filter update
     *
     * @param  array $filterForm
     * @return JSON
     */
    public function ajaxRefresh($filterForm)
    {
        //apply filter to query activities
        $filtersArr = isset($filterForm['filters']) ? $filterForm['filters'] : array();

        //load filters model
        $filters = HALOFilter::getFilterByIds(array_keys($filtersArr));
        //update filters values
        foreach ($filters as $filter) {
            $filter->value = isset($filtersArr[$filter->id]) ? $filtersArr[$filter->id] : '';
        }
        //stream content
        $acts = HALOActivityModel::getActivities(array('filters' => $filters));

        //update zone
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'stream.content', array('acts' => $acts, 'zone' => 'stream_content'))->fetch());

        return HALOResponse::sendResponse();

    }

    /**
     * callback for stream updater
     *
     * @param array $filterForm
     * @return bool
     */

    public function ContentUpdater($filterForm)
    {

        //apply filter to query activities
        $filtersArr = isset($filterForm['filters']) ? $filterForm['filters'] : array(0);
        $latestId = isset($filterForm['latestId']) ? $filterForm['latestId'] : 0;
        $actIds = isset($filterForm['actIds']) ? $filterForm['actIds'] : array();

        //check input data
        if (!$latestId) {
            return false;
        }

        //load filters model
        $filters = HALOFilter::getFilterByIds(array_keys($filtersArr));
        //update filters values
        foreach ($filters as $filter) {
            $filter->value = isset($filtersArr[$filter->id]) ? $filtersArr[$filter->id] : '';
        }
        //new stream content
        $acts = HALOActivityModel::getActivities(array('before' => $latestId, 'filters' => $filters));

        //only update if there are new $acts
        if (count($acts)) {
            $html = '';

            foreach ($acts as $act) {
                $html .= $act->render();
            }

            //update zone
            HALOResponse::addScriptCall('halo.activity.insertActivity', $html);
            //update zone
        }
        //updated stream content
        if (!empty($actIds)) {
            $acts = HALOActivityModel::getActivities(array('actid' => $actIds, 'filters' => $filters, 'updatedOnly' => true));
            if (count($acts)) {
                foreach ($acts as $act) {
                    HALOResponse::addScriptCall('halo.activity.updateActivity', $act->render());
                }

            }

        }

        Session::set('halo_stream_timestamps', Carbon::now()->subSeconds(5));//give some delay for timestamps update
        return true;

    }

}
