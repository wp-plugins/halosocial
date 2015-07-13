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

class MessageController extends BaseController
{

    /**
     * Inject the models.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a single conv
     *
     * @param  int $convId
     * @return View
     */
    public function getShow($convId = null)
    {
        $my = HALOUserModel::getUser();

        if (!$my) {
            $title = __halotext('Error');
            $messages = HALOError::failed(__halotext('Login required'));
            return View::make('site/error', compact('title', 'messages'));
        }

        //get recent conversation list: get only conversation in last n days
        $filters = array();
        $options = array('attender_id' => $my->user_id, 'filters' => $filters, 'orderBy' => 'updated_at');

        //get list of active converstation
		$convsArr = array();
        $convs = HALOConversationModel::getConversations($options);
		if($convs) {
			$convs->load('lastMessage', 'convattenders');

			//group convs by day
			$attenderIds = array();
			$today = Carbon::today();
			foreach ($convs as $c) {
				//load attenders
				$attenderIds = array_merge($attenderIds, $c->convattenders->lists('attender_id'));

				$diff = $c->updated_at->diffInDays($today);
				if (!isset($convsArr[$diff])) {
					$convsArr[$diff] = array();
				}
				$convsArr[$diff][] = $c;
			}

			HALOUserModel::init($attenderIds);
		}
		
		//$convId is not provide, try to get the latest conv
		if(is_null($convId) && count($convs)) {
			$convId = $convs->first()->id;
		}
		if(!is_null($convId)){
			$conv = HALOConversationModel::find($convId);
			if (!$conv) {
				$title = __halotext('Error');
				$messages = HALOError::failed(__halotext('Invalid Conversation Id'));
				return View::make('site/error', compact('title', 'messages'));
			}
			//@todo: check permission
			//user must be a part of conversation attenders to view the conversation
			if (!$conv->isAttender($my->id)) {
				$title = __halotext('Error');
				$messages = HALOError::failed(__halotext('Only participants in the conversation can access the conversation'));
				return View::make('site/error', compact('title', 'messages'));
			}
			//load conv
			$conv->load('messages', 'convattenders');

			//load messages
			//get all unread messages
			/*
			$messages = $conv->getMessages(array('limit'=>'all','newOnly'=>true));
			if(count($messages) < HALOConfig::get('message.defaultDisplayLimit')){
			//load more read messages to reach default message display limit
			$limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
			$olderMessages = $conv->getMessages(array('limit'=>$limit));
			//merge old and unread messages together
			$messages = $olderMessages->merge($messages);
			}
			 */
			$messages = $conv->getMessages(array());
			$title = $conv->getDisplayName();
		} else {
			$conv = null;
			$messages = null;
			$title = __halotext('My messages');
		}
		

        return View::make('site/conv/conv', compact('title', 'my', 'conv', 'messages', 'convsArr'));
    }

