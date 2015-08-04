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

class HALOPrivacy
{

    /**
     * apply filter on input query to enforcement privacy setting
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @param  mixed $value 
     * @param  mixed $params 
     * @return  void    
     */
    public static function privacyStreamApplyFilter(&$query, $value, $params)
    {
        //do not apply privacy for admin
        if (HALOAuth::can('backend.view')) {
            return;
        }

        $query = $query->where(function ($query) use ($value, $params) {
            $my = HALOUserModel::getUser();
            //public privacy
            $query->where('access', '=', HALO_PRIVACY_PUBLIC);
            if ($my) {
                //site member privacy
                $query->orWhere(function ($query) {
                    $my = HALOUserModel::getUser();
                    $query->where('access', '=', HALO_PRIVACY_MEMBER);
                });
                //friends& followers privacy
                $query->orWhere(function ($query) {
                    $my = HALOUserModel::getUser();
                    $followerIds = $my->getFollowingIds();
                    $firendIds = $my->getFriendIds();
                    $followerIds = array_merge($followerIds, $firendIds);

                    if (empty($followerIds)) {

                        $followerIds = array();
                    }
                    //add my to the follower list
                    $followerIds[] = $my->id;
                    $query->where('access', '=', HALO_PRIVACY_FOLLOWER);
                    $query->whereIn('actor_id', $followerIds);
                });
                //only me
                $query->orWhere(function ($query) {
                    $my = HALOUserModel::getUser();
                    $query->where('access', '=', HALO_PRIVACY_ONLYME);
                    $query->where('actor_id', '=', $my->id);
                });
            }
            //trigger event to load additional privacy filter
            Event::fire('system.onApplyStreamPrivacy', array(&$query, $value, $params));
        });
    }

    /**
     * displayfilter to display list of activity actor
     * 
     * @param   $params
     * @param  string $uiType
     * @return array
     */
    public static function getUserActivitiesDisplayFilter($params, $uiType)
    {
        $options = array(HALOObject::getInstance(array('name' => __halotext('All Users'), 'value' => 'all'))
            , HALOObject::getInstance(array('name' => __halotext('Friends'), 'value' => 'friend'))
            , HALOObject::getInstance(array('name' => __halotext('Followers'), 'value' => 'follower'))
            , HALOObject::getInstance(array('name' => __halotext('Me'), 'value' => 'meonly')));
        //$uiType = 'filter.multiple_select';//user filter_tree_radio UI for this filter
		$params->set('uiType', 'filter.multiple_select');//user filter_tree_radio UI for this filter
        return $options;
    }

    /**
     * apply filter on input query to enforcement user's activities
     * 
     * @param  Illuminate\Database\Query\Builder $query 
     * @param  mixed $value
     * @param  mixed $params
     * @return mixed
     */
    public static function getUserActivitiesApplyFilter(&$query, $value, $params)
    {
        if (is_string($value)) {
            $values = explode(',', $value);
        } else if (is_array($value)) {
            $values = $value;
        }
        if (empty($values)) {
            //nothing to apply on the value
            return true;
        }

        $ids = array();
        $my = HALOUserModel::getUser();
        if ($my) {
            foreach ($values as $val) {
                if ($val == 'all') {
                    //all users , do not apply any where condition
                    return true;
                }
                if ($val == 'friend') {
                    //friends
                    $friendIds = $my->getFriendIds();
                    $ids = array_merge($ids, $friendIds, array(-1));//tricky add -1 as friendId to make sure that the friendIds array is not empty
                }
                if ($val == 'follower') {
                    //followers
                    $followerIds = $my->getFollowerIds();
                    $ids = array_merge($ids, $followerIds, array(-1));//tricky add -1 as followerId to make sure that the followerIds array is not empty
                }
                if ($val == 'meonly') {
                    //me
                    $ids = array_merge($ids, array($my->id));
                }
            }
        }
        try {
            if (!empty($ids)) {
                $query = $query->where(function ($query) use ($ids) {
                    $query->whereIn('actor_id', $ids)
                        ->whereOr(function ($query) use ($ids) {
                        $query->where('context', 'profile')
                            ->whereIn('target_id', $ids);
                    });
                });
            }
            return true;

        } catch (\Exception $e) {
            return false;
        }

    }

    
    /**
     * return a list of privacy options
     * 
     * @return array
     */
    public static function getPrivacyOptions()
    {
        $options = array(HALO_PRIVACY_PUBLIC => HALOObject::getInstance(array('name' => __halotext('Public'), 'value' => HALO_PRIVACY_PUBLIC, 'icon' => 'globe')),
        	HALO_PRIVACY_MEMBER => HALOObject::getInstance(array('name' => __halotext('Members'), 'value' => HALO_PRIVACY_MEMBER, 'icon' => 'users')),
            HALO_PRIVACY_FOLLOWER => HALOObject::getInstance(array('name' => __halotext('Followers'), 'value' => HALO_PRIVACY_FOLLOWER, 'icon' => 'hand-o-right')),
            HALO_PRIVACY_ONLYME => HALOObject::getInstance(array('name' => __halotext('Only me'), 'value' => HALO_PRIVACY_ONLYME, 'icon' => 'lock')),
        );
        return $options;
    }

}
