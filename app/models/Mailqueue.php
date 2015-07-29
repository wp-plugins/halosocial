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

class HALOMailqueueModel extends HALOModel
{
	public $timestamps = false;

    protected $table = 'halo_mailqueue';

    protected $fillable = array('to', 'subject', 'plain_msg', 'html_msg', 'status', 'template', 'source_str', 'scheduled');

    private $validator = null;

    private $_params = null;
    
    /**
     * Get Validate Rule 
     * 
     * @return array
     */
    public function getValidateRule()
    {
        return array('to' => 'required|email', 'subject' => 'required', 'plain_msg' => 'required', 'html_msg' => 'required', 'template' => 'required');

    }

    /**
     * Send this mail queue email
     * 
     * @return bool
     */
    public function send()
    {
        $mailqueue = $this;
        try {
            Mail::send(array('emails/layout_html', 'emails/layout_text'), array('plain_msg' => $this->plain_msg,
                'html_msg' => $this->html_msg,
                'params' => $this->params)
                , function ($message) use ($mailqueue) {
                    $message->to($mailqueue->to)->subject($mailqueue->subject);
                });
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
        //mark this mailqueue as sent
        $this->status = HALO_MAILQUEUE_SENT;
        return true;
    }

    /**
     * Return toggleable status state of mailqueue
     * 
     * @param  string $field 
     * @return array        
     */
    public function getStates($field)
    {
        //by default only enable/disable state provided. For additional states, need to override this method
        return array(0 => array('title' => __halotext('Pending'),
            'icon' => 'random text-info'),
            1 => array('title' => __halotext('Sent'),
                		'icon' => 'check-circle text-success')

        );
    }

}
