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

class HALOMessageModel extends HALOModel
{
    protected $table = 'halo_messages';

    protected $fillable = array('message');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('actor_id' => 'required', 'conv_id' => 'required', 'message' => 'required');

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
     * HALOConversationModel, HALOMessageModel: one to one (actor)
     * 
     * @return Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function conversation()
    {
        return $this->belongsTo('HALOConversationModel', 'conv_id');
    }

    /**
     * HALOActivityModel, HALOLikeModel: polymorphic
     * 
     * @return Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function likes()
    {
        return $this->morphMany('HALOLikeModel', 'likeable');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Override the save message to update all related fields in conversation table
     * 
     * @param  array  $options
     * @return array          
     */
    public function save(array $options = array())
    {
        parent::save($options);
        $my = HALOUserModel::getUser();
        $conv = $this->conversation()->get()->first();
        if ($conv) {
            $conv->load('convattenders');
            foreach ($conv->convattenders as $attender) {
                $attender->latest_id = $this->id;
                //update lastseen_id for the actor's conversation
                if ($attender->attender_id == $my->id) {
                    $attender->lastseen_id = $this->id;
                }
            }
            $conv->push();
        }
    }

    /**
     * Return messages in html format
     * 
     * @return string
     */
    public function getMessage()
    {
		if(class_exists('HALOPostModel')) {
			//process message hashtag
			$message = HALOHashTagAPI::renderMessage($this->message, new HALOPostModel());
		} else {
			$message = $this->message;
		}

        $message = HALOUtilHelper::renderMessage($message);

        return $message;
    }

    /**
     * Return display name with link for this model
     * 
     * @param  string $class 
     * @return string       
     */
    public function getDisplayLink($class = '')
    {

        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Get Notification Content
     * 
     * @param  string $class 
     * @return string        
     */
    public function getNotifContent($class = '')
    {
        $message = $this->getMessage();
        return '"' . HALOOutputHelper::ellipsis(HALOOutputHelper::striptags($message, array('p'))) . '"';
    }

    /**
     * Return display name without link for this model
     * 
     * @return string 
     */
    public function getDisplayName()
    {

        return __halotext('message');
    }

    /**
     * Return view url
     * 
     * @return string
     */
    public function getUrl()
    {
        return URL::to('?view=conv&task=show&uid=' . $this->conv_id);
    }

}
