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

class StatusController extends BaseController
{
    /**
     * Initializer
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * process ajax submit a new status
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSubmitStatus($postData)
    {
        //1. check permission to make sure user can perform the action

        //2. dectect the handler to process the action
        $shareAction = isset($postData['share_action']) ? $postData['share_action'] : '';
        $actionParts = explode('.', $shareAction);

        //validate the shareAction format
        Redirect::ajaxError(__halotext('Invalid share action'))
            ->when(count($actionParts) != 2)
            ->apply();

        //controller is the first part of share_action
        $controllerName = ucfirst($actionParts[0]) . 'Controller';

        //method is retrivied from the second part of share_action
        $method = 'handler' . ucfirst($actionParts[1]);

        //validate the shareAction format
        Redirect::ajaxError(__halotext('Invalid handler'))
            ->when(!method_exists($controllerName, $method))
            ->apply();

        //3. collect activity data
        HALOActivityAPI::setData('action', $postData['share_action']);
        HALOActivityAPI::setData('context', $postData['share_context']);
        HALOActivityAPI::setData('tagged_list', $postData['share_tagged_list']);
        HALOActivityAPI::setData('target_id', $postData['share_target_id']);
        HALOActivityAPI::setData('message', $postData['share_message']);
        HALOActivityAPI::setData('access', $postData['share_privacy']);
        HALOActivityAPI::setData('location_name', $postData['share_location_name']);
        HALOActivityAPI::setData('location_lat', $postData['share_location_lat']);
        HALOActivityAPI::setData('location_lng', $postData['share_location_lng']);
		
		//4. collect display actor
		if(isset($postData['share_display_context']) && isset($postData['share_display_id'])) {
			HALOActivityAPI::setData('display_context', $postData['share_display_context']);
			HALOActivityAPI::setData('display_id', $postData['share_display_id']);		
		}
		
        if (isset($postData['nopreview'])) {
            HALOActivityAPI::setData('nopreview', $postData['nopreview']);
        }
        //set the current location
        HALOLocationModel::setCurrentLocation($postData['share_location_name'], $postData['share_location_lat'], $postData['share_location_lng']);

        //call the handler to handle create new status action
        $controller = new $controllerName();
        $result = call_user_func_array(array($controller, $method), array($postData));
        if ($result) {
            if (HALOResponse::hasData('act')) {
                //on post created successfully, the created activity is stored in $msg->_act
                HALOResponse::insertZone('stream_content', HALOResponse::getData('act')->render(), 'first');
            }
            //reset the status form on succeess
            HALOResponse::addScriptCall('halo.status.reset');
        }
        //4. response

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to delete a status
     *
     * @param  int $actId
     * @return JSON
     */
    public function ajaxDeleteStatus($actId)
    {
        $act = HALOActivityModel::find($actId);
        Redirect::ajaxError(__halotext('Invalid Id'))
            ->when(!$act)
            ->apply();

        //1. check permission to make sure user can perform the action
        if (!HALOAuth::can('activity.delete', $act)) {
            return;
        }
        //2. dectect the handler to process the action
        $actionParts = explode('.', $act->action);

        //controller is the first part of share_action
        $controllerName = ucfirst($actionParts[0]) . 'Controller';
        //method is retrivied from the second part of share_action
        $method = 'handlerDelete';

        //validate the shareAction format
        Redirect::ajaxError(__halotext('Invalid handler'))
            ->when(!method_exists($controllerName, $method))
            ->apply();

        //3. call the handler
        $controller = new $controllerName();
        $response = call_user_func_array(array($controller, $method), array($act));

        //4. response

        return $response;
    }

    /**
     * ajax handler to show edit post message form
     *
     * @param  int $actId
     * @return JSON
     */
    public function ajaxEditPostForm($actId)
    {
        $act = HALOActivityModel::find($actId);
        Redirect::ajaxError(__halotext('Invalid Id'))
            ->when(!$act)
            ->apply();
        //1. check permission to make sure user can perform the action
        if (HALOAuth::can('activity.edit', $act)) {
			HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'activity_edit', array('act'=> $act))->fetch());
        }

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to cancel edit post message form
     *
     * @param  int $actId
     * @return JSON
     */
    public function ajaxCancelEditPost($actId)
    {
        $act = HALOActivityModel::find($actId);
        Redirect::ajaxError(__halotext('Invalid Id'))
            ->when(!$act)
            ->apply();
        if (HALOAuth::can('activity.edit', $act)) {
			HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'activity_message', array('act'=> $act))->fetch());
        }

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to cancel edit post message form
     *
     * @param  int $actId
     * @param  int $data form data
     * @return JSON
     */
    public function ajaxDoneEditPost($actId, $data)
    {
        $act = HALOActivityModel::find($actId);
        Redirect::ajaxError(__halotext('Invalid Id'))
            ->when(!$act)
            ->apply();
        if (HALOAuth::can('activity.edit', $act)) {
			if(isset($data['edited_message']) && isset($data['edited_message'])) {
				$message = null;
				if(!empty($data['edited_message'])) {
					$message = $data['edited_message'];
				} else {
					if($data['edited_message'] == $data['act_message']) {
						$message = $data['edited_message'];
					}
				}
				if($message !== null) {
					$act->message = $message;
					HALOActivityAPI::save($act);
				}
			}
			//reload message content
			HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'activity_message', array('act'=> $act))->fetch());
        }

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to change privacy of  a status
     *
     * @param  int $actId
     * @param  int $val
     * @return JSON
     */
    public function ajaxChangePrivacy($actId, $val)
    {
        $act = HALOActivityModel::find($actId);
        Redirect::ajaxError(__halotext('Invalid Id'))
            ->when(!$act)
            ->apply();
        //1. check permission to make sure user can perform the action
        if (HALOAuth::can('activity.edit', $act)) {
            $act->access = $val;
            $act->save();
        }

        return HALOResponse::sendResponse();
    }

    /**
     * handler for status.create action
     *
     * @param  array $data
     * @return bool
     */
    public function handlerCreate($data)
    {
        //just trigger an event for adding to the stream
        if (!isset($data['share_message']) || $data['share_message'] == '') {
            HALOResponse::addMessage(HALOError::failed(__halotext('Please enter your share message')));
            return false;
        }

        if (Event::fire('status.onBeforeAdding', array($data), true) === false) {
            //error occur, return
            return false;
        }

        $event = Event::fire('status.onAdding', array());

        return true;
    }

    /**
     * handler for status.delete action
     *
     * @param  HALOActivityModel $act
     * @return JSON
     */
    public function handlerDelete($act)
    {
        $msg = new stdClass();
        $actid = $act->id;
        $act->delete();

        HALOResponse::addScriptCall('halo.status.deleteCallBack', $actid);

        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to reset share status form
     *
     * @param  string $context
     * @param  string $target
     * @return JSON
     */
    public function ajaxResetStatusForm($context, $target)
    {
        $html = HALOStatus::render($context, $target);
        if ($html) {
            HALOResponse::updateZone($html);
        }

        return HALOResponse::sendResponse();
    }
}
