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

class HALOReviewAPI
{
    protected static $_data = array();

    /**
     * @api: add a new review
     * 
     * @param array $data
     * @param object target model $target
     * @return  bool
     */
    public static function add($data, $target)
    {
        $default = array('message' => '',
            'rating' => '',
            'params' => '');
        $data = array_merge($default, $data);

        //trigger before adding activity event
        if (Event::fire('review.onBeforeAdding', array($data, $target), true) === false) {
            //error occur, return
            return false;
        }
        //check permission
        if (!HALOAuth::can('review.create', $target)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
            return false;
        }
        //prepare data
        $review = new HALOReviewModel();
        //actor is the current user
        $user = HALOUserModel::getUser();
        $review->actor_id = $user->user_id;

        //validate data
        if ($review->bindData($data)->validate()->fails()) {

            $msg = $review->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        } else {
            //setup moderate state
            if (HALOConfig::get('review.moderate') && !HALOAuth::can('review.approve')) {
                $review->setModerating();
            } else {
                $review->setModerated();
            }
            //save polymorphic relationship first
            $target->reviews()->save($review);

            //update the target rating point
            $rating = $target->reviews()->avg('rating');
            $target->rating = is_null($rating) ? 0 : $rating;
            $target->review_count = $target->reviews()->count();
            $target->review_id = (empty($target->review_id) && ($target->review_count)) ? $target->reviews()->first()->id : 0;
            $target->save();
            //add current user to the follower list of the target and review
            HALOFollowAPI::follow($target);
            HALOFollowAPI::follow($review);

            //trigger event on review submitted
            Event::fire('review.onAfterAdding', array($target, $review));
            //on activity added, add its reference as HALOResponse data
            HALOResponse::setData('review', $review);
            return true;
        }
    }

    /**
     * @api: edit a review
     * 
     * @param array $data
     * @param object target model $target
     * @return  bool
     */
    public static function edit($data, $target)
    {
        $default = array('message' => '',
            'rating' => '',
            'params' => '');
        $data = array_merge($default, $data);

        //trigger before adding activity event
        if (Event::fire('review.onBeforeEditing', array($data, $target), true) === false) {
            //error occur, return
            return false;
        }
        //check permission
        if (!HALOAuth::can('review.edit', $target)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
            return false;
        }

        //prepare data
        $review = $target;

        //validate data
        if ($review->bindData($data)->validate()->fails()) {
            $msg = $review->getValidator()->messages();
            HALOResponse::addMessage($msg);
            return false;
        } else {
            //save polymorphic relationship first
            $review->save();

            //update the reviewable rating point
            $rating = $review->reviewable->reviews()->avg('rating');
            $review->reviewable->rating = is_null($rating) ? 0 : $rating;
            $target->reviewable->save();

            //trigger event on review submitted
            Event::fire('review.onAfterEditing', array($target, $review));
            //on activity added, add its reference as HALOResponse data
            HALOResponse::setData('review', $review);
            return true;
        }
    }

    /**
     *@api: approve a review
     *
     * @param  object review model $review
     * @return bool
     */
    public static function approve($review)
    {
        //trigger before adding activity event
        if (Event::fire('review.onBeforeApproval', array($review), true) === false) {
            //error occur, return
            return false;
        }
        //check permission
        /*
        if(!HALOAuth::can('review.approve')){
        HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
        return false;
        }
         */
        $review->setModerated();
        $review->save();

        //prepare data
        $target = $review->reviewable;

        //$target->rating = $target->reviews()->avg('rating');
        $rating = $target->reviews()->avg('rating');
        $target->rating = is_null($rating) ? 0 : $rating;
        $target->review_count = $target->reviews()->count();
        $target->review_id = (empty($target->review_id) && ($target->review_count)) ? $target->reviews()->first()->id : 0;
        $target->save();

        //trigger event on review submitted
        Event::fire('review.onAfterApproval', array($target, $review));
        //on activity added, add its reference as HALOResponse data
        HALOResponse::setData('review', $review);
        return true;
    }

    /**
     *@api: delete a review
     *
     * @param  object review model $review
     * @return bool
     */
    public static function delete($review)
    {
        //trigger before delete event
        if (Event::fire('review.onBeforeDelete', array($review), true) === false) {
            //error occur, return
            return false;
        }
        //check permission
        if (!HALOAuth::can('review.delete', $review)) {
            HALOResponse::addMessage(HALOError::failed(__halotext('Permission Denied')));
            return false;
        }

        //prepare data
        $target = $review->reviewable;
        $review->delete();
        //update target rating
        $rating = $target->reviews()->avg('rating');
        $target->rating = is_null($rating) ? 0 : $rating;
        $target->review_count = $target->reviews()->count();
        $target->review_id = (empty($target->review_id) && ($target->review_count)) ? $target->reviews()->first()->id : 0;

        $target->save();
        //trigger event on review delete
        Event::fire('review.onAfterDelete', array($target));
        return true;
    }
}
