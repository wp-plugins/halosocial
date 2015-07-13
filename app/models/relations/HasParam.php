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

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class HasParam extends Relation {

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * The param key of the parent model.
	 *
	 * @var string
	 */
	protected $paramKey;

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $paramKey
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $paramKey, $foreignKey='id')
	{
		$this->paramKey = $paramKey;
		$this->foreignKey = $foreignKey;

		parent::__construct($query, $parent);
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	public function addConstraints()
	{
		if (static::$constraints)
		{
			$table = $this->related->getTable();
			$paramValues = (array) $this->parent->getParams($this->paramKey,array());
			if(empty($paramValues)){
				$paramValues = array(-1);
			}
			$this->query->whereIn($table.'.'.$this->foreignKey, $paramValues);
		}
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		$keys = $this->getParamKeys($models, $this->paramKey);
		$keys = empty($keys) ? array(0) : $keys;
		$this->query->whereIn($this->foreignKey, $keys);
	}

	/**
	 * Get all of the primary keys for an array of models.
	 *
	 * @param  array   $models
	 * @param  string  $key
	 * @return array
	 */
	protected function getParamKeys(array $models, $paramKey)
	{
		$keys = array();
		foreach ($models as $model) {
			$keys = array_merge($keys, $this->getModelKeys($model, $paramKey));
		}
		return $keys;
	}

	/**
	 * Get all of the primary keys an of models
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model $model
	 * @param  string $paramKey
	 * @return array          
	 */
	protected function getModelKeys($model, $paramKey)
	{
		$key = $model->getParams($paramKey);
		if(!is_array($key)) {
			$key = $key == '' ? array() : explode(',', $key);
		}
		return $key;
	}
	
	/**
	 * Match the eagerly loaded results to their many parents.
	 *
	 * @param  array   $models
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @param  string  $type
	 * @return object
	 */
	public function match(array $models, Collection $results, $relation)
	{
		$dictionary = $this->buildDictionary($results);

		// Once we have the dictionary we can simply spin through the parent models to
		// link them up with their children using the keyed dictionary to make the
		// matching very convenient and easy work. Then we'll just return them.
		foreach ($models as $model) {
			$key = $this->getModelKeys($model, $this->paramKey);
			
			if (!empty($key)) {
				$value = $this->getRelationValue($dictionary, $key);

				$model->setRelation($relation, $value);
			}
		}

		return $models;
	}
	 
	/**
	 * Get the value of a relationship by one or many type.
	 *
	 * @param  array   $dictionary
	 * @param  string  $key
	 * @param  string  $type
	 * @return string
	 */
	protected function getRelationValue(array $dictionary, $keys)
	{
		$value = null;

		foreach($keys as $key) {
			if(isset($dictionary[$key])) {
				if(is_null($value)) {
					$value = $this->related->newCollection($dictionary[$key]);
				} else {
					$value->add($dictionary[$key]);
				}
			}
		}

		return $value;
	}

	/**
	 * Build model dictionary keyed by the relation's foreign key.
	 *
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @return array
	 */
	protected function buildDictionary(Collection $results)
	{
		$dictionary = array();

		$foreign = $this->getPlainForeignKey();

		// First we will create a dictionary of models keyed by the foreign key of the
		// relationship as this will allow us to quickly access all of the related
		// models without having to do nested looping which will be quite slow.
		foreach ($results as $result) {
			$dictionary[$result->{$foreign}][] = $result;
		}

		return $dictionary;
	}

	/**
	 * Attach a model instance to the parent model.
	 *
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function save(Model $model)
	{
		$model->setAttribute($this->getPlainForeignKey(), $this->getParentKey());

		return $model->save() ? $model : false;
	}

	/**
	 * Attach an array of models to the parent instance.
	 *
	 * @param  array  $models
	 * @return array
	 */
	public function saveMany(array $models)
	{
		array_walk($models, array($this, 'save'));

		return $models;
	}

	/**
	 * Create a new instance of the related model.
	 *
	 * @param  array  $attributes
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $attributes)
	{
		$foreign = array (
			$this->getPlainForeignKey() => $this->getParentKey(),
		);

		// Here we will set the raw attributes to avoid hitting the "fill" method so
		// that we do not have to worry about a mass accessor rules blocking sets
		// on the models. Otherwise, some of these attributes will not get set.
		$instance = $this->related->newInstance();

		$instance->setRawAttributes(array_merge($attributes, $foreign));

		$instance->save();

		return $instance;
	}

	/**
	 * Create an array of new instances of the related model.
	 *
	 * @param  array  $records
	 * @return array
	 */
	public function createMany(array $records)
	{
		$instances = array();

		foreach ($records as $record) {
			$instances[] = $this->create($record);
		}

		return $instances;
	}

	/**
	 * Perform an update on all the related models.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	public function update(array $attributes)
	{
		if ($this->related->usesTimestamps()) {
			$attributes[$this->relatedUpdatedAt()] = $this->related->freshTimestamp();
		}

		return $this->query->update($attributes);
	}

	/**
	 * Get the key for comparing against the parent key in "has" query.
	 *
	 * @return string
	 */
	public function getHasCompareKey()
	{
		return $this->getForeignKey();
	}

	/**
	 * Get the foreign key for the relationship.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	/**
	 * Get the plain foreign key.
	 *
	 * @return string
	 */
	public function getPlainForeignKey()
	{
		$segments = explode('.', $this->getForeignKey());

		return $segments[count($segments) - 1];
	}

	/**
	 * Get the key value of the paren's local key.
	 *
	 * @return object
	 */
	public function getParentKey()
	{
		return $this->parent->getAttribute($this->localKey);
	}

	/**
	 * Get the fully qualified parent key name.
	 *
	 * @return object
	 */
	public function getQualifiedParentKeyName()
	{
		return $this->parent->getTable() . '.' . $this->localKey;

	}

	/**
	 * Get the results of the relationship.
	 *
	 * @return object
	 */
	public function getResults()
	{
		return $this->query->get();
	}

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  array   $models
	 * @param  string  $relation
	 * @return array
	 */
	public function initRelation(array $models, $relation)
	{
		foreach ($models as $model) {
			$model->setRelation($relation, $this->related->newCollection());
		}

		return $models;
	}
	
}
