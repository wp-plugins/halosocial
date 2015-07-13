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

class BrowseController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Returns all the blog posts.
     *
     * @return View
     */
    public function getIndex()
    {
        $user = HALOUserModel::getUser();
        //get filters
        $filters = $this->getBrowseFilter(array());

        $this->ajaxDisplaySection(array());

        $actions = HALOUIBuilder::getInstance('', 'usection_action', array());
        // if (HALOAuth::can('post.create')){
        //     $actions->addUI('post_actions', HALOUIBuilder::getInstance('', 'content', array('title' => __halotext('Create Post'), 'tooltip'=>__halotext('Create Post'), 'onClick' => "halo.post.editPostForm()", 'icon'=>'puls', 'class'=> 'halo-btn halo-btn-primary halo-btn-lg halo-create-btn halo-pull-left')));
        // }
        HALOOutputHelper::addPostViewModes($actions);

        $rtn = View::make('site/browse/index', compact('user', 'filters', 'actions'));
        return $rtn;
    }

    /**
     * return filter settings for the current postData
     * @param  string $postData
     * @return $listingFilters
     */
    private function getBrowseFilter($postData)
    {
        $postData['limit'] = 24;
        Input::merge($postData);
        //setup filters
        $listingFilters = HALOFilter::getFilterByName('browse.listing.*');
        $filterValues = Input::all('filters');
        $filterValues = isset($filterValues['filters']) ? $filterValues['filters'] : array();
        foreach ($listingFilters as $filter) {
            //setup default filter for location
            if ($filter->name == 'browse.listing.location' && !isset(Input::all()['filters'][$filter->id])) {
                /*
            $defaultLocation = HALOBrowseHelper::getDefaultLocation();
            $lat = $defaultLocation->getLat();
            $lng = $defaultLocation->getLng();
            $distance = 5;
            $data = array('lat'=>$lat,'lng'=>$lng,'name'=>'','distance'=>$distance);

            Input::merge(array('filters'=>array($filter->id=>$data)));
             */
            }
            //setup default sort
            if ($filter->name == 'browse.listing.sort' && !isset(Input::all()['filters'][$filter->id])) {
                //$data = array('sort'=>'date');
                $data = array('sort' => 'default');
                $filterValues[$filter->id] = $data;
            }
        }
        if (!empty($filterValues)) {
            Input::merge(array('filters' => $filterValues));
        }

        return $listingFilters;
    }

    /**
     * ajax handler to list this site's group
     * @param  string $postData
     * @return JSON
     */
    public function ajaxBrowsePosts($postData)
    {

        $postData['limit'] = 24;
        $postData['com'] = 'browse';
        $postData['func'] = 'DisplaySection';
        Input::merge($postData);

        $query = new HALOPostModel();

        //apply moderate
        $query = $query->moderateFilter();

        //post must have location to be browsed
        //$query = $query->has('location');

        $posts = HALOPagination::getData($query, 'halo_posts.id');
        $totalPost = $posts->getTotal();
        $postListHtml = HALOUIBuilder::getInstance('', 'post.list', array(
            'layout' => 'browse',
            'posts' => HALOUtilHelper::paginatorLoad($posts, array('category', 'location', 'labels')),
            'zone' => 'halo-browse-wrapper'
        ))->fetch();

        HALOResponse::setZoneContent('halo-browse-wrapper', $postListHtml, HALO_CONTENT_UPDATE_MODE);

        //pagination
        $paginationHtml = $posts->links('ui.pagination_ajax')->__toString();

        HALOResponse::setZonePagination('halo-browse-wrapper', $paginationHtml);
        if ($totalPost > 0) {
            HALOResponse::addZoneScript('halo-browse-wrapper', 'halo.browse.updateResultCounter', sprintf(__halotext('About %d results.'), $totalPost));
        } else {
            HALOResponse::addZoneScript('halo-browse-wrapper', 'halo.browse.updateResultCounter', '');

        }

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to display post browsing
     *
     * @param  string $postData
     * @return JSON
     */
    public function ajaxDisplaySection($postData)
    {
        return $this->ajaxBrowsePosts($postData);
    }
}
