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

class BlogController extends BaseController {

	/**
	 * @var \Fbf\LaravelBlog\Post
	 */
	protected $post;

	/**
	 * @param \Fbf\LaravelBlog\Post $post
	 */
	public function __construct(HALOBlogModel $post)
	{
		$this->post = $post;
	}

	/**
	 * @return mixed
	 */
	public function index()
	{
		// Get the selected posts
		$posts = $this->post->live()
			->orderBy($this->post->getTable().'.is_sticky', 'desc')
			->orderBy($this->post->getTable().'.published_date', 'desc')
			->paginate(12);

		// $archives = $this->post->archives();
		//dd($posts);
		return \View::make('site.blog.index', compact('posts', 'archives'));
	}

	/**
	 * @param $selectedYear
	 * @param $selectedMonth
	 * @return mixed
	 */
	public function indexByYearMonth($selectedYear, $selectedMonth)
	{
		// Get the selected posts
		$posts = $this->post->live()
			->byYearMonth($selectedYear, $selectedMonth)
			->orderBy($this->post->getTable().'.is_sticky', 'desc')
			->orderBy($this->post->getTable().'.published_date', 'desc')
			->paginate(\Config::get('laravel-blog::views.index_page.results_per_page'));

		// Get the archives data if the config says to show the archives on the index page
		if (\Config::get('laravel-blog::views.index_page.show_archives'))
		{
			$archives = $this->post->archives();
		}

		return \View::make(\Config::get('laravel-blog::views.index_page.view'), compact('posts', 'selectedYear', 'selectedMonth', 'archives'));
	}

	/**
	 * @param $relationshipIdentifier
	 * @return mixed
	 */
	public function indexByRelationship($relationshipIdentifier)
	{
		// Get the selected posts
		$posts = $this->post->live()
			->byRelationship($relationshipIdentifier)
			->orderBy($this->post->getTable().'.is_sticky', 'desc')
			->orderBy($this->post->getTable().'.published_date', 'desc')
			->paginate(\Config::get('laravel-blog::views.index_page.results_per_page'));

		// Get the archives data if the config says to show the archives on the index page
		if (\Config::get('laravel-blog::views.index_page.show_archives'))
		{
			$archives = $this->post->archives();
		}

		return \View::make(\Config::get('laravel-blog::views.index_page.view'), compact('posts', 'archives'));
	}

	/**
	 * @param $slug
	 * @return mixed
	 */
	public function view($id, $slug = null)
	{
		// Get the selected post
		$post = $this->post->live()
			->where($this->post->getTable().'.id', '=', $id)
			->firstOrFail();
		// Show raw SQL
    	// $queries = DB::getQueryLog();
    	// dd(end($queries));
    	// dd($posts->count());
		// Get the next newest and next oldest post if the config says to show these links on the view page
		$newer = $older = false;
		// if (\Config::get('laravel-blog::views.view_page.show_adjacent_items'))
		// {
		// 	$newer = $post->newer();
		// 	$older = $post->older();
		// }

		// Get the archives data if the config says to show the archives on the view page
		// if (\Config::get('laravel-blog::views.view_page.show_archives'))
		// {
		// 	$archives = $this->post->archives();
		// }

		return View::make('site.blog.view', compact('post', 'newer', 'older', 'archives'));

	}

	/**
	 * @return mixed
	 */
	public function rss()
	{
		$feed = Rss::feed('2.0', 'UTF-8');
		$feed->channel(array(
			'title' => \Config::get('laravel-blog::meta.rss_feed.title'),
			'description' => \Config::get('laravel-blog::meta.rss_feed.description'),
			'link' => \URL::current(),
		));
		$posts = $this->post->live()
			->where($this->post->getTable().'.in_rss', '=', true)
			->orderBy($this->post->getTable().'.published_date', 'desc')
			->take(10)
			->get();
		foreach ($posts as $post){
			$feed->item(array(
				'title' => $post->title,
				'description' => $post->summary,
				'link' => \URL::action('Fbf\LaravelBlog\PostsController@view', array('slug' => $post->slug)),
			));
		}
		return \Response::make($feed, 200, array('Content-Type', 'application/rss+xml'));
	}

}
