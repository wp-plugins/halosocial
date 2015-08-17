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

class ReviewController extends BaseController
{

    /**
     * Initializer.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ajax handle to submit a new review
     *
     * @param  string $context
     * @param  int $target_id
     * @param  array $postData
     * @return JSON
     */
    public function ajaxSubmit($context, $target_id, $postData)
    {
        $my = HALOUserModel::getUser();
        if (!$my) {
            return HALOResponse::login(__halotext('Login required'));
        }

        $target = HALOModel::getCachedModel($context, $target_id);
        if (!$target) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Target')));
        }

        if (!isset($postData['confirm'])) {
            //show the review form
            //show the form
            $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
                ->addUI('confirm', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'confirm', 'value' => 1)))
                ->addUI('rating', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'rating', 'title' => __halotext('Rating'), 'value' => '', 'validation' => 'required',
                'options' => array(array('value' => '', 'title' => __halotext('Select Rating')),
                    array('value' => 1, 'title' => __halotext('1 star')),

                    array('value' => 2, 'title' => __halotext('2 stars')),

                    array('value' => 3, 'title' => __halotext('3 stars')),

                    array('value' => 4, 'title' => __halotext('4 stars')),
                    array('value' => 5, 'title' => __halotext('5 stars'))

                ))))
                ->addUI('message', HALOUIBuilder::getInstance('', 'form.textarea', array('name' => 'message', 'value' => $target->message, 'class' => 'haloTextAreaAutoSzie',
                'title' => __halotext('Comment'), 'validation' => 'required', 'data' => array('provide' => 'markdown', 'iconlibrary' => 'fa', 'height' => '60')
            )))
            ;
            $title = __halotext('Add your review');
            $content = $builder->fetch();
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Save'), "onclick" => "halo.review.submit('" . $context . "','" . $target_id . "')", "icon" => "check", 'class' => 'halo-btn-primary'));
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm')
            ;
            return HALOResponse::sendResponse();
        } else {
            if (!HALOReviewAPI::add($postData, $target)) {
                return HALOResponse::sendResponse();
            } else {
                $review = HALOResponse::getData('review');
                HALOResponse::addScriptCall('halo.popup.close');
                //flush all auth cache data to make sure the auth is updated
                HALOAuth::flushCache();
                //update zone
                HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.rating', array('target' => $target, 'showAdd' => 1))->fetch());
                HALOResponse::insertZone('reviews.' . $target->getZone(), HALOUIBuilder::getInstance('', 'review.item', array('review' => $review, 'zone' => $review->getZone()))->fetch(), 'first');

                if (!HALOAuth::can('review.create', $target)) {
                    HALOResponse::addScriptCall('halo.review.removeReviewBtn');
                }
                HALOResponse::addScriptCall('halo.review.haveReviews', 1);
                HALOResponse::refresh();
                return HALOResponse::sendResponse();
            }
        }
    }

    /**
     * ajax handle to edit review
     *
     * @param  int $reviewId
     * @param  array $postData
     * @return JSON
     */
    public function ajaxEdit($reviewId, $postData)
    {
        $review = HALOReviewModel::find($reviewId);
        if (!$review) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Review')));
        }

        if (!isset($postData['confirm'])) {
            //show the review form
            //show the form
            $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
                ->addUI('confirm', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'confirm', 'value' => 1)))
                ->addUI('id', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'id', 'value' => $reviewId)))
                ->addUI('rating', HALOUIBuilder::getInstance('', 'form.select', array('name' => 'rating', 'title' => __halotext('Rating'), 'value' => $review->rating,

                'options' => array(array('value' => 1, 'title' => __halotext('1 star')),

                    array('value' => 2, 'title' => __halotext('2 stars')),

                    array('value' => 3, 'title' => __halotext('3 stars')),

                    array('value' => 4, 'title' => __halotext('4 stars')),

                    array('value' => 5, 'title' => __halotext('5 stars'))

                ))))
                ->addUI('message', HALOUIBuilder::getInstance('', 'form.textarea', array('name' => 'message', 'value' => $review->message, 'class' => 'haloTextAreaAutoSzie',
                'title' => __halotext('Comment'), 'validation' => 'required', 'data' => array('provide' => 'markdown', 'iconlibrary' => 'fa', 'height' => '60')
            )))
            ;
            $title = __halotext('Edit your review');
            $content = $builder->fetch();
            $actionSave = HALOPopupHelper::getAction(array("name" => __halotext('Save'), "onclick" => "halo.review.edit('" . $reviewId . "')", "icon" => "check", 'class' => 'halo-btn-primary'));
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', $actionSave)
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm')
            ;
            return HALOResponse::sendResponse();
        } else {
            if (!HALOReviewAPI::edit($postData, $review)) {
                return HALOResponse::sendResponse();
            } else {
                $review = HALOResponse::getData('review');
                HALOResponse::addScriptCall('halo.popup.close');
                //update zone
                HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.rating', array('target' => $review->reviewable, 'showAdd' => 1))->fetch());
                HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.item', array('review' => $review, 'zone' => $review->getZone()))->fetch());
                HALOResponse::refresh();
                return HALOResponse::sendResponse();
            }
        }
    }

    /**
     * ajax handle to approve review
     *
     * @param  int $reviewId
     * @param  array $postData
     * @return JSON
     */
    public function ajaxApprove($reviewId, $postData)
    {
        $review = HALOReviewModel::find($reviewId);
        if (!$review) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Unknown Review')));
        }
        if (!HALOReviewAPI::approve($review)) {
            return HALOResponse::sendResponse();
        } else {
            $review = HALOResponse::getData('review');
            //update zone
            HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.rating', array('target' => $review->reviewable, 'showAdd' => 1))->fetch());
            HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.item', array('review' => $review, 'zone' => $review->getZone()))->fetch());
            return HALOResponse::sendResponse();
        }
    }

    /**
     * ajax handle to view all reviews of a target
     *
     * @param  int $target_id
     * @param  string $context
     * @return JSON
     */
    public function ajaxViewAll($target_id, $context)
    {

        //get the target object
        $target = HALOModel::getCachedModel($context, $target_id);

        //validate post data
        Redirect::ajaxError(__halotext('Could not find a target object for this comment'))
            ->when(!$target || !method_exists($target, 'reviews'))
            ->apply();

        //load comments
        $target->reviews(true)->get()->load('actor', 'likes');

        //update zone
        HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.list', array('target' => $target, 'limit' => $target->reviews(true)->count(),
            'zone' => 'reviews.' . $target->getZone()))->fetch());

        return HALOResponse::sendResponse();

    }

    /**
     * ajax handle to delete a review
     *
     * @param  int $reviewId
     * @param  array $postData
     * @return JSON
     */
    public function ajaxDelete($reviewId, $postData)
    {
        $review = HALOReviewModel::find($reviewId);

        //check permission
        if (!$review || !HALOAuth::can('review.delete', $review)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission denied')));
            return HALOResponse::sendResponse();
        }
        $title = __halotext('Delete Review');
        if (!isset($postData['confirm'])) {
            //sho confirm dialog
            $builder = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'popupForm'))
                ->addUI('confirm', HALOUIBuilder::getInstance('', 'form.hidden', array('name' => 'confirm', 'value' => 1)));
            $content = $builder->fetch();
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setMessage', __halotext('Are you sure you want to delete this review?'), 'error')
                ->addScriptCall('halo.popup.setFormContent', $content)
                ->addScriptCall('halo.popup.addFormAction', '{"name": "' . __halotext('Yes') . '","onclick": "halo.review.deleteMe(\'' . $reviewId . '\')","href": "javascript:void(0);"}')
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm');
            return HALOResponse::sendResponse();

        }
        //delete the comment
        $zone = $review->getZone();
        $target = $review->reviewable;
        if (!HALOReviewAPI::delete($review)) {
            return HALOResponse::sendResponse();
        } else {
            $message = __halotext('Your review has been deleted');
            HALOResponse::addScriptCall('halo.popup.reset')
                ->addScriptCall('halo.popup.setFormTitle', $title)
                ->addScriptCall('halo.popup.setMessage', $message, 'error', true)
                ->addScriptCall('halo.popup.addFormAction', '{"name": "' . __halotext('Done') . '","onclick": "halo.popup.close()","href": "javascript:void(0);"}')
                ->addScriptCall('halo.popup.addFormActionCancel')
                ->addScriptCall('halo.popup.showForm');

            //update zone
            HALOResponse::removeZone($zone);
            HALOResponse::updateZone(HALOUIBuilder::getInstance('', 'review.rating', array('target' => $target, 'showAdd' => 1))->fetch());
            if (!$target->getReviews(0)) {
                HALOResponse::addScriptCall('halo.review.haveReviews', 0);
            }
			HALOResponse::refresh();
            return HALOResponse::sendResponse();
        }
    }

}
