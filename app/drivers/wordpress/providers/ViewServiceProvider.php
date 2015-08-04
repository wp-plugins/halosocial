<?php 
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\CompilerEngine;

use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;
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

class ViewServiceProvider extends BaseViewServiceProvider {
	/**
	 * Register the view finder implementation.
	 *
	 * @return void
	 */
	public function registerViewFinder()
	{
		$this->app['view.finder'] = $this->app->share(function($app)
		{
			$paths = array();
			$paths = HALOAssetHelper::getPaths();
			return new FileViewFinder($app['files'], $paths);
		});
	}
	/**
	 * Register the Blade engine implementation.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $resolver
	 * @return void
	 */
	public function registerBladeEngine($resolver)
	{
		$app = $this->app;

		// The Compiler engine requires an instance of the CompilerInterface, which in
		// this case will be the Blade compiler, so we'll first create the compiler
		// instance to pass into the engine so it can compile the views properly.
		$app->bindShared('blade.compiler', function($app)
		{
			$cache = $app['path.storage'].'/views';

			return new HALOBladeCompiler($app['files'], $cache);
		});

		$resolver->register('blade', function() use ($app)
		{
			return new CompilerEngine($app['blade.compiler'], $app['files']);
		});
	}

}
