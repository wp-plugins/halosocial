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

class HALOHashTagAPI
{
    const MAX_WORDS = 2;
    /*
    @api: check if the message has hash data
    @params: message => message to be checked
    target => hash tag target if exits
     */
    /**
     * @api: check if the message has hash data
     * 
     * @param  message => message to be checked
     * @param  object $target  target => hash tag target if exits
     * @return bool
     */
    public static function process($message, $target)
    {
        //only process taggable target
        if (!method_exists($target, 'hashtags')) {
            return false;
        }
        $tagIds = array();
        $tagNameList = self::parseMessage($message);
        //limit number of hashtag per target
        if (!empty($tagNameList) && count($tagNameList) > 20) {
            $tagNameList = array_splice($tagNameList, 0, 20);
        }
        foreach ($tagNameList as $tagName) {
            $tag = HALOHashTagModel::firstOrCreate(array('name' => mb_strtolower($tagName, 'UTF-8')));
            $tagIds[] = $tag->id;
        }
        $currentTagIds = $target->hashtags->lists('id');
        //merge old and new tag list
        $tagIds = array_unique(array_merge($currentTagIds, $tagIds));
        //store tags to database
        if ($tagIds) {
            $target->hashtags()->sync($tagIds);
            //update hashtags attribute
            $target->hashtags = $target->hashtags()->get();
            //Event::fire('hashtag.onTagging',array($tagIds,$target));
        }

        return true;
    }

    /**
     * function to parse on a message to get a hash tag list. 
     * Rule: hashtag is only appear at the end of the message
     * 
     * @param  string $message
     * @return array
     */
    public static function parseMessage($message)
    {

        $tagList = array();
        //inline hashtag only single word
        //$count = preg_match_all("/(#\w+)/", $message, $matches);
        $count = preg_match_all("/(#[\p{L}0-9_&\+\-]+)/u", $message, $matches);
        if ($count) {
            foreach ($matches[0] as $match) {
                $tagList[] = substr($match, 1);
            }
            return $tagList;
        } else {
            return array();
        }

        /*
    $tagList = array();
    if(strpos($message,'#') === false){
    return $tagList;
    }

    //split message to words
    $wordList = explode('#', $message);

    while((count($wordList) > 1) && ($tagName = array_pop($wordList)) && strlen($tagName) > 0 && str_word_count(trim($tagName)) <= self::MAX_WORDS){
    $tagList[] = trim($tagName);
    }
    return $tagList;
     */
    }

    /**
     * render message with hashtag
     * 
     * @param  string $message message: the message to be rendered
     * @param  object $target  target: the target object to check for hash tag
     * @return string
     */
    public static function renderMessage($message, $target)
    {
        //only process taggable target
        if (!method_exists($target, 'hashtags') || !method_exists($target, 'getContext')) {
            return $message;
        }

        //$tagList = self::parseMessage($message);
        $tag = new HALOHashTagModel();
        $context = $target->getContext();
        $message = preg_replace_callback('/(#[\p{L}0-9_&\+\-]+)/u', function ($matches) use ($context, $tag) {
            $tag->name = substr($matches[0], 1);
            $tagLink = $tag->getHashLink($context);
            return $tagLink;
        },
            $message
        );

        return $message;

        $startWithHash = false;
        $hashPos = strpos($message, '#');

        if ($hashPos === false) {
            return $message;
        }

        if ($hashPos === 0) {
            $startWithHash = true;
        }
        $wordList = explode('#', $message);
        $tags = $target->hashtags;
        $tagList = $tags->lists('name');
        $tagLinks = array();
        $context = $target->getContext();

        $tagName = array_pop($wordList);
        while (strlen($tagName) > 0 && str_word_count(trim($tagName)) <= self::MAX_WORDS) {
            $index = array_search(mb_strtolower(trim($tagName), 'UTF-8'), $tagList);
            if ($index !== false && isset($tags[$index])) {
                $tagLinks[] = $tags[$index]->getHashLink($context);
            } else {
                //tag not found, push back
                $tagLinks[] = '#' . $tagName;
            }
            $tagName = array_pop($wordList);
        }
        if (strlen($tagName) > 0 && count(explode(' ', $tagName)) > self::MAX_WORDS) {
            //insert the last word to word list
            $wordList[] = $tagName;
        }

        if (!empty($tagLinks)) {
            //tagLinks needs to be reversed to be displayed
            $tagLinks = array_reverse($tagLinks);
            $prefix = implode('#', $wordList);
            if ((substr($prefix, 0, 1) == '#') && !$startWithHash) {
                //skip the hash character
                $prefix = substr($prefix, 1);
            }
            $message = $prefix . implode(' ', $tagLinks);
        }
        return $message;

    }
}