    /**
     * ajax to change conversation content in fullview mode
     *
     * @param  int $convId
     * @return JSON
     */
    public function ajaxChangeConv($convId)
    {
        $conv = HALOConversationModel::find($convId);
        Redirect::ajaxError(__halotext('Invalid conversation Id'))
            ->when(is_null($conv))
            ->apply();
        //@todo: check permission
        //user must be a part of conversation attenders to view the conversation
        $my = HALOUserModel::getUser();
        Redirect::ajaxError(__halotext('Only participants in the conversation can access the conversation'))
            ->when(!$conv->isAttender($my->id))
                                      ->apply();
        //load conv
        $conv->load('messages', 'convattenders');

        //load messages
        //get all unread messages
        $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
        if (count($messages) < HALOConfig::get('message.defaultDisplayLimit')) {
            //load more read messages to reach default message display limit
            $limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
            $olderMessages = $conv->getMessages(array('limit' => $limit));
            //merge old and unread messages together
            $messages = $olderMessages->merge($messages);
        }

        $html = HALOUIBuilder::getInstance('', 'conv.fullview_container', array('messages' => $messages, 'conv' => $conv))->fetch();
        //update conv content
        HALOResponse::updateZone($html);

        //update client url if supported
        HALOResponse::addScriptCall('halo.util.setUrl', $conv->getUrl());

        //set input in focus
        HALOResponse::addScriptCall('halo.message.focusConvFullView', $conv->id);
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handler to display the message container html
     *
     * @param  int $convIds
     * @return JSON
     */
    public function ajaxGetContainerHtml($convIds)
    {

        $my = HALOUserModel::getUser();
        //check permission
        if (!$my) {
            return HALOResponse::sendResponse();
        }
        //get contacts list
        $contacts = $my->getFriends();

        //get recent conversation list: get only conversation in last n days
        $recentDays = HALOConfig::get('conv.showrecentdays');
        $today = Carbon::today();
        $nDay = $today->subDays($recentDays);
        $filters = array();
        $options = array('attender_id' => $my->user_id, 'filters' => $filters, 'since' => $nDay->__toString(), 'orderBy' => 'updated_at');

        //get list of active converstation
        $convs = HALOConversationModel::getConversations($options);

        //group convs by day
        $convsArr = array();
        $today = Carbon::today();
        foreach ($convs as $conv) {
            $diff = $conv->updated_at->diffInDays($today);
            if (!isset($convsArr[$diff])) {
                $convsArr[$diff] = array();
            }
            $convsArr[$diff][] = $conv;
        }

        //build container html
        $html = HALOUIBuilder::getInstance('', 'conv.container', array('contacts' => $contacts, 'conv_groups' => $convsArr))->fetch();

        HALOResponse::addScriptCall('halo.message.displayContainer', $html);

        //also requested conversation contents
        if (is_array($convIds) && !empty($convIds)) {
            $convs = HALOConversationModel::find($convIds);
            if (!empty($convs)) {
                $my = HALOUserModel::getUser();

                //go through all the convs and update the message
                foreach ($convs as $conv) {
                    //@todo: check permission
                    //user must be a part of conversation attenders to view the conversation
                    if ($conv->isAttender($my->id)) {

                        //get all unread messages
                        $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
                        if (count($messages) < HALOConfig::get('message.defaultDisplayLimit')) {
                            //load more read messages to reach default message display limit
                            $limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
                            $olderMessages = $conv->getMessages(array('limit' => $limit));
                            //merge old and unread messages together
                            $messages = $olderMessages->merge($messages);
                        }

                        $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
                        $title = sprintf(__halotext('%s'), $conv->getDisplayName());

                        $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

                        HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());

                    }

                }
            }

        }
        return HALOResponse::sendResponse();
    }

    /**
     * ajax handler to load older conversation list
     *
     * @return JSON
     */
    public function ajaxShowOlderConvs()
    {

        $my = HALOUserModel::getUser();
        //check permission
        if (!$my) {
            return HALOResponse::sendResponse();
        }
        //get contacts list
        $contacts = $my->getFriends();

        $filters = array();
        $options = array('attender_id' => $my->user_id, 'filters' => $filters, 'orderBy' => 'updated_at', 'limit' => 'all');

        //get list of active converstation
        $convs = HALOConversationModel::getConversations($options);

        //group convs by day
        $convsArr = array();
        $today = Carbon::today();
        foreach ($convs as $conv) {
            $diff = $conv->updated_at->diffInDays($today);
            if (!isset($convsArr[$diff])) {
                $convsArr[$diff] = array();
            }
            $convsArr[$diff][] = $conv;
        }

        //build container html
        $html = HALOUIBuilder::getInstance('', 'conv.recentlist', array('zone' => 'conv.panel.recentlist', 'showOlder' => false, 'contacts' => $contacts, 'conv_groups' => $convsArr))->fetch();

        HALOResponse::updateZone($html);

        return HALOResponse::sendResponse();

    }

