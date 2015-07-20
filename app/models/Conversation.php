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

class HALOConversationModel extends HALOModel
{
    protected $table = 'halo_conversations';

    protected $fillable = array('attenders');

    private $validator = null;

    private $_params = null;

    /**
     * Get validate rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('attenders' => 'required');

    }

    //////////////////////////////////// Define Relationships /////////////////////////
  
    /**
     * HALOConverstaionModel, HALOMessagesModel: one to many
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function messages()
    {
        return $this->hasMany('HALOMessageModel', 'conv_id');
    }

    /**
     * Relationship: conv has one last message
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function lastMessage()
    {
        return $this->hasMany('HALOMessageModel', 'conv_id');
    }

    /**
     * HALOConverstaionModel, HALOConvattenderModel: one to many
     *  
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function convattenders()
    {
        return $this->hasMany('HALOConvattenderModel', 'conv_id');
    }
    
    /**
     * HALOConverstaionModel, HALOConvattenderModel: one to many with attender_id condition
     * 
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function detail()
    {
        $my = HALOUserModel::getUser();
        return $this->hasMany('HALOConvattenderModel', 'conv_id')->where('attender_id', '=', $my->id);
    }

    //////////////////////////////////// Define Relationships /////////////////////////


    /**
     * Return a list of activity by using input options
     * 
     * @param  array $options 
     * @return Illuminate\Database\Query\Builder          
     */
    public function getMessages($options)
    {
        $default = array('limit' => HALO_ACTIVITY_LIMIT_DISPLAY,
            'orderBy' => 'created_at',
            'orderDir' => 'desc',
            'after' => '',
            'before' => '',
            'newOnly' => false,
            'filters' => array()
        );
        $options = array_merge($default, $options);
        $query = HALOMessageModel::with('actor', 'likes')
            ->where('conv_id', '=', $this->id)
            ->orderBy($options['orderBy'], $options['orderDir']);
        if ($options['limit'] != 'all') {
            $query->take($options['limit']);
        }

        if ($options['newOnly']) {
            $lastseen_id = $this->detail->first()->lastseen_id;
            $query->where('id', '>', $lastseen_id);
        }

        if (!empty($options['before'])) {
            $query->where('id', '>', $options['before']);
        }

        if (!empty($options['after'])) {
            $query->where('id', '<', $options['after']);
        }

        //apply filters
        $query = HALOFilter::applyFilters($query, $options['filters']);

        //revert the result order
        $rtn = $query->get()->reverse();

        return $rtn;
    }

    /**
     *  Return a list of conversation by using input options
     *  
     * @param  array $options
     * @return object
     */
    public static function getConversations($options)
    {
        $default = array('limit' => HALO_ACTIVITY_LIMIT_DISPLAY,
            'attender_id' => '',
            'orderBy' => 'created_at',
            'orderDir' => 'desc',
            'latest_id' => -1,
            'after' => '',
            'before' => '',
            'since' => '',
            'newOnly' => false,
            'filters' => array()
        );
        $options = array_merge($default, $options);
        $query = HALOConvattenderModel::where('attender_id', '=', $options['attender_id'])
            ->where('latest_id', '>', $options['latest_id']);
        if ($options['limit'] != 'all') {
            $query->take($options['limit']);
        }

        if (!empty($options['before'])) {
            $query->where('id', '<', $options['before']);
        }

        if (!empty($options['after'])) {
            $query->where('id', '>', $options['after']);
        }

        if (!empty($options['since'])) {
            $query->where('updated_at', '>', $options['since']);
        }

        if ($options['newOnly']) {
            $query->whereRaw('latest_id > lastseen_id');
        }
        //apply filters
        $query = HALOFilter::applyFilters($query, $options['filters']);

        $conv_ids = $query->lists('conv_id');
        if (empty($conv_ids)) {
            return array();
        }

		$query = HALOConversationModel::whereIn('id', $conv_ids);

        if (!empty($options['orderBy']) && !empty($options['orderDir'])) {
            $query->orderBy($options['orderBy'], $options['orderDir']);
        }
        $convs = $query->get();
        $convs->load('detail');
        return $convs;
    }

