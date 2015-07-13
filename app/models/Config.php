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

class HALOConfigModel extends HALOModel
{
    protected $table = 'halo_config';

    protected $hidden = array('region_geom');

    private $_params = null;

    /**
     * init Param
     * 
     * @return HALOConfigModel
     */
    public function initParam()
    {
        if (!isset($this->param_value) || empty($this->param_value)) {
            $this->param_value = '{}';
        }
        $this->_params = new HALOParams($this->param_value);
        return $this;
    }

    /**
     * Get param
     * 
     * @param  string $key     
     * @param  string  $default 
     * @return object      
     */
    public function getParam($key, $default = null)
    {
        return $this->_params->get($key, $default);
    }

    /**
     * Set Param
     * 
     * @param string $key   
     * @param string $value 
     * @return HALOConfigModel
     */
    public function setParam($key, $value)
    {
        $this->_params->set($key, $value);
        return $this;
    }

    /**
     * Prepare save 
     * 
     * @return string
     */
    public function prepareSave()
    {
        //before saving, need to convert HALOParams to param string
        $this->param_value = $this->_params->toString();
    }
	
	/*
		return site short info
	*/
	public static function getSiteShortInfo(){
		static $site = null;
		if(!$site) {
			$site = new HALOConfigModel();
			Event::fire('system.onLoadShortInfo',array(&$site));
		}
		
		return $site->getShortInfo();
	}
}
