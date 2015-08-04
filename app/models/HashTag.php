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

class HALOHashTagModel extends HALOModel
{
    public $timestamps = true;

    protected $table = 'halo_hash_tags';

    protected $fillable = array('name');

    private $validator = null;

    private $_params = null;

    /**
     * Get Validate Rule 
     * @return array
     */
    public function getValidateRule()
    {
        return array();

    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * Posts 
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function posts()
    {
        return $this->morphedByMany('HALOPostModel', 'taggable', 'halo_hash_taggables', 'tag_id');
    }

    /**
     * Posts 
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function activities()
    {
        return $this->morphedByMany('HALOActivityModel', 'taggable', 'halo_hash_taggables', 'tag_id');
    }

    //////////////////////////////////// Define Relationships /////////////////////////

    /**
     * A list of tags that ready for autocomplete rendering
     * 
     * @param  string $term         
     * @param  string $filterValues 
     * @return array
     */
    public static function getSearch($term, $filterValues = null)
    {
        $tags = HALOHashTagModel::where('name', 'like', $term . '%')->get();
        $list = array();
        foreach ($tags as $tag) {
            $obj = new stdClass();
            $obj->name = $tag->getDisplayName();
            $list[] = $obj;
        }
        return $list;
    }

    /**
     * Return display name for this tag
     * 
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * Return the name with link for display, either username of name based on backend config
     * 
     * @param  string  $class 
     * @param  boolean $brief 
     * @param  array   $attrs 
     * @return string         
     */
    public function getDisplayLink($class = '', $brief = true, $attrs = null)
    {
        $brief = false;
        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        $attrsString = '';
        if (is_array($attrs)) {
            foreach ($attrs as $attr => $value) {
                $attrsString[] = $attr . '="' . $value . '"';
            }
            $attrsString = implode(' ', $attrsString);
        }
        return '<a ' . $attrsString . ' ' . $class . ($brief ? $this->getBriefDataAttribute() : '') . 'href="' . $this->getUrl() . '">' . $this->getDisplayName() . '</a>';
    }

    /**
     * Return hash display url of this hash tag
     * 
     * @param  string  $context 
     * @param  string  $class   
     * @param  boolean $brief   
     * @return string           
     */
    public function getHashLink($context, $class = '', $brief = true)
    {
        $brief = false;
        if (!empty($class)) {
            $class = 'class="' . $class . '" ';
        }
        return '<a target="_blank" ' . $class . ($brief ? $this->getBriefDataAttribute() : '') . 'href="' . $this->getUrl($context) . '">#' . $this->getDisplayName() . '</a>';
    }

    /**
     * Return url of this hash tag
     * 
     * @param  string $context 
     * @param  array  $params  
     * @return HALOHashTagModel          
     */
    public function getUrl($context = 'post', array $params = array())
    {
        $hashEncode = urlencode(mb_strtolower($this->getDisplayName(), 'UTF-8'));
        return HALOHashTagModel::getUrlPrefix($context, $params) . $hashEncode;
    }

    /**
     * Return hashtag url prefix 
     * 
     * @param  string $context 
     * @param  array  $params  
     * @return string          
     */
    public static function getUrlPrefix($context = 'post', array $params = array())
    {
        $pString = '';
        if (!empty($params)) {
            $pString = '&' . http_build_query($params);
        }
        //hack for activity context
        if ($context == 'activity') {
            $filterPrefix = 'activity.home';
        } else {
            $filterPrefix = $context . '.listing';
        }
        $context = ($context == 'activity') ? 'stream' : $context;
        //get post status filter
        $filter = Cache::rememberForever($filterPrefix . '.hashtag.filter', function () use ($filterPrefix) {
            $filters = HALOFilter::getFilterByName($filterPrefix . '.hashtag');
            return $filters->first();
        });
        if ($filter) {
            return URL::to('?view=home&usec=' . $context . $pString . '&' . $filter->getInputName() . '=');
        } else {
            return URL::to('?view=home&usec=' . $context . $pString . '&hashtag=');
        }
    }

}
