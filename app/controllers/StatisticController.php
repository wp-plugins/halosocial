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

class StatisticController extends BaseController
{

    /**
     * Get users Statistic
     *
     * @return View
     */
    public function getUsersStat()
    {
        //init relationships
        //today approved post
        HALOUserModel::setResourceCb('todayAprrovedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->where('halo_posts.published', 1);
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfDay(), Carbon::now()->endOfDay()));
            });
            return $query;
        });
        //today created post
        HALOUserModel::setResourceCb('todayCreatedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfDay(), Carbon::now()->endOfDay()));
            });
            return $query;
        });

        //yesterday approved post
        HALOUserModel::setResourceCb('yesterdayAprrovedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->where('halo_posts.published', 1);
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->yesterday()->startOfDay(), Carbon::now()->yesterday()->endOfDay()));
            });
            return $query;
        });
        //yesterday created post
        HALOUserModel::setResourceCb('yesterdayCreatedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->yesterday()->startOfDay(), Carbon::now()->yesterday()->endOfDay()));
            });
            return $query;
        });

        //this month approved post
        HALOUserModel::setResourceCb('thisMonthAprrovedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->where('halo_posts.published', 1);
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()));
            });
            return $query;
        });
        //this month created post
        HALOUserModel::setResourceCb('thisMonthCreatedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()));
            });
            return $query;
        });

        //last month approved post
        HALOUserModel::setResourceCb('lastMonthAprrovedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->where('halo_posts.published', 1);
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfMonth()->subMonth()->startOfMonth(), 
																	Carbon::now()->startOfMonth()->subMonth()->endOfMonth()));
            });
            return $query;
        });
        //last month created post
        HALOUserModel::setResourceCb('lastMonthCreatedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->whereBetween('halo_posts.created_at', array(Carbon::now()->startOfMonth()->subMonth()->startOfMonth(), 
																	Carbon::now()->startOfMonth()->subMonth()->endOfMonth()));
            });
            return $query;
        });

        //total approved post
        HALOUserModel::setResourceCb('totalAprrovedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            $query->where(function ($query) {
                $query->where('halo_posts.published', 1);
            });
            return $query;
        });
        //total created post
        HALOUserModel::setResourceCb('totalCreatedPosts', function ($user) {
            $query = $user->hasMany('HALOPostModel', 'creator_id');
            return $query;
        });

        $userIds = Input::get('userids', '');
        if ($userIds) {
            $userIds = explode(',', trim($userIds));
        }
        if (!empty($userIds)) {
            $query = HALOUserModel::whereIn('id', $userIds);
        } else {
            $query = new HALOUserModel();
        }
        $stat = HALOPagination::getData($query);
        $title = __halotext('User statistic');

        $dateRangeFilter = HALOFilter::getFilterByName('post.listing.daterange')->first();

        return View::make('site/statistic/user', compact('stat', 'title', 'dateRangeFilter'));
    }

}
