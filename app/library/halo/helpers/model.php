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

class HALOModelHelper
{

    /**
     * array model lazy load counter of relation
     *
     * @param  Illuminate\Database\Eloquent\Collection $models
     * @param  array $relationArr
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function loadRelationFromParam(&$models, $relationArr)
    {
        if ($models->count() == 0) {
            return $models;
        }

        $relationArr = (array) $relationArr;
        if (count($relationArr) != 2) {
            return $models;
        }
        $relationParam = $relationArr['param'];
        $relationName = $relationArr['name'];
        $relationIds = array();
        $dictionary = array();
        foreach ($models as $model) {
            $idString = $model->getParams($relationParam);
            $ids = json_decode($idString);
            $dictionary[$model->id] = $ids;
            $relationIds = array_merge($relationIds, $ids);
        }

        //get relation models
        $relations = HALOModel::getCachedModel($relationName, $relationIds);

        //match models
        foreach ($models as $model) {

        }

        $model = $models->first();
        $query = $model->newQuery();
        foreach ($arr as $relationName) {
            if (!self::relationExists($model, $relationName)) {
                continue;
            }
            $relation = $query->getRelation($relationName);
            //var_dump(get_class($relation));
            $relation->addEagerConstraints($models->all());

            //get eager contraint keys and use them for grouping
            $keys = self::getEagerContraintsKeys($relation);

            //var_dump($keys);

            call_user_func_array(array(&$relation, 'groupBy'), $keys);

            $relation->select($keys);
            $relation->addSelect(DB::raw('count(*) as counter'));

            //var_dump($relation->getQuery()->getQuery()->toSql());

            $results = $relation->getQuery()->getQuery()->get();

            //var_dump($results);
            self::matchRelationCounter($models, $results, $relation, $relationName);
            //var_dump($results);
        }
        return $models;
    }

    /**
     * array model lazy load counter of relation
     * 
     * @param  Illuminate\Database\Eloquent\Collection $models $models
     * @param  array $arr
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function loadRelationCounter(&$models, $arr)
    {
        if ($models->count() == 0) {
            return $models;
        }

        //wordaround fix
        HALOAuth::hasRole('admin');

        $model = $models->first();
        $query = $model->newQuery();
        foreach ($arr as $relationName) {
            if (!self::relationExists($model, $relationName)) {
                continue;
            }
            $relation = $query->getRelation($relationName);
            $relation->addEagerConstraints($models->all());

            //get eager contraint keys and use them for grouping
            $keys = self::getEagerContraintsKeys($relation);

            call_user_func_array(array(&$relation, 'groupBy'), $keys);

            $relation->select($keys);
            $relation->addSelect(DB::raw('count(*) as counter'));

            //var_dump($relation->getQuery()->getQuery()->toSql());

            $results = $relation->getQuery()->getQuery()->get();

            self::matchRelationCounter($models, $results, $relation, $relationName);
        }
        return $models;
    }

    /**
     * match relation counter results with parent models
     * 
     * @param  Illuminate\Database\Eloquent\Collection $models 
     * @param  array $results
     * @param  object $relation 
     * @param  string $relationName 
     * @return array               
     */
    public static function matchRelationCounter(&$models, $results, $relation, $relationName)
    {
        $relationClass = get_class($relation);
        $keys = array();
        switch ($relationClass) {
            case 'Illuminate\Database\Eloquent\Relations\HasManyThrough':
                //$keys[] = $relation->getForeignKey();
                break;

            case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
            case 'Illuminate\Database\Eloquent\Relations\BelongsToMany':
            case 'Illuminate\Database\Eloquent\Relations\HasOne':
            case 'Illuminate\Database\Eloquent\Relations\HasMany':
            case 'Illuminate\Database\Eloquent\Relations\HasOneOrMany':
            case 'Illuminate\Database\Eloquent\Relations\MorphOne':
            case 'Illuminate\Database\Eloquent\Relations\MorphMany':
            case 'Illuminate\Database\Eloquent\Relations\MorphOneOrMany':
            case 'Illuminate\Database\Eloquent\Relations\MorphToMany':
            default:
                self::matchAllRelationCounter($models, $results, $relation, $relationName);
                break;

        }
        return $keys;

    }

