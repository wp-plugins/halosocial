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

class NotificationEventHandler
{

	/**
	 * Subscribe 
	 * 
	 * @param  array $events 
	 */
    public function subscribe($events)
    {
        $events->listen('comment.onAfterAdding', 'NotificationEventHandler@onCommentAdding');

        $events->listen('like.onAfterLike', 'NotificationEventHandler@onLike');

        $events->listen('message.onAfterAdding', 'NotificationEventHandler@onMessageAdding');

        $events->listen('friend.onAfterRequest', 'NotificationEventHandler@onFriendRequest');

        $events->listen('friend.onAfterApprove', 'NotificationEventHandler@onFriendApprove');

        $events->listen('friend.onAfterReject', 'NotificationEventHandler@onFriendReject');

        $events->listen('mention.onMention', 'NotificationEventHandler@onMention');

        $events->listen('tag.onAfterTaggingUser', 'NotificationEventHandler@onTagUser');

        //event handler for getting notification settings
        $events->listen('notification.onLoadingSettings', 'NotificationEventHandler@onNotificationLoading');

        //event handler for notification rendering
        $events->listen('notification.onRender', 'NotificationEventHandler@onNotificationRender');

        //event handler for notification rendering
        $events->listen('notification.onRenderEmail', 'NotificationEventHandler@onNotificationRenderEmail');
    }

    /**
     * Event handler to load efault notification setting
     * 
     * @param  HALOObject $settings 
     */
    public function onNotificationLoading(HALOObject $settings)
    {
        //new comment notification
        $settings->setNsValue('comment.create.i', 1);
        $settings->setNsValue('comment.create.e', 1);
        $settings->setNsValue('comment.create.d', __halotext('New Comments'));

        //new tag notification
        $settings->setNsValue('system.tag.i', 1);
        $settings->setNsValue('system.tag.e', 1);
        $settings->setNsValue('system.tag.d', __halotext('Someone tagged you'));

        //new mention notification
        $settings->setNsValue('system.mention.i', 1);
        $settings->setNsValue('system.mention.e', 1);
        $settings->setNsValue('system.mention.d', __halotext('Someone mentioned you'));

        //new like notification
        $settings->setNsValue('system.like.i', 1);
        $settings->setNsValue('system.like.e', 1);
        $settings->setNsValue('system.like.d', __halotext('Someone liked your content'));

        //new message notification
        $settings->setNsValue('system.message.i', 1);
        $settings->setNsValue('system.message.e', 1);
        $settings->setNsValue('system.message.d', __halotext('Someone sent you a new message'));

        //friend request
        $settings->setNsValue('friend.request.i', 1);
        $settings->setNsValue('friend.request.e', 1);
        $settings->setNsValue('friend.request.d', __halotext('New Friend Request'));

        //friend approved
        $settings->setNsValue('friend.approve.i', 1);
        $settings->setNsValue('friend.approve.e', 1);
        $settings->setNsValue('friend.approve.d', __halotext('Approved Friend Request'));

        //friend rejected
        $settings->setNsValue('friend.reject.i', 1);
        $settings->setNsValue('friend.reject.e', 1);
        $settings->setNsValue('friend.reject.d', __halotext('Rejected Friend Request'));
    }

    /**
     * Event handler to create a new notification on new comment added
     * 
     * @param  string $comment 
     * @param  array  $target  
     * @return HALONotificationAPI          
     */
    public function onCommentAdding($comment, $target)
    {
        //prepare data
        $options = array();
        $options['action'] = 'comment.create';
        $options['context'] = $target->getContext();
        $options['target_id'] = $target->id;
        $params = HALOParams::getInstance();

        $params->set('comment_id', $comment->id);
        $options['params'] = $params->toString();
        //receivers
        $options['receivers'] = $target->getFollowers();
        return HALONotificationAPI::add($options);

    }

