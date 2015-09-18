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

class HALOPaginationPresenterAjax extends HALOPaginationPresenter
{
    /**
     * Get the previous page pagination element.
     *
     * @param  string  $text
     * @return string
     */
    public function getPrevious($text = '&laquo;')
    {
        // If the current page is less than or equal to one, it means we can't go any
        // further back in the pages, so we will render a disabled previous button
        // when that is the case. Otherwise, we will give it an active "status".
        if ($this->currentPage <= 1) {
            return '<li class="disabled"><span>' . $text . '</span></li>';
        } else {
            $url = $this->getUrl($this->currentPage - 1);

            //return '<li><a href="'.$url.'">'.$text.'</a></li>';
            //return '<li><a href="javascript:void(0)" class="halo-pagination-link" data-pagination-page data-pagination-index="'.($this->currentPage - 1).'">'.$text.'</a></li>';
            return '<li><a href="' . $url . '" class="halo-pagination-link" data-pagination-page data-pagination-index="' . ($this->currentPage - 1) . '">' . $text . '</a></li>';
        }
    }

    /**
     * Get the next page pagination element.
     *
     * @param  string  $text
     * @return string
     */
    public function getNext($text = '&raquo;')
    {
        // If the current page is greater than or equal to the last page, it means we
        // can't go any further into the pages, as we're already on this last page
        // that is available, so we will make it the "next" link style disabled.
        if ($this->currentPage >= $this->lastPage) {
            return '<li class="disabled"><span>' . $text . '</span></li>';
        } else {
            $url = $this->getUrl($this->currentPage + 1);

            //return '<li><a href="'.$url.'">'.$text.'</a></li>';
            //return '<li><a href="javascript:void(0)" class="halo-pagination-link" data-pagination-page data-pagination-index="'.($this->currentPage + 1).'">'.$text.'</a></li>';
            return '<li><a href="' . $url . '" class="halo-pagination-link" data-pagination-page data-pagination-index="' . ($this->currentPage + 1) . '">' . $text . '</a></li>';
        }
    }

    /**
     * Create a pagination slider link.
     *
     * @param  mixed   $page
     * @return string
     */
    public function getLink($page)
    {
        $url = $this->getUrl($page);
        //return '<li><a href="'.$url.'">'.$page.'</a></li>';
        //return '<li><a href="javascript:void(0)" class="halo-pagination-link" data-pagination-page data-pagination-index="'.$page.'">'.$page.'</a></li>';
        return '<li><a href="' . $url . '" class="halo-pagination-link" data-pagination-page data-pagination-index="' . $page . '">' . $page . '</a></li>';
    }

    /**
     * return url for a specific page
     * 
     * @param  mixed $page
     * @return string
     */
    public function getUrl($page)
    {

        //for ajax request
        $refer_url = Input::get('__refer_url', null);
        if (!is_null($refer_url)) {
            $url = $refer_url;
            $url = $this->addURLParameter($url, $this->paginator->getEnvironment()->getPageName(), $page);
        } else {
            $usec = Input::get('usec', null);
            if (!is_null($usec)) {
                $this->paginator->addQuery('usec', $usec);
            }
            $url = $this->paginator->getUrl($page);
        }

        return $url;
    }

    /**
     * update param of a url
     * 
     * @param string $url
     * @param mixed $paramName
     * @param mixed $paramValue
     * @return  string
     */
    public function addURLParameter($url, $paramName, $paramValue)
    {
        $url_data = parse_url($url);
        if (!isset($url_data["query"])) {
            $url_data["query"] = "";
        }

        $params = array();
        parse_str($url_data['query'], $params);
        $params[$paramName] = $paramValue;

        $url_data['query'] = http_build_query($params);
        return $this->build_url($url_data);
    }

    /**
     * build url from string
     * 
     * @param  array $url_data 
     * @return string
     */
    public function build_url($url_data)
    {
        $url = "";
        if (isset($url_data['host'])) {
            $url .= $url_data['scheme'] . '://';
            if (isset($url_data['user'])) {
                $url .= $url_data['user'];
                if (isset($url_data['pass'])) {
                    $url .= ':' . $url_data['pass'];
                }
                $url .= '@';
            }
            $url .= $url_data['host'];
            if (isset($url_data['port'])) {
                $url .= ':' . $url_data['port'];
            }
        }
        $url .= $url_data['path'];
        if (isset($url_data['query'])) {
            $url .= '?' . $url_data['query'];
        }
        if (isset($url_data['fragment'])) {
            $url .= '#' . $url_data['fragment'];
        }
        return $url;
    }

}
