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

class JAXResponse
{
    public $_response = null;
    public $_queue = array();

    public static $instances = array();

    protected static $isAjax = false;

    protected $data = array();

    protected $zoneContent = array();
    protected $zonePagination = array();
    protected $zoneScript = array();

    protected $mb = null;

    const CONTENT_INSERT = 1;
    const CONTENT_UPDATE = 2;
    /**
     * turn on ajax flag
     */

    public static function setAjax()
    {
        self::$isAjax = true;
    }

    /**
     * get the current ajax flag
     */
    public static function ajax()
    {
        return self::$isAjax;
    }

    /**
     * get the current ajax flag
     */
    public static function isAjax()
    {
        return self::$isAjax;
    }
    /**
     * get Instance
     * 
     * @param  string $type
     * @return JAXResponse
     */
    public static function getInstance($type = 'default')
    {
        if (empty(self::$instances[$type])) {
            self::$instances[$type] = new JAXResponse();
        }
        return self::$instances[$type];
    }
    /**
     * return JAXResponse
     */
    public function JAXResponse()
    {
        $this->_response = array();
        $this->mb = HALOError::getMessageBag();

        //Add dummy response so we can easily track for errro
        $this->addClear('ajax_calls', 'd');
    }
    /**
     * 
     * @param object $obj 
     * @return array
     */
    public function object_to_array($obj)
    {
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        $arr = array();
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * Assign new sData to the $sTarget's $sAttribute property
     * 
     * @param string $sTarget
     * @param string $sAttribute
     * @param string $sData
     * @return  JAXResponse
     */
    public function addAssign($sTarget, $sAttribute, $sData)
    {
        $this->_response[] = array('as', $sTarget, $sAttribute, $this->encodeString($sData));
        return $this;
    }

    /**
     * Update zone with html
     * 
     * @param  string $sData
     * @return JAXResponse
     */
    public function updateZone($sData)
    {
        $this->_response[] = array('uz', '', '', $this->encodeString($sData));
        return $this;
    }

    /**
     * Insert a zone after another zone
     * 
     * @param  string $zone
     * @param  string $sData
     * @return JAXResponse
     */
    public function afterZone($zone, $sData)
    {
        $this->_response[] = array('az', $zone, '', $this->encodeString($sData));
        return $this;
    }

    /**
     * Insert a zone before another zone
     * 
     * @param  string $zone
     * @param  string $sData
     * @return JAXResponse
     */
    public function beforeZone($zone, $sData)
    {
        $this->_response[] = array('bz', $zone, '', $this->encodeString($sData));
        return $this;
    }


    /**
     * Insert a zone as a child of another zone
     * 
     * @param  string $zone
     * @param  string $sData
     * @param  string $mode
     * @return JAXResponse
     */
    public function insertZone($zone, $sData, $mode = 'last')
    {
        $this->_response[] = array('iz', $zone, $mode, $this->encodeString($sData));
        return $this;
    }


    /**
     * Insert a zone content
     * 
     * @param  string $sData
     * @param  string $mode
     * @return JAXResponse
     */
    public function insertZoneContent($sData, $mode = 'last')
    {
        $this->_response[] = array('izc', '', $mode, $this->encodeString($sData));
        return $this;
    }

    /**
     * Delete a zone
     * 
     * @param  string $zone
     * @return JAXResponse
     */
    public function removeZone($zone)
    {
        $this->_response[] = array('rz', $zone, '', '');
        return $this;
    }


    /**
     * Clear the given target property
     * 
     * @param string $sTarget
     * @param string $sAttribute
     * @return  
     */
    public function addClear($sTarget, $sAttribute)
    {
        $this->_response[] = array('as', $sTarget, $sAttribute, "");
        return $this;
    }
    /**
     *
     * 
     * @param string $sParent
     * @param string $sTag 
     * @param string $sId  
     * @param string $sType
     * @return  JAXResponse
     */
    public function addCreate($sParent, $sTag, $sId, $sType = "")
    {
        $this->_response[] = array('ce', $sParent, $sTag, $sId);
        return $this;
    }
    /**
     * addRemove
     * 
     * @param string $sTarget
     * @return  JAXResponse
     */
    public function addRemove($sTarget)
    {
        $this->_response[] = array('rm', $sTarget);
        return $this;
    }

    /**
     * Assign new sData to the $sTarget's $sAttribute property
     * 
     * @param string $sData
     * @return JAXResponse
     */
    public function addAlert($sData)
    {
        $this->_response[] = array('al', "", "", $this->encodeString($sData));
        return $this;
    }

    /**
     * Return error message on input data validation failed
     * 
     * @param string $error
     * @return JAXResponse
     */
    public function addError($error)
    {
        $this->_response[] = array('er', "", "", $this->encodeString($error));
        return $this;
    }

    /**
     * ajax system message
     * 
     * @param  string $sData
     * @param  string $sType 
     * @return JAXResponse
     */
    public function enqueueMessage($sData, $sType = 'message')
    {
        $this->_response[] = array('msg', "", $sType, $this->encodeString($sData));

        return $this;
    }

    /**
     * ajax redirect
     * 
     * @param  string $sData 
     * @param  string $message
     * @param  string $sType 
     * @return JAXResponse
     */
    public function redirect($sData, $message = '', $sType = 'message')
    {
        $this->_response[] = array('red', $sType, $this->encodeString($message), $this->encodeString($sData));

        return $this;
    }
    /**
     * refresh
     * 
     * @param  bool $clearCache
     * @return JAXResponse
     */
    public function refresh($clearCache = false)
    {
        $this->_response[] = array('ref', "", "", $clearCache);

        return $this;
    }
    /**
     * 
     * 
     * @param  string $str
     * @return string
     */
    public function _hackString($str)
    {
        # Convert '{' and '}' to 0x7B and 0x7D
        //$str = str_replace(array('{', '}'), array('&#123;', '&#125;'), $str);
        return $str;
    }
    /**
     * 
     * 
     * @return JAXResponse
     */
    public function _stopLoading()
    {
        $this->_response[] = array('stop', "", "", "");
        return $this;
    }

    /**
     * Add a script call
     * 
     * @param string $func
     * @return  JAXResponse
     */
    public function addScriptCall($func)
    {
        $size = func_num_args();
        $response = "";

        if ($size > 1) {
            $response = array();

            for ($i = 1; $i < $size; $i++) {
                $arg = func_get_arg($i);
                $response[] = $this->encodeString($arg);
            }
        }

        $this->_response[] = array('cs', $func, "", $response);
        return $this;
    }


    /**
     * Queue up a script call
     * 
     * @param  string $func
     * @return  JAXResponse
     */
    public function queueScriptCall($func)
    {
        $size = func_num_args();
        $response = "";

        if ($size > 1) {
            $response = array();

            for ($i = 1; $i < $size; $i++) {
                $arg = func_get_arg($i);
                $response[] = $this->encodeString($arg);
            }
        }

        $this->_queue[] = array('cs', $func, "", $response);
        return $this;
    }
    /**
     * encode String
     * 
     * @param string $contents 
     * @return string
     */
    public function encodeString($contents)
    {
        //return addcslashes($contents,'"');
        return $contents;
    }


    /**
     * Flush the output back
     * 
     * @param  bool $triggerEvent
     * @return string
     */
    public function sendResponse($triggerEvent = true)
    {

        //covert all message to error
        if ($this->mb->any()) {
            $this->addError($this->mb->__toString());
        }
        if ($triggerEvent) {
            Event::fire('system.onAjaxResponse', array());
        }

        $obEnabled = ini_get('output_buffering');

        if ($obEnabled == "1" || $obEnabled == 'On') {
            $ob_active = ob_get_length() !== false;
            if ($ob_active) {
                while (@ob_end_clean());
                if (function_exists('ob_clean')) {
                    @ob_clean();
                }
            }
            ob_start();
        }

        //$output = Response::json($this->_response);
        if (!empty($this->_queue)) {
            $response = array_merge($this->_response, $this->_queue);
        } else {
            $response = $this->_response;
        }
        $output = json_encode($response);
        return $output;
    }
    /**
     *
     * 
     * @param int $id 
     * @param  mixed $data
     * @return  JAXResponse
     */
    public function addScriptCallJson($id, $data)
    {
        $this->_response[] = array('csj', $id, "", $data);
        return $this;
    }

    /**
     * set data
     * @param  mixed $key
     * @param mixed $val
     * @return  JAXResponse 
     */
    public function setData($key, $val)
    {
        $this->data[$key] = $val;
        return $this;
    }

    /**
     * get data
     * 
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     *  check if data is set
     *  
     * @param  mixed  $key
     * @return bool
     */
    public function hasData($key)
    {
        return isset($this->data[$key]);
    }

    /*
    add message bag error
     */
    public function addMessage($mb)
    {

        $this->mb->merge($mb->getMessages());
        return $this;
    }

    /*
    return message bag error
     */
    public function getMessage()
    {
        return $this->mb;
    }
	
	public function clearMessages(){
		$this->mb = HALOError::getMessageBag();
	}
    /*
    
     */
    /**
     * check if the current request is ajax
     * 
     * @return bool
     */
    public static function isAjaxRequest()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
             and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * Display popup login form
     * 
     * @param  string $err_msg
     * @return JSON
     */
    public function login($err_msg = '')
    {

        //setup redirect url
        $redirect_url = HALOResponse::getData('redirect_url');

        //display login page if in mobile
        if (!$redirect_url) {
            $redirect_url = Input::get('__refer_url');
            HALOResponse::setData('redirect_url', $redirect_url);
        }

        $builder = HALOUIBuilder::getInstance('ajaxlogin', 'ajaxlogin', array('name' => 'loginForm', 'msg' => $err_msg));
        $content = $builder->fetch();
        $title = __halotext("Log in into your account");
		$this->addScriptCall('halo.popup.close');
        $this->addScriptCall('halo.popup.setFormTitle', $title)
             ->addScriptCall('halo.popup.setFormContent', $content);
        if (!empty($err_msg)) {
            $this->addScriptCall('halo.popup.setMessage', $err_msg, 'warning');
        }
        $this->addScriptCall('halo.popup.showForm')
             ->addScriptCall('halo.user.setupSubmitLogin');

        return $this->sendResponse();
    }


    /**
     * function to set zone content, there are 2 mode:insert or update
     * 
     * @param int $zoneId 
     * @param string $content
     * @param string $mode
     * 
     */
    public function setZoneContent($zoneId, $content, $mode)
    {
        //this function behave differently depend on the current request type
        if (JAXResponse::ajax()) {
            if ($mode == HALO_CONTENT_INSERT_MODE) {
                $this->insertZone($zoneId, $content);
            } else if ($mode == HALO_CONTENT_UPDATE_MODE) {
                $this->updateZone($content);
            }
        } else {
            //in case of get/post request, mode setting is not important, just store the zone content data
            $this->zoneContent[$zoneId] = $content;
        }
    }

    /**
     * function to get zone content that is set from pervious set content function
     * 
     * @param  int $zoneId
     * @return string
     */
    public function getZoneContent($zoneId)
    {
        return isset($this->zoneContent[$zoneId]) ? $this->zoneContent[$zoneId] : '';
    }

    /**
     * function to set zone content pagination links
     * 
     * @param int $zoneId
     * @param string $paginationHtml
     * @return  string
     */
    public function setZonePagination($zoneId, $paginationHtml)
    {
        //this function behave differently depend on the current request type
        if (JAXResponse::ajax()) {
            $this->addScriptCall('halo.util.addZonePagination', $zoneId, $paginationHtml);
        } else {
            //in case of get/post request, mode setting is not important, just store the zone content data
            $this->zonePagination[$zoneId] = $paginationHtml;
        }

    }

    /**
     * function to get zone pagniation that is set from pervious set pagination function
     * 
     * @param  int $zoneId
     * @return string
     */
    public function getZonePagination($zoneId)
    {
        return isset($this->zonePagination[$zoneId]) ? $this->zonePagination[$zoneId] : '';
    }


    /**
     * function to set zone script
     * 
     * @param int $zoneId
     * @param string $func
     * @return  string
     */

    public function addZoneScript($zoneId, $func)
    {
        //this function behave differently depend on the current request type
        $args = func_get_args();
        //remove the zoneId from args list
        array_shift($args);
        if (JAXResponse::ajax()) {
            call_user_func_array(array($this, 'addScriptCall'), $args);
        } else {
            //remove func from args list
            array_shift($args);
            $params = array();
            foreach ($args as $arg) {
                $params[] = json_encode($this->encodeString($arg));
            }
            $script = 'halo.util.scriptCall(\'' . $func . '\',' . json_encode($args) . ');';
            if (!isset($this->zoneScript[$zoneId])) {
                $this->zoneScript[$zoneId] = array();
            }
            $this->zoneScript[$zoneId][] = $script;
        }
    }

    /**
     * function to get zone pagniation that is set from pervious set pagination function
     * 
     * @param  int $zoneId
     * @return string
     */
    public function getZoneScript($zoneId)
    {
        if (isset($this->zoneScript[$zoneId])) {
            return '<script>__haloReady(function() {' . implode(';', $this->zoneScript[$zoneId]) . '});</script>';
        } else {
            return '';
        }
    }
}
