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

class HALOReportModel extends HALOModel
{
    protected $table = 'halo_reports';

    protected $fillable = array('message', 'type');

    private $validator = null;

    private $_params = null;

    /**
     * Gwt validate rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('actor_id' => 'required', 'owner_id' => 'required', 'type' => 'required', 'message' => 'required');

    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * HALOUserModel, HALOMessageModel: one to one (actor)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function actor()
    {
        return $this->belongsTo('HALOUserModel', 'actor_id');
    }

    /**
     * HALOUserModel, HALOMessageModel: one to one (actor)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owner()
    {
        return $this->belongsTo('HALOUserModel', 'owner_id');
    }

    //define polymorphic relationship
    public function reportable()
    {
        return $this->morphTo();
    }
    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Get Message 
     * 
     * @return HALOOutputHelper
     */
    public function getMessage()
    {
        $message = $this->message;
        return HALOOutputHelper::text2html($message);

    }

    /**
     * Get Display Link 
     * 
     * @param  string $class 
     * @return string     
     */
    public function getDisplayLink($class = '')
    {
		if($this->reportable){
			return $this->reportable->getDisplayLink($class);
		} else {
			return __halotext('Unknown');
		}
    }

    /**
     * Get Notification Content 
     * 
     * @param  string $class 
     * @return string        
     */
    public function getNotifContent($class = '')
    {
        $message = $this->message;
        return $this->getDisplayLink($class) . ' "' . HALOOutputHelper::ellipsis(HALOOutputHelper::striptags($message, array('p'))) . '"';
    }

    /**
     * getNotificationTargetAction ajax response a target url
     * 
     * @return bool
     */
    public function getNotificationTargetAction()
    {
        HALOResponse::redirect(URL::to('?app=admin&view=reports'));
        return true;
    }

}
