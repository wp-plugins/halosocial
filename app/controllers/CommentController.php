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

class CommentController extends BaseController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handle to submit a new comment
     * @param  string $postData
     * @return JSON
     */
    public function ajaxSubmit($postData)
    {
        //check if this is edit comment submit
        if (!isset($postData['target_id']) && isset($postData['comment_id'])) {
            //redirect to ajaxSaveEdit
            return $this->ajaxEdit($postData);
        }
        //get the target object
        $target_id = $postData['target_id'];
        $context = $postData['context'];

        $target = HALOModel::getCachedModel($context, $target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this comment'))
            ->when(!$target || !method_exists($target, 'comments'))
            ->apply();

        //@todo: check permission

        if (!HALOCommentAPI::add($postData, $target)) {
            return HALOResponse::sendResponse();
        } else {
            $comment = HALOResponse::getData('comment');
            //update zone
            HALOResponse::beforeZone('comment_input.' . $target->getZone(), HALOUIBuilder::getInstance('', 'comment.entry', array('comment' => $comment, 'zone' => $comment->getZone()))->fetch());
            return HALOResponse::sendResponse();
        }
    }

    /**
     * ajax handle to view all comments of a target
     *
     * @param  string $target_id
     * @param  string $context
     * @return JSON
     */
    public function ajaxViewAll($target_id, $context)
    {

        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this comment'))
            ->when(!$target || !method_exists($target, 'comments'))
            ->apply();

        //load comments
        $target->comments()->get()->load('actor', 'likes');

        //update zone
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'comment.wrapper', array('target' => $target, 'limit' => $target->comments->count(),
            'zone' => 'comment.' . $target->getZone()))->fetch());

        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to delete a comment
     * @param  string $commentId
     * @return JSON
     */
    public function ajaxDelete($commentId)
    {
        $comment = HALOCommentModel::find($commentId);

        //check input data
        Redirect::ajaxError(__halotext('Invalid comment'))
            ->when(is_null($comment))
            ->apply();

        //check permission
        Redirect::ajaxError(__halotext('Permission denied'))
            ->when(!HALOAuth::can('comment.edit', $comment))
            ->apply();

        //delete the comment
        $zone = $comment->getZone();
        $comment->delete();
        //update zone
        HALOResponse::removeZone($zone);
        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to show edit comment
     * @param  string $commentId
     * @return JSON
     */
    public function ajaxShowEdit($commentId)
    {
        $my = HALOUserModel::getUser();

        $comment = HALOCommentModel::find($commentId);

        //check input data
        Redirect::ajaxError(__halotext('Invalid comment'))
            ->when(is_null($comment))
            ->apply();

        //check permission
        Redirect::ajaxError(__halotext('Permission denied'))
            ->when(!HALOAuth::can('comment.edit', $comment))
            ->apply();

        //delete the comment
        $target = $comment->commentable;
        Redirect::ajaxError(__halotext('Invalid target for this comment'))
            ->when(is_null($target))
            ->apply();

        $builder = HALOUIBuilder::getInstance('', 'comment.edit', array('id' => $comment->id, 'actor' => $my,
            'zone' => 'comment_edit.' . $comment->getZone(),
            'comment' => $comment));

        //update zone
        HALOResponse::afterZone($comment->getZone(), $builder->fetch());
        //init edit form
        HALOResponse::addScriptcall('halo.comment.showEdit', $comment->id);
        return HALOResponse::sendResponse();

    }
	
    /**
     * ajax handle to remove photo attachment
     * @param  string $commentId
     * @return JSON
     */
    public function ajaxRemovePhoto($commentId)
    {
        $my = HALOUserModel::getUser();

        $comment = HALOCommentModel::find($commentId);

        //check input data
        Redirect::ajaxError(__halotext('Invalid comment'))
            ->when(is_null($comment))
            ->apply();

        //check permission
        Redirect::ajaxError(__halotext('Permission denied'))
            ->when(!HALOAuth::can('comment.edit', $comment))
            ->apply();

        //delete the comment
        $target = $comment->commentable;
        Redirect::ajaxError(__halotext('Invalid target for this comment'))
            ->when(is_null($target))
            ->apply();

		$comment->clearParams('photo_id');
		$comment->save();
		
        return HALOResponse::sendResponse();

    }
	
    /**
     * ajax handle to save edited comment
     * @param  string $postData
     * @return JSON
     */
    public function ajaxEdit($postData)
    {
        $commentId = $postData['comment_id'];
        $comment = HALOCommentModel::find($commentId);
        //validate post data
        Redirect::ajaxError(__halotext('The comment does not exists'))
            ->when(is_null($comment))
            ->apply();
        //validate data
        if ($comment->bindData($postData)->validate()->fails()) {

            $error = $comment->getValidateMessages();
            Redirect::ajaxError($error)
                ->apply();
        } else {
            HALOUtilHelper::parseShareLink($postData, $comment);
            $comment->save();
            //update zone
            HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'comment.entry', array('comment' => $comment, 'zone' => $comment->getZone()))->fetch());
            HALOResponse::addScriptCall('halo.comment.doneEdit', $comment->id);
            return HALOResponse::sendResponse();

        }

    }

    /**
     *  handler for delete activity action
     *
     * @param  string $act
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

}