    /**
     * Find a conversation matching attenders list
     * 
     * @param  array  $attenders 
     * @return object     
     */
    public static function findConv(array $attenders)
    {
        //sort attenders list
        asort($attenders);

        $attendersStr = implode(',', $attenders);
        $conv = HALOConversationModel::where('attenders', '=', $attendersStr)->get();
        if (count($conv)) {
            return $conv->first();
        } else {
            //create new $conv
            $conv = new HALOConversationModel();
            $conv->attenders = $attendersStr;
            $conv->save();
            //also add attenders to conv
            $attendersArr = array();
            foreach ($attenders as $att) {
                $obj = new HALOConvattenderModel();
                $obj->attender_id = $att;
                $obj->latest_id = 0;
                $conv->convattenders()->save($obj);
            }
        }
        return $conv;
    }

    /**
     * Return display name for this conversation
     * 
     * @return string
     */
    public function getDisplayName()
    {
        $name = $this->name;
        if (empty($name)) {
            //use attenders as display name if conv name is not set
            $attendersArr = explode(',', $this->attenders);
            $attenders = HALOUserModel::init($attendersArr);
            $my = HALOUserModel::getUser();

            //check if this is echo test
            if (count($attendersArr) == 2 && $attendersArr[0] == $my->id && $attendersArr[1] == $my->id) {
                $name = __halotext("Echo testing");
            } else {
                $arr = array();
                foreach ($attenders as $att) {
                    //do not include my name as the conv name
                    if ($att->id != $my->id) {
                        $arr[] = $att->getDisplayName();
                    }
                }
                $name = implode(',', $arr);
            }
        }
        return $name;
    }

    /**
     * Check if this converssation has unread messages
     * 
     * @return boolean 
     */
    public function hasUnreadMessage()
    {
        $latest_id = $this->detail->first()->latest_id;
        $lastseen_id = $this->detail->first()->lastseen_id;
        return (((int) $latest_id > (int) $lastseen_id));
    }

    /**
     * Check if a specific userID is an attender of this conversation
     * 
     * @param  string  $userId 
     * @return boolean         
     */
    public function isAttender($userId)
    {
        $attendersArr = explode(',', $this->attenders);
        return in_array($userId, $attendersArr);
    }

    /**
     * Get a list of attender Ids of this conversation
     * 
     * @param  string $excluded
     * @return array         
     */
    public function getAttenderIds($excluded = '')
    {
        $excludedArr = (array) $excluded;
        $attendersArr = explode(',', $this->attenders);
        return array_diff($attendersArr, $excludedArr);
    }

    /**
     * Check if a messages is unread
     *  
     * @param  string  $message 
     * @return boolean          
     */
    public function isUnreadMessage($message)
    {
        $my = HALOUserModel::getUser();
        $lastseen_id = $this->detail->first()->lastseen_id;
        return ($my->id != $message->actor_id) && (int) $lastseen_id < (int) $message->id;
    }

    /**
     * Return the onclick action for notification on conversation
     * 
     * @return bool
     */
    public function getNotificationTargetAction()
    {
        //open the single view mode for this activity
        HALOResponse::addScriptCall('halo.message.openConvByConvId', $this->id);
        return true;
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
        return '<a ' . $class . 'href="' . $this->getUrl() . '">' . __halotext('conversation') . '</a>';
    }

    /**
     * Get url
     * 
     * @return string
     */
    public function getUrl()
    {
        return URL::to('?view=message&task=show&uid=' . $this->id);
    }

    /**
     * Return last messages for this conversation
     * 
     * @return string
     */
    public function getLastMessage()
    {
        $message = $this->lastMessage->last();

        return $message ? $message->getMessage() : '';
    }

}
