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

class HALOSearch
{

    public static $instance = null;
    public static $client = null;

    public $query = null;
    public $filter = null;
    public $index = '';
    protected $sort = array();
    protected $from = 0;
    protected $size = 12;
    protected $orderBy = array();
    protected $model = null;

    public function __construct()
    {
        $this->query = HALOSearchQuery::getInstance('bool');
        $this->filter = HALOSearchQuery::getInstance('bool');
    }
    /**
     * get Instance 
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $instance = new HALOSearch();
            self::$instance = $instance;
        }
        return self::$instance;
    }
    /**
     * check Instance
     * 
     * @return bool
     */
    public static function hasInstance()
    {
        return !is_null(self::$instance);
    }

    /**
     * set from atrribute of query
     * 
     * @param  int $from
     * @return HALOSearch
     */
    public function skip($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * set size attribute of query
     * 
     * @param  int $size
     * @return HALOSearch
     */
    public function take($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     *return pagination size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * add ordering setting
     * 
     * @param  mixed $col
     * @param  string $direction
     * @return HALOSearch
     */
    public function orderBy($col, $direction = 'desc')
    {
        $this->orderBy[] = array($col => $direction);
        return $this;
    }

    /**
     * set index to search
     * 
     * @param string $index
     * @return  HALOSearch
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * function to build elasticsearch params
     * 
     * @return array
     */
    private function buildParams()
    {
        $filtered = array();
        if ($query = $this->query->getQuery()) {
            $filtered['query'] = $query;
        }
        if ($filter = $this->filter->getQuery()) {
            $filtered['filter'] = $filter;
        }
        $params = array(
            'from' => $this->from,
            'size' => $this->size,
        );
        //query
        if (!empty($filtered)) {
            $params['query'] = array('filtered' => $filtered);
        }
        //sort
        if (!empty($this->sort)) {
            $params['sort'] = $this->sort;
        }
        return $params;

    }

    /**
     * init elaticsearch client settings
     * 
     * @return Elasticsearch\Client
     */
    public static function init()
    {
        if (is_null(self::$client)) {
            $params = array();
            $params['hosts'] = array(
                HALOConfig::get('se.hosts'), //'localhost:9200'
            );
            self::$client = new Elasticsearch\Client($params);
        }
    }


    /**
     * function to call elasticsearch API
     */
    public function search()
    {
        HALOSearch::init();
        //$result = HALOSearch::$client->search($this->buildParams());
        $params = array();
        $params['index'] = $this->index;
        $params['body'] = $this->buildParams();

        $results = HALOSearch::$client->search($params);

        return $results;
    }

    /**
     * return paginator object from this search
     * 
     * @param  mixed $model 
     * @return Illuminate\Pagination\Paginator
     */
    public function paginate($model)
    {

        $env = $model->getQuery()->getConnection()->getPaginator();
        $se = HALOSearch::getInstance();
        $results = $se->search();
        //process results
        $itemIds = array();
        foreach ($results['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            $itemIds[] = $source['id'];
        }
        if ($itemIds) {
            $collection = $model->whereIn('id', $itemIds)->get();
            //keep the result ordering
            $collection = HALOUtilHelper::sortCollectionByArray($collection, (array) $itemIds);
        }
        $total = $results['hits']['total'];
        $perPage = $se->getSize();

        //build paginator object from the result
        $rtn = new Illuminate\Pagination\Paginator($env, $collection->all(), $total, $perPage);

        return $rtn;
    }


    /**
     * function to set expected return model of search result
     * 
     * @param mixed $model
     * @return   HALOSearch
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }


    /**
     * add sort condition
     * 
     * @param  string $val
     * @return HALOSearch
     */
    public function sort($val)
    {
        $this->sort[] = $val;

        return $this;
    }

}

class HALOSearchQuery
{
    protected $query;
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
        $this->query = array();
    }
    /**
     * get Instance
     * 
     * @param  string $type
     * @return HALOSearchQuery
     */
    public static function getInstance($type = 'bool')
    {
        return new HALOSearchQuery($type);
    }
    /**
     * 
     * @param  string $val
     * @return HALOSearchQuery
     */
    public function must($val)
    {
        if (!isset($this->query['must'])) {
            $this->query['must'] = array();
        }
        $this->query['must'][] = $val;
        return $this;
    }
    /**
     * 
     * @param  string $val
     * @return HALOSearchQuery
     */
    public function should($val)
    {
        if (!isset($this->query['should'])) {
            $this->query['should'] = array();
        }
        $this->query['should'][] = $val;

        return $this;
    }
    /**
     * 
     * @param  string $val
     * @return HALOSearchQuery
     */
    public function mustNot($val)
    {
        if (!isset($this->query['must_not'])) {
            $this->query['must_not'] = array();
        }
        $this->query['must_not'][] = $val;

        return $this;
    }
    /**
     * 
     * @param  string $val
     * @return HALOSearchQuery
     */
    public function shouldNot($val)
    {
        if (!isset($this->query['should_not'])) {
            $this->query['should_not'] = array();
        }
        $this->query['should_not'][] = $val;

        return $this;
    }
    /**
     * get Query
     * 
     * @return array
     */
    public function getQuery()
    {
        if (!isset($this->query['must']) && !isset($this->query['must_not']) && !isset($this->query['should']) && !isset($this->query['should_not'])) {
            return null;
        }
        return array($this->type => $this->query);
    }
}