    /**
     *  ajax handle to open a conversation window between current login user and another user
     *
     * @param  int $userId
     * @param  string $fullSize
     * @return JSON
     */
    public function ajaxOpenConvByUserId($userId, $fullSize)
    {

        $userA = HALOUserModel::getUser();
        $userB = HALOUserModel::getUser($userId);
        //@todo: check permission
        //validate post data
        Redirect::ajaxError(__halotext('Messaging is not allowed on between you and this user'))
            ->when(is_null($userA->id) || is_null($userB->id))
                                                     ->apply();
        $attenders = array($userA->user_id, $userB->user_id);
        $conv = HALOConversationModel::findConv($attenders);

        //get all unread messages
        $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
        if (count($messages) < HALOConfig::get('message.defaultDisplayLimit')) {
            //load more read messages to reach default message display limit
            $limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
            $olderMessages = $conv->getMessages(array('limit' => $limit));
            //merge old and unread messages together
            $messages = $olderMessages->merge($messages);
        }
        if ($fullSize) {
            HALOResponse::redirect($conv->getUrl());
        } else {
            //update zone
            $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
            $title = $conv->getDisplayName();

            $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

            //make sure the conv storage is clean
            HALOResponse::addScriptCall('halo.message.removeConvStorage', $builder->fetch());
            HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());
        }
        return HALOResponse::sendResponse();
    }

    /**
     * ajax handle to open a conversation window between current login user and another user
     *
     * @param  int $convId
     * @return JSON
     */
    public function ajaxOpenConvByConvId($convId)
    {

        $conv = HALOConversationModel::find($convId);
        Redirect::ajaxError(__halotext('Invalid conversation Id'))
            ->when(is_null($conv))
            ->apply();
        //@todo: check permission
        //user must be a part of conversation attenders to view the conversation
        $my = HALOUserModel::getUser();
        Redirect::ajaxError(__halotext('Only participants in the conversation can access the conversation'))
            ->when(!$conv->isAttender($my->id))
                                      ->apply();

        //get all unread messages
        $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
        if (count($messages) < HALOConfig::get('message.defaultDisplayLimit')) {
            //load more read messages to reach default message display limit
            $limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
            $olderMessages = $conv->getMessages(array('limit' => $limit));
            //merge old and unread messages together
            $messages = $olderMessages->merge($messages);
        }

        //update zone
        $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
        $title = $conv->getDisplayName();

        $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

        HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to open a conversation window between current login user and another user
     *
     * @param  int $convIds
     * @return JSON
     */
    public function ajaxLoadConvs($convIds)
    {

        $convs = HALOConversationModel::find($convIds);
        Redirect::ajaxError(__halotext('Invalid conversation Id'))
            ->when(empty($convs))
            ->apply();
        $my = HALOUserModel::getUser();

        //go through all the convs and update the message
        foreach ($convs as $conv) {
            //@todo: check permission
            //user must be a part of conversation attenders to view the conversation
            Redirect::ajaxError(__halotext('Only participants in the conversation can access the conversation'))
                ->when(!$conv->isAttender($my->id))
                                          ->apply();

            //get all unread messages
            $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
            if (count($messages) < HALOConfig::get('message.defaultDisplayLimit')) {
                //load more read messages to reach default message display limit
                $limit = HALOConfig::get('message.defaultDisplayLimit') - count($messages);
                $olderMessages = $conv->getMessages(array('limit' => $limit));
                //merge old and unread messages together
                $messages = $olderMessages->merge($messages);
            }

            $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
            $title = sprintf(__halotext('%s'), $conv->getDisplayName());

            $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

            HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());

        }
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to submit a new message
     *
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSubmit($postData)
    {

        //check if this is edit message submit

        //get the target object
        $conv_id = $postData['conv_id'];
        $messsageTxt = $postData['message'];
        $conv = HALOConversationModel::find($conv_id);
        $fullview = isset($postData['fullview']) ? $postData['fullview'] : 0;
        //validate post data
        Redirect::ajaxError(__halotext('The conversation does not exist'))
            ->when(empty($conv))
            ->apply();

        //@todo: check permission

        if (!HALOMessageAPI::add($postData, $conv)) {
            return HALOResponse::sendResponse();
        } else {
            $message = HALOResponse::getData('message');
            if ($fullview) {
                HALOResponse::insertZone($conv->getZone(1), HALOUIBuilder::getInstance('', 'conv.fullview_entry', array('message' => $message, 'conv' => $conv))->fetch(), 'last');
                HALOResponse::addScriptCall('halo.message.stickFullViewScroll');
            } else {
                HALOResponse::insertZone($conv->getZone(1), HALOUIBuilder::getInstance('', 'conv.entry', array('message' => $message, 'conv' => $conv))->fetch(), 'last');
            }

            //update the conv window
            HALOResponse::addScriptCall('halo.message.displayConv', $conv->id);
            return HALOResponse::sendResponse();

        }
    }

    /**
     * callback for stream updater
     *
     * @param array $filterForm
     */
    public function ContentUpdater($filterForm)
    {
        $user = HALOUserModel::getUser();

        //@todo: permission checking
        if (empty($user->user_id)) {
            return true;
        }

        //apply filter to query activities
        $filtersArr = isset($filterForm['filters']) ? $filterForm['filters'] : array(0);
        $latestId = isset($filterForm['latestId']) ? $filterForm['latestId'] : 0;
        $fullviewId = isset($filterForm['fullviewId']) ? $filterForm['fullviewId'] : 0;
        //check input data

        //load filters model

        $filters = HALOFilter::getFilterByIds(array_keys($filtersArr));
        //update filters values
        foreach ($filters as $filter) {
            $filter->value = isset($filtersArr[$filter->id]) ? $filtersArr[$filter->id] : '';
        }

        $options = array('attender_id' => $user->user_id, 'filters' => $filters, 'latest_id' => $latestId, 'newOnly' => true);

        //get list of unread converstation
        $convs = HALOConversationModel::getConversations($options);

        //go through all the convs and update the message
        foreach ($convs as $conv) {
            if ($conv->id != $fullviewId) {
                //update zone
                $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
                $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
                $title = sprintf(__halotext('%s'), $conv->getDisplayName());
                $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

                HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());
            } else {
                //for fullview conv
                $messages = $conv->getMessages(array('limit' => 'all', 'newOnly' => true));
                foreach ($messages as $message) {
                    HALOResponse::insertZone($conv->getZone(1), HALOUIBuilder::getInstance('', 'conv.fullview_entry', array('message' => $message, 'conv' => $conv))->fetch(), 'last');
                }
                HALOResponse::addScriptCall('halo.message.stickFullViewScroll');

            }
        }

    }

    /**
     * ajax handler to update the lastseen message id of a conversation
     *
     * @param  itn $convId
     * @param  int $lastseen_id
     * @return JSON
     */
    public function ajaxUpdateLastSeenMessage($convId, $lastseen_id)
    {

        $conv = HALOConversationModel::find($convId);

        //validate post data
        Redirect::ajaxError(__halotext('The conversation does not exist'))
            ->when(empty($conv))
            ->apply();
        //@todo: check permission
        $my = HALOUserModel::getUser();
        Redirect::ajaxError(__halotext('Permission dennied'))
            ->when(!$conv->isAttender($my->id))
                                      ->apply();
        $convDetail = $conv->detail()->first();
        if ((int) $convDetail->lastseen_id < (int) $lastseen_id) {
            $convDetail->lastseen_id = $lastseen_id;
            $convDetail->save();
        }
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to load older messages
     *
     * @param  int $convId
     * @param  int $lastId
     * @return JSON
     */
    public function ajaxLoadOlderMessages($convId, $lastId)
    {

        $conv = HALOConversationModel::find($convId);
        Redirect::ajaxError(__halotext('Invalid conversation Id'))
            ->when(is_null($conv))
            ->apply();
        //@todo: check permission
        //user must be a part of conversation attenders to view the conversation
        $my = HALOUserModel::getUser();
        Redirect::ajaxError(__halotext('Only participants in the conversation can access the conversation'))
            ->when(!$conv->isAttender($my->id))
                                      ->apply();

        //get all unread messages
        $messages = $conv->getMessages(array('limit' => HALOConfig::get('message.defaultDisplayLimit') * 2, 'after' => $lastId));
        if (count($messages) == 0) {
            //no older messages, mark the conv to stop any futher loading
            HALOResponse::addScriptCall('halo.message.setConvAsOldest', $conv->id);
        } else {
            //update zone
            $content = HALOUIBuilder::getInstance('', 'conv.layout', array('messages' => $messages, 'conv' => $conv, 'zone' => $conv->getZone()))->fetch();
            $title = $conv->getDisplayName();

            $builder = HALOUIBuilder::getInstance('', 'conv.window', array('title' => $title, 'content' => $content, 'conv' => $conv, 'zone' => 'conv_win_' . $conv->getZone()));

            HALOResponse::addScriptCall('halo.message.addConv', $builder->fetch());
        }
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to load older messages in fullview mode
     *
     * @param  int $convId
     * @param  int $lastId
     * @return JSON
     */
    public function ajaxLoadOlderFullViewMessages($convId, $lastId)
    {

        $conv = HALOConversationModel::find($convId);
        Redirect::ajaxError(__halotext('Invalid conversation Id'))
            ->when(is_null($conv))
            ->apply();
        //@todo: check permission
        //user must be a part of conversation attenders to view the conversation
        $my = HALOUserModel::getUser();
        Redirect::ajaxError(__halotext('Only participants in the conversation can access the conversation'))
            ->when(!$conv->isAttender($my->id))
                                      ->apply();

        //get all unread messages
        $messages = $conv->getMessages(array('limit' => HALOConfig::get('message.defaultDisplayLimit') * 2, 'after' => $lastId));
        if (count($messages) == 0) {
            //no older messages, mark the conv to stop any futher loading
            HALOResponse::addScriptCall('halo.message.stopFullViewLoadMore', $conv->id);
        } else {
            $content = '';
            foreach ($messages as $message) {
                $content .= HALOUIBuilder::getInstance('', 'conv.fullview_entry', array('message' => $message, 'conv' => $conv))->fetch();
            }
            HALOResponse::addScriptCall('halo.message.showFullViewMessages', $content);
        }
        return HALOResponse::sendResponse();

    }

}