    /**
     * Event handler to display comment create notification
     * 
     * @param  string $notification
     */
    public function renderCommentAdding($notification)
    {
        $attachment = new stdClass();

        $my = HALOUserModel::getUser();
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //remove myself from the actor list
        $actorIds = array_diff($actorIds, array($my->id));

        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
			//remove myself from the actor list
			$actorList = $notification->getDisplayActor();
			if(method_exists($actorList, 'removeActor')) {
				$actorList->removeActor($my->id);		//use display actor data instead
			}
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actorList))->fetch();

            //render comment message as the notification content
            $comment_id = $notification->getParams('comment_id', 0);
            $message = '';
            if ($comment_id) {
                $comment = HALOCommentModel::find($comment_id);
                if ($comment) {
                    $message = $comment->getNotifContent();
                } else {
                    //comment might be delete, just skip this notification
                    return false;
                }
            }
            //check for owner relationship
            if (HALOAuth::hasRole('owner', $target)) {
                $attachment->headline = sprintf(__halotext('%s commented on your %s: %s'), $actorsHtml, $target->getNotifContent(), $message);
            } else {
                $attachment->headline = sprintf(__halotext('%s commented on a %s that you are following: %s'), $actorsHtml, $target->getNotifContent(), $message);
            }

            $attachment->content = '';
        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to display email notification for comment create action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue      
     */
    public function renderEmailCommentAdding($notification, $to, HALOMailqueueModel &$mailqueue)
    {

        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //remove myself from the actor list
        $actorIds = array_diff($actorIds, array($to->id));

        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $comment_id = $notification->getParams('comment_id', 0);
            $message = '';
            if ($comment_id) {
                $comment = HALOModel::getCachedModel('comment', $comment_id);
                if ($comment) {
                    $message = '"' . $comment->getMessage() . '"';
                }
            }
			$actorList = $notification->getDisplayActor();
			if(method_exists($actorList, 'removeActor')) {
				$actorList->removeActor($to->id);		//use display actor data instead
			}
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actorList, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actorList, 'my' => $to, 'textOnly' => true))->fetch();
			
            $mailqueue->to = $to->getEmail();
            if (HALOAuth::hasRole('owner', $target)) {
                $mailqueue->subject = sprintf(__halotext('%s commented on your %s:'), $actorsText, $target->getDisplayName());
            } else {
                $mailqueue->subject = sprintf(__halotext('%s commented on a %s that you are following'), $actorsText, $target->getDisplayName());
            }
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.commentNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $target, 'commentText' => $message))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.commentNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $target, 'commentText' => $message))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Event handler to generate like action notification
     * 
     * @param  array $target 
     * @param  array $like   
     * @return HALONotificationAPI         
     */
    public function onLike($target, $like)
    {
        //prepare data
        $options = array();
        $options['action'] = 'system.like';
        $options['context'] = $target->getContext();
        $options['target_id'] = $target->id;
        $params = HALOParams::getInstance();
        $options['params'] = $params->toString();
        //receiver is the actor or the owner
        switch ($options['context']) {
            case 'comment':
            case 'activity':
                $options['receivers'] = array($target->actor_id);
                break;
            default:
                //use the follower list
                if (method_exists($target, 'followers')) {
                    $options['receivers'] = $target->followers()->lists('follower_id');
                } else {
                    $options['receivers'] = array();
                }
        }
        return HALONotificationAPI::add($options);
    }

    /**
     * Event handler to render like action notification
     * 
     * @param  string $notification 
     */
    public function renderLike($notification)
    {
        $attachment = new stdClass();

        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s liked your %s'), $actorsHtml, $target->getNotifContent());
            //render comment message as the notification content
            $attachment->content = '';

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render email notification content for like action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue         
     */
    public function renderEmailLike($notification, $to, HALOMailqueueModel &$mailqueue)
    {

        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s liked your %s'), $actorsText, $target->getDisplayName());
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.likeNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $target))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.likeNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $target))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Event handler to generate tag action notification
     * 
     * @param  array $target  
     * @param  int $user_id 
     * @return HALONotificationAPI          
     */
    public function onTagUser($target, $user_id)
    {
        //prepare data
        $options = array();
        $options['action'] = 'system.tag';
        $options['context'] = $target->getContext();
        $options['target_id'] = $target->id;
        $params = HALOParams::getInstance();
        $options['params'] = $params->toString();
        //receiver is the actor or the owner
        $options['receivers'] = (array) $user_id;
        return HALONotificationAPI::add($options, false);
    }

    /**
     * Event handler to render like action notification
     * 
     * @param  string    $notification  
     */
    public function renderTagUser($notification)
    {
        $attachment = new stdClass();

        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s tagged you in a %s'), $actorsHtml, $target->getNotifContent());
            //render comment message as the notification content
            $attachment->content = method_exists($target, 'getMessage') ? $target->getMessage() : '';

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render like action notification
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue                
     */
    public function renderEmailTagUser($notification, $to, HALOMailqueueModel &$mailqueue)
    {

        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s tagged you in a %s'), $actorsText, $target->getDisplayName());
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.tagNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $target))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.tagNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $target))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Event handler to generate notification for new message action
     * 
     * @param  string $message 
     * @param  array $conv    
     * @return HALONotificationAPI          
     */
    public function onMessageAdding($message, $conv)
    {
        //prepare data
        $options = array();
        $options['action'] = 'system.message';
        $options['context'] = $conv->getContext();
        $options['target_id'] = $conv->id;
        $params = HALOParams::getInstance();

        $params->set('message_id', $message->id);
        $options['params'] = $params->toString();
        //receivers
        $options['receivers'] = $conv->getAttenderIds();
        return HALONotificationAPI::add($options);

    }

    /**
     * Event handler to render message create notification
     * 
     * @param  string $notification  
     */
    public function renderMessageAdding($notification)
    {
        $attachment = new stdClass();
        $my = HALOUserModel::getUser();
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);

        //remove myself from the actor list
        $actorIds = array_diff($actorIds, array($my->id));
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            //render comment message as the notification content
            $message_id = $notification->getParams('message_id', 0);
            $message = '';
            if ($message_id) {
                $message = HALOMessageModel::find($message_id);
                if ($message) {
                    $message = $message->getNotifContent();
                }
            }

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s sent you a message: %s'), $actorsHtml, $message);

            $attachment->content = '';
        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render message create notification
     * 
     * @param  string           $notification 
     * @param  string           $to         
     * @param  HALOMailqueueModel $mailqueue       
     */
    public function renderEmailMessageAdding($notification, $to, HALOMailqueueModel &$mailqueue)
    {
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);

        //remove myself from the actor list
        $actorIds = array_diff($actorIds, array($to->id));
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            //render comment message as the notification content
            $message_id = $notification->getParams('message_id', 0);
            $message = '';
            if ($message_id) {
                $message = HALOMessageModel::find($message_id);
                if ($message) {
                    $message = '"' . $message->getMessage() . '"';
                }
            }
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();
            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s sent you a message'), $actorsText);
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.messageNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $target, 'messageText' => $message))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.messageNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $target, 'messageText' => $message))->fetch();
            $mailqueue->template = 'emails/layout';
        }
    }

    /**
     * Event handler to generate notification for new friend request action
     * 
     * @param  array $from 
     * @param  HALOUserModel $to   
     * @return HALONotificationAPI       
     */
    public function onFriendRequest($from, $to)
    {
        //prepare data
        $options = array();
        $options['action'] = 'friend.request';
        $options['context'] = $to->getContext();
        $options['target_id'] = $to->id;
        $params = HALOParams::getInstance();

        $options['params'] = $params->toString();
        //receivers
        $options['receivers'] = $to->id;

        return HALONotificationAPI::addNewOrExist($options, false);
    }

    /**
     * Event handler to render message create notification
     * 
     * @param  string $notification 
     */
    public function renderFriendRequest($notification)
    {
        $attachment = new stdClass();
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s sent a friend request'), $actorsHtml);
            //render response actions
            $attachment->content = HALOUIBuilder::getInstance('', 'friend.requestResponse', array('user' => end($actors)))->fetch();

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render email notification content for friend request action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue         
     */
    public function renderEmailFriendRequest($notification, $to, HALOMailqueueModel &$mailqueue)
    {
        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);
        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s sent a friend request'), $actorsText);
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.friendRequestNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $to))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.friendRequestNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $to))->fetch();
            $mailqueue->template = 'emails/layout';
        }
    }

    /**
     * Event handler to generate notification for friend request approve action
     * 
     * @param  array $from 
     * @param  HALOUserModel $to   
     * @return HALONotificationAPI       
     */
    public function onFriendApprove($from, $to)
    {
        //prepare data
        $options = array();
        $options['action'] = 'friend.approve';
        $options['context'] = $from->getContext();
        $options['target_id'] = $from->id;
        $params = HALOParams::getInstance();

        $options['params'] = $params->toString();
        //receivers
        $options['receivers'] = $from->id;

        $rtn = HALONotificationAPI::add($options, true);

        if ($rtn) {
            //notificaiton two way
            $options['actors'] = $from->id;
            $options['context'] = $to->getContext();
            $options['target_id'] = $to->id;
            $options['receivers'] = $to->id;
            $rtn = HALONotificationAPI::add($options, true);
        }
        //remove the old friend request notification
        $request = HALONotificationModel::where('action', 'friend.request')
            ->where('context', $to->getContext())
            ->where('target_id', $to->id)
            ->where('actors', $from->id)
            ->get()    ->first();
        if ($request) {
            DB::table('halo_notification_receivers')
                    ->where('notification_id', '=', $request->id)
                    ->delete();
            $request->delete();
        }
        return $rtn;
    }

    /**
     * Event handler torender message create notification
     * 
     * @param  string   $notification          
     */
    public function renderFriendApprove($notification)
    {
        $attachment = new stdClass();
                //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                    //could not find the comment target. Treat the target is invalid
            return false;
        }

        $my = HALOUserModel::getUser();
        $actorIds = explode(',', $notification->actors);

                //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                    //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('You are now friends with %s'), $actorsHtml);
                    //render comment message as the notification content
            $attachment->content = '';

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render email notification content for friend approval action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue    
     */
    public function renderEmailFriendApprove($notification, $to, HALOMailqueueModel &$mailqueue)
    {
                //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                    //could not find the comment target. Treat the target is invalid
            return false;
        }

        $my = HALOUserModel::getUser();
        $actorIds = explode(',', $notification->actors);

                //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                    //wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('You are now friends with %s'), $actorsText);
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.friendApproveNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $to))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.friendApproveNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $to))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Event handler to generate notification for friend request approve action
     * 
     * @param  array        $from 
     * @param  HALOUserModel  $to   
     * @return HALONotificationAPI   
     */
    public function onFriendReject($from, $to)
    {
                //prepare data
        $options = array();
        $options['action'] = 'friend.reject';
        $options['context'] = $from->getContext();
        $options['target_id'] = $from->id;
        $params = HALOParams::getInstance();

        $options['params'] = $params->toString();
                //receivers
        $options['receivers'] = $from->id;

        $rtn = HALONotificationAPI::add($options, true);

                //remove the old friend request notification
        $request = HALONotificationModel::where('action', 'friend.request')
                    ->where('context', $to->getContext())
                    ->where('target_id', $to->id)
                    ->where('actors', $from->id)
                    ->get()            ->first();
        if ($request) {
            DB::table('halo_notification_receivers')
                            ->where('notification_id', '=', $request->id)
                            ->delete();
            $request->delete();
        }
        return $rtn;
    }

    /**
     * Event handler to render message create notification
     * 
     * @param  string $notification  
     */
    public function renderFriendReject($notification)
    {
        $attachment = new stdClass();
                        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $my = HALOUserModel::getUser();
        $actorIds = explode(',', $notification->actors);

                        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s rejected your friend request'), $actorsHtml);
                            //render comment message as the notification content
            $attachment->content = '';

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render email notification content for friend reject action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue         
     */
    public function renderEmailFriendReject($notification, $to, HALOMailqueueModel &$mailqueue)
    {
                        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $actorIds = explode(',', $notification->actors);

                        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                            //wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s rejected your friend request'), $actorsText);
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.friendRejectNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $to))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.friendRejectNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $to))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Event handler to generate notification on metion action
     * 
     * @param  array $mentionList 
     * @param  array $target      
     * @return HALONotificationAPI              
     */
    public function onMention($mentionList, $target)
    {
                        //prepare data
        $options = array();
        $options['action'] = 'system.mention';
        $options['context'] = $target->getContext();
        $options['target_id'] = $target->id;
        $params = HALOParams::getInstance();

        $options['params'] = $params->toString();
                        //receivers
        $options['receivers'] = $mentionList;

        $rtn = HALONotificationAPI::add($options, false);

        return $rtn;
    }

    /**
     * Event handler to render notification for on mention action
     * 
     * @param  string $notification 
     */
    public function renderMention($notification)
    {
        $attachment = new stdClass();
                        //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                            //could not find the comment target. Treat the target is invalid
            return false;
        }

        $my = HALOUserModel::getUser();
        $actorIds = explode(',', $notification->actors);

                        //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                            //wrong format activity, just do nothing to skip it
            return false;
        } else {
            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors))->fetch();
            $attachment->headline = sprintf(__halotext('%s mentioned you in a %s'), $actorsHtml, $target->getNotifContent());
                        //render comment message as the notification content
            $attachment->content = '';

        }
        $notification->attachment = $attachment;
    }

    /**
     * Event handler to render email notificatiion content memtion action
     * 
     * @param  string           $notification 
     * @param  HALOUserModel      $to           
     * @param  HALOMailqueueModel $mailqueue         
     */
    public function renderEmailMention($notification, $to, HALOMailqueueModel &$mailqueue)
    {
                    //prepare data
        $target = $notification->getTarget();
        if (empty($target->id)) {
                        //could not find the comment target. Treat the target is invalid
            return false;
        }

        $my = HALOUserModel::getUser();
        $actorIds = explode(',', $notification->actors);

                    //init actors model
        $actors = HALOUserModel::init($actorIds);

        if (empty($actors)) {
                        //wrong format activity, just do nothing to skip it
            return false;
        } else {

            $actorsHtml = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to))->fetch();
            $actorsText = HALOUIBuilder::getInstance('', 'user.list_inline', array('users' => $actors, 'my' => $to, 'textOnly' => true))->fetch();

            $mailqueue->to = $to->getEmail();
            $mailqueue->subject = sprintf(__halotext('%s mentioned you in a %s'), $actorsText, $target->getDisplayName());
            $mailqueue->plain_msg = HALOUIBuilder::getInstance('', 'email.mentionNotif_plain', array('toName' => '', 'actorsText' => $actorsText, 'target' => $target))->fetch();
            $mailqueue->html_msg = HALOUIBuilder::getInstance('', 'email.mentionNotif_html', array('toName' => '', 'actor' => array_shift($actors), 'actorsText' => $actorsText, 'actorsHtml' => $actorsHtml, 'target' => $target))->fetch();
            $mailqueue->template = 'emails/layout';

        }
    }

    /**
     * Evnet handler to render notification 
     * 
     * @param  string $notification 
     */
    public function onNotificationRender($notification)
    {
        switch ($notification->action) {
            case 'comment.create':
                        //prepare data
                $this->renderCommentAdding($notification);
                break;
            case 'system.like':
                        //prepare data
                $this->renderLike($notification);
                break;
            case 'system.tag':
                        //prepare data
                $this->renderTagUser($notification);
                break;
            case 'system.mention':
                        //prepare data
                $this->renderMention($notification);
                break;
            case 'system.message':
                        //prepare data
                $this->renderMessageAdding($notification);
                break;
            case 'friend.request':
                        //prepare data
                $this->renderFriendRequest($notification);
                break;
            case 'friend.approve':
                        //prepare data
                $this->renderFriendApprove($notification);
                break;
            case 'friend.reject':
                        //prepare data
                $this->renderFriendReject($notification);
                break;
            default:

        }

    }

    /**
     * Event  handler to render notification
     * 
     * @param  string 			$notification 
     * @param  HALOUserModel  	$to           
     * @param  HALOMailqueueModel $mailqueue    
     */
    public function onNotificationRenderEmail($notification, $to, $mailqueue)
    {
        switch ($notification->action) {
            case 'comment.create':
                        //prepare data
                $this->renderEmailCommentAdding($notification, $to, $mailqueue);

                break;
            case 'system.like':
                        //prepare data
                $this->renderEmailLike($notification, $to, $mailqueue);
                break;
            case 'system.tag':
                        //prepare data
                $this->renderEmailTagUser($notification, $to, $mailqueue);
                break;
            case 'system.mention':
                        //prepare data
                $this->renderEmailMention($notification, $to, $mailqueue);
                break;
            case 'system.message':
                        //prepare data
                $this->renderEmailMessageAdding($notification, $to, $mailqueue);
                break;
            case 'friend.request':
                        //prepare data
                $this->renderEmailFriendRequest($notification, $to, $mailqueue);
                break;
            case 'friend.approve':
                        //prepare data
                $this->renderEmailFriendApprove($notification, $to, $mailqueue);
                break;
            case 'friend.reject':
                        //prepare data
                $this->renderEmailFriendReject($notification, $to, $mailqueue);
                break;
            default:

        }

    }
}
