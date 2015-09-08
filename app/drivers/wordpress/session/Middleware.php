<?php 
use Symfony\Component\HttpFoundation\Request;
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

class HALOSessionMiddleware extends Illuminate\Session\Middleware {

	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		$this->checkRequestForArraySessions($request);

		// If a session driver has been configured, we will need to start the session here
		// so that the data is ready for an application. Note that the Laravel sessions
		// do not make use of PHP "native" sessions in any way since they are crappy.
		if ($this->sessionConfigured())
		{
			$session = $this->startSession($request);

			$request->setSession($session);
		}

		$response = $this->app->handle($request, $type, $catch);

		// Again, if the session has been configured we will need to close out the session
		// so that the attributes may be persisted to some storage medium. We will also
		// add the session identifier cookie to the application response headers now.
		if ($this->sessionConfigured())
		{
			//$this->closeSession($session);

			$this->addCookieToResponse($response, $session);
		}

		return $response;
	}

	/**
	 * Get the session implementation from the manager.
	 *
	 * @return \Illuminate\Session\SessionInterface
	 */
	public function getSession(Request $request)
	{
		$session = $this->manager->driver();
		if ($session->getName() && isset($_COOKIE[$session->getName()])) {
			$session->setId($_COOKIE[$session->getName()]);
		}

		return $session;
	}

}
