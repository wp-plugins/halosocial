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

class HALOPaginationPresenterMore extends HALOPaginationPresenter
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
            return '';
        } else {
            return '<li><a href="javascript:void(0)" class="halo-pagination-link halo-pagination-more" data-pagination-page data-pagination-index="' . ($this->currentPage - 1) . '">' . $text . '</a></li>';
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
            return '';
        } else {
            return '<li><a href="javascript:void(0)" class="halo-pagination-link halo-pagination-more" data-pagination-page data-pagination-index="' . ($this->currentPage + 1) . '">' . $text . '</a></li>';
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
        return '<li><a href="javascript:void(0)" class="halo-pagination-link halo-pagination-more" data-pagination-page data-pagination-index="' . $page . '">' . $page . '</a></li>';
    }

}
