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
 
class HALOGATracking
{
    public static $hits = array();
    public static $haloPageGroup = '';

    /**
     * add hit to queue
     * 
     * @param string $handler
     * @param array $params
     */
    public static function addHit($handler, $params)
    {
        $hit = new stdClass();
        //automatically append pageGroup and registered Dimension for pageview event
        if ($handler == 'pageview') {
            $pageGroupDimensionIdx = HALOConfig::get('global.GAPageGroupDimension');
            $resigteredDimensionIdx = HALOConfig::get('global.GARegsiteredUserDimension');
            $my = HALOUserModel::getUser();

            if ($pageGroupDimensionIdx) {
                $params['dimension' . $pageGroupDimensionIdx] = self::$haloPageGroup;
            }

            if ($resigteredDimensionIdx) {
                $params['dimension' . $resigteredDimensionIdx] = $my ? 'Registered User' : 'Public User';
            }
        }

        $hit->handler = $handler;
        $hit->params = $params;
        self::$hits[] = $hit;
    }
    /**
     * get Tracking Html
     * 
     * @return string
     */
    public static function getTrackingHtml()
    {

        $html = '';
        if (HALOConfig::get('global.GAEnable', 0)) {
            //by default, tracking the page view
            //GA actions
            $params = array();
            HALOGATracking::addHit('pageview', $params);

            $options = new stdClass();
            $config = new stdClass();

            $config->trackingId = HALOConfig::get('global.GATrackingId');
            $my = HALOUserModel::getUser();
            if ($my) {
                $config->userId = 'halo_' . $my->id;
            }

            $options->config = $config;
            $options->hits = self::$hits;
            $html = '<div id="halo_gatracking" data-gadebug="' . HALOConfig::get('global.GAEnableDebug', 0) . '" data-ga=\'' . json_encode($options) . '\'></div>';
        }
        return $html;
    }

}