    /**
     * match all type of relation counter results with parent models
     *
     * 
     * @param  Illuminate\Database\Eloquent\Collection $models 
     * @param  array $results
     * @param  object $relation     
     * @param  string $relationName 
     * @return int             
     */
    public static function matchAllRelationCounter(&$models, $results, $relation, $relationName)
    {
        //build dictionary
        $dictionary = array();

        $foreign = self::getPlainKey($relation->getForeignKey());

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result) {
            $dictionary[$result->{$foreign}] = $result->counter;
        }

        //set counters
        foreach ($models as $model) {
            if (isset($dictionary[$model->id])) {
                $model->setRelationCounter($relationName, $dictionary[$model->id]);
            } else {
                //default value
                $model->setRelationCounter($relationName, 0);
            }
        }
    }

    /**
     * return eager contraint keys of a relation
     * 
     * @param  object $relation
     * @return array
     */
    public static function getEagerContraintsKeys($relation)
    {
        $relationClass = get_class($relation);
        $keys = array();
        switch ($relationClass) {
            case 'Illuminate\Database\Eloquent\Relations\BelongsTo':
                $keys[] = $relation->getQualifiedOtherKeyName();
                break;
            case 'Illuminate\Database\Eloquent\Relations\BelongsToMany'://checked
                $keys[] = $relation->getForeignKey();
                break;
            case 'Illuminate\Database\Eloquent\Relations\HasOne':
            case 'Illuminate\Database\Eloquent\Relations\HasMany':
            case 'Illuminate\Database\Eloquent\Relations\HasOneOrMany'://checked
                $keys[] = $relation->getForeignKey();
                break;
            case 'Illuminate\Database\Eloquent\Relations\HasManyThrough':
                //$keys[] = $relation->getForeignKey();
                break;
            case 'Illuminate\Database\Eloquent\Relations\MorphOne':
            case 'Illuminate\Database\Eloquent\Relations\MorphMany':
            case 'Illuminate\Database\Eloquent\Relations\MorphOneOrMany'://checked
                $keys[] = $relation->getForeignKey();
                $keys[] = $relation->getMorphType();
                break;
            case 'Illuminate\Database\Eloquent\Relations\MorphToMany'://checked
                $keys[] = $relation->getForeignKey();
                $keys[] = $relation->getTable() . '.' . $relation->getMorphType();
                break;
        }
        return $keys;
    }

    /**
     * Get the plain key.
     *
     * @return string
     */
    public static function getPlainKey($key)
    {
        $segments = explode('.', $key);

        return $segments[count($segments) - 1];
    }

    /**
     * function to check if a relation exists
     * 
     * @param  object $model
     * @param  string $relationName
     * @return bool
     */
    public static function relationExists($model, $relationName)
    {
        //first check for method_exists
        if (method_exists($model, $relationName)) {
            return true;
        } else {
            //check for model resource callback
            if (method_exists(get_class($model), 'getResourceCb')) {
                return !is_null(call_user_func_array(array(get_class($model), 'getResourceCb'), array($relationName)));
            }
        }
        return false;
    }

    /**
     * remove where condition of a query
     * 
     * @param  Illuminate\Database\Query\Builder $query
     * @param  mixed $column
     * @param  mixed $operator
     * @param  mixed $value 
     * @param  string $boolean
     * @return Illuminate\Database\Query\Builder
     */
    public static function removeWhere($query, $column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($query instanceof Illuminate\Database\Query\Builder) {
            $wheres = $query->wheres;
            foreach ($wheres as $ind => $where) {
                if ($where['column'] == $column &&

                    (is_null($operator) || $where['operator'] == $operator) &&

                    (is_null($value) || $where['value'] == $value) &&

                    $where['boolean'] == $boolean) {
                    array_splice($wheres, $ind, 1);
                }
            }
            $query->wheres = $wheres;
        } else {
            if (method_exists($query, 'getQuery')) {
                $q = $query->getQuery();
                $q = self::removeWhere($q, $column, $operator, $value, $boolean);
                $query->setQuery($q);
            }
        }
        return $query;
    }
}
