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

class HALOCommentAPI
{
    protected static $_data = array();

    /**
     * add a new comment
     * 
     * @param array $data
     * @param target model $target
     * @return   [description]
     */
    public static function add($data, $target)
    {
        $default = array('message' => '',
            'tagged_list' => '',
            'params' => '');
        $data = array_merge($default, $data);

		if(!method_exists($target, 'comments')){
			return false;
		}
        //trigger before adding activity event
        if (Event::fire('comment.onBeforeAdding', array($data, $target), true) === false) {
            //error occur, return
            return false;
        }
        //check permission
        if (!HALOAuth::can('comment.create', $target)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
            return false;
        }

        //prepare data
        $comment = new HALOCommentModel();
        //actor is the current user
        $user = HALOUserModel::getUser();
        $comment->actor_id = $user->user_id;

        $comment->bindData($data);
        //validate data
        if ($comment->bindData($data)->validate()->fails()) {

            $msg = $comment->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        } else {
            //parse link data
            HALOUtilHelper::parseShareLink($data, $comment);
			
			//store attached photo
			if(isset($data['photo_id'])){
				$comment->setParams('photo_id',intval($data['photo_id']));
			}
            //find the last sibling node
            $comments = $target->comments()->get();
            $lastComment = $comments->last();
            //save polymorphic relationship first
            $target->comments()->save($comment);

            //mark the target as updated
            $target->touch();

            //then update the nested table
            if ($lastComment) {
                $comment->makeSiblingOf($lastComment);
            } else {
                // this the first comment of the target, just put it as the child of root node
                $root = HALOCommentModel::root();
                $comment->makeChildOf($root);
            }

            //add current user to the follower list of the target
            HALOFollowAPI::follow($target);

            //process mention on the message
            HALOMentionAPI::process($comment->message, $comment);

            //also add mentioned users to the follower list of the target
            $mentionList = HALOMentionAPI::process($comment->message, $comment);
            if (!empty($metionList)) {
                HALOFollowAPI::follow($target, $mentionList);
            }
			
			//store display actor
			if(isset($data['display_context']) && isset($data['display_id'])){
				$displayActor = HALOModel::getCachedModel($data['display_context'], $data['display_id']);
				//must have permission to store display actor
				if($displayActor && HALOAuth::can($data['display_context'] . '.edit', $displayActor)) {
					HALOActorListHelper::addActorToColumn($comment, 'actor_list', $comment->actor_id, $displayActor);
					$comment->save();
				}
			}

            //trigger event on comment submitted
            Event::fire('comment.onAfterAdding', array($comment, $target));
            //on activity added, add its reference as HALOResponse data
            HALOResponse::setData('comment', $comment);
            return true;

        }

    }

}
