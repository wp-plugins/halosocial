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

/**
 * Global Asset manager
 */
use Illuminate\Http\RedirectResponse;
 
class HALORedirectResponse extends Illuminate\Http\RedirectResponse
{
	protected $condition = true;
	protected $args = array();
	/**
	 * Method for condition redirecting
	 * 
	 */
	public function when($condition)
	{
		$this->condition = $condition;
		return $this;
	}
	
	/**
	 * Do apply the redirect content to the output instead of waiting for echo. Combine with condition checking for more fluent programming
	 * So bascially, in pratise, dev will call: Redirect::to()->when($condition)->apply()
	 * 
	 */
	public function apply(){
		if($this->condition){
			//treat differently between ajax request and http request
			if(JAXResponse::ajax()){
				//check if this is internal redirect or browser redirect
				$url = $this->targetUrl;
				if(($pos = strpos($url,'?')) !== false){
					parse_str(substr($url,$pos+1),$par);
					
					if(isset($par['app']) && $par['app'] == 'ajax') {
						$func = $par['task'];
						$func = 'ajax' . ucfirst($func);
						$className = ucfirst($par['view']) . 'Controller';
						$args = $this->args;
						$controller = new $className();
						
						$response = call_user_func_array(array($controller,$func), $args);
						//for ajax call, we need to clean old request data after redirecting otherwise it will exists on any next ajax request
						$this->session->flush();
						echo $response;
						exit;
					
					}
				}
				$response    = new JAXResponse();
				$response->redirect($url);
				echo $response->sendResponse();
				exit;
			}
			
			echo $this->getContent();
			//then exit @todo: consider to throw exception instead of halt up
			exit;
		} else {
			//do no thing
		}
	}
	
	public function setArgs(array $args){
		$this->args = $args;
		return $this;
	}
    /**
     * Sets the redirect target of this response. Because the plugin works on shortcode content, sending redirecting header is not applicable. 
	 * Use javascript redirect call to make the soft redirect
     *
     * @param string  $url     The URL to redirect to
     *
     * @return RedirectResponse The current response.
     * 
     * @throws \InvalidArgumentException
	 * @applicable: wp only
     */
    public function setTargetUrl($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
		
        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<script>location.href = "%1$s";</script>', $url));
            //sprintf('<script>location.href = "%1$s";</script>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        //$this->headers->set('Location', $url);

        return $this;
    }
	
	public function render(){
		echo $this->getContent();
	}
}

