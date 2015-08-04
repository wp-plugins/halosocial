<?php
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Output\BufferedOutput;
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

class AdminToolsController extends AdminController
{

    /**
     * Inject the models.
     * @param HALOFieldModel $field
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show a list of all the tool avaialble.
     *
     * @return View
     */
    public function getIndex()
    {
        // Title
        $title = 'Tools';

		// Toolbar
		HALOToolbar::addToolbar('Install','','','halo.tools.installDatabase()','plus');

        // Show the page
        return View::make('admin/tools/index', compact('title'));
    }

	/**
	 * Show installation
	 *
	 * @return View
	 */
	public function getInstall()
	{
			// Title
			$title = 'Installation';
			// Show the page
			return View::make('admin/tools/install', compact('title'));
	}

	public function __getLicenseBuilder($licenseString = '') {
		$product = 'PRO';
		$license = HALOConfig::loadLicense();
		$builder = HALOUIBuilder::getInstance('', 'content', array('zone' => 'licenseform'));
		$content = HALOUIBuilder::getInstance('', 'form.form', array('name' => 'licenseForm'));
		if($license && $license->license !== 'valid') {
			if(isset($license->error) && $license->error){
				switch($license->error) {
					case 'revoked': 
						$content->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => 'Your license key is revoked.',
																						'type' => 'warning')));
						break;
					case 'expired':
						$content->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => 'Your license key is expired. Please consider to renew your license to get most out of HaloSocial.',
																						'type' => 'warning')));
						HALOResponse::addMessage(HALOError::failed(__halotext('Expired license key.')));
						break;
					default:
						$content->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => 'Your license key is incorrect. Please check your license key then try again.',
																						'type' => 'warning')));
						HALOResponse::addMessage(HALOError::failed(__halotext('Incorrect license key.')));
						break;
				
				}
			} else {
				$content->addUI('alert', HALOUIBuilder::getInstance('', 'form.alert', array('title' => 'Please update your license key to get automatic updated.',
																				'type' => 'warning')));			
			}
		}
		$content->addUI('product_type', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Product Type', 
																						'text' => HALO_PLUGIN_PRODUCT_TYPE)));
		$content->addUI('product', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Product Version', 
																						'text' => HALO_PLUGIN_VER)));
		$content->addUI('license', HALOUIBuilder::getInstance('', 'form.text', array('name' => 'license',
																					'title' => 'License Key',
																					'helptext'=> __halotext('You can get the license number from the HaloSocial website'),
																					'value' => HALOConfig::get('license.key', $licenseString ),
																					'onKeyup' => "halo.tools.changeLicense()",
																					'validation' => 'required')));
		if($license && trim($license->customer_email)) {																					
			$content->addUI('email', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Customer Email', 'text' => $license->customer_email)));
		}
		if($license && trim($license->customer_name)) {
			$content->addUI('name', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Customer Name', 'text' => $license->customer_name)));
		}
		if($license && trim($license->payment_id)) {																					
			$content->addUI('payement', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Payment Id', 'text' => $license->payment_id)));
		}
		if($license && $license->expires && $license->expires !== '1970-01-01 00:00:00' ) {
			$expires = new Carbon($license->expires);
			$now = Carbon::now();
			$diffInDays = $now->diffInDays($expires, false);
			if($diffInDays < 0){
				$expiresClass = 'badge badge-danger';			
			} elseif($diffInDays < 7) {
				$expiresClass = 'badge badge-info';
			} else {
				$expiresClass = 'badge badge-success';
			}
			
			$content->addUI('expires', HALOUIBuilder::getInstance('', 'form.static', array('title' => 'Expires', 'text' => $license->expires . ' <span class="' . $expiresClass . '">' . $expires->diffForHumans(). '</span>.')));
		}
					
		$content->addUI('activate', HALOUIBuilder::getInstance('', 'form.button', array('row' => '', 'inline' => 1, 'class' => 'halo-btn-success halo-activate-license-btn', 
																						'title' => 'Activate',
																						'onClick' => "halo.tools.activate()"
																						)));
		if($license && $license->license == 'valid') {																					
			$content->addUI('update', HALOUIBuilder::getInstance('', 'form.button', array('row' => '', 'inline' => 1, 'class' => 'halo-btn-success halo-activate-license-btn', 
																						'title' => 'Check for update',
																						'zone' => 'checkupdate_btn',
																						'onClick' => "halo.tools.checkUpdate()"
																						)));
		}
		if($license && $license->expires && $license->expires !== '1970-01-01 00:00:00') {																					
			$expires = new Carbon($license->expires);
			$now = Carbon::now();
			//@rule: ask for renew when license expires < 7 days
			if($now->diffInDays($expires, false) <= 7){
				$content->addUI('renew', HALOUIBuilder::getInstance('', 'form.button', array('row' => '', 'inline' => 1, 'class' => 'halo-btn-success halo-activate-license-btn', 
																						'title' => 'Renew',
																						'onClick' => "halo.util.redirect('" . HALOAssetHelper::getRenewalUrl(HALOConfig::get('license.key', '' ), EDD_HALOSOCIAL_DOWNLOAD_ID) . "')"
																						)));
			}
		} else {
				$content->addUI('clearfix', HALOUIBuilder::getInstance('', 'clearfix', array())); 
				$content->addUI('get_license', HALOUIBuilder::getInstance('', 'form.raw', array('row' => '', 
																						'content' => "Don't have a license? <a target=\"_blank\" href=\"http://tiny.cc/halosocial-myorders\">Get one today!</a>"
																						)));
		}
		
		$builder->addUI('content', $content);
		
		return $builder;
	}
	
	/**
	 * Show Licensing page
	 *
	 * @return View
	 */
	public function getLicense()
	{
		// Title
		$title = 'License';
		$activated = false;
		$product = 'PRO';
		$builder = $this->__getLicenseBuilder();
		// Show the page
		return View::make('admin/tools/license', compact('title', 'activated', 'builder'));
	}

    /**
     * function to activate license
     *
     * @return JSON
     */
    public function ajaxActivate($data)
    {
		if(!isset($data['license']) || empty($data['license'])) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Please enter license key.')));
			return HALOResponse::sendResponse();
		}
		$license = $data['license'];

		// make sure the response came back okay
		$license_data = HALOConfig::activateLicense($license);
		HALOAuthSystemHandler::canGo(true);
		
		if ( !$license_data ) {
			HALOResponse::addMessage(HALOError::failed(__halotext('Could not connect to server')));
		} else {
			$builder = $this->__getLicenseBuilder();
			HALOResponse::updateZone($builder->fetch());
		}
        return HALOResponse::sendResponse();
    }
	
    /**
     * function to check for update
     *
     * @return JSON
     */
    public function ajaxCheckUpdate()
    {
		$updater = HALOConfig::checkUpdate();
		$data = $updater->check_update(null);
		if(isset($data->response) && !empty($data->response)) {
			$builder = HALOUIBuilder::getInstance('', 'form.button', array('row' => '', 'inline' => 1, 'class' => 'halo-btn-success', 
																			'title' => 'New version available. Click to update',
																			'zone' => 'checkupdate_btn',
																			'onClick' => "halo.tools.liveUpdate()"
																			));
			HALOResponse::updateZone($builder->fetch());

		}
		return HALOResponse::sendResponse();
    }
	
    /**
     * function to live update
     *
     * @return JSON
     */
    public function ajaxLiveUpdate()
    {
		// $updater = HALOConfig::checkUpdate();
		// $data = $updater->check_update(null);
		// if(isset($data->response) && !empty($data->response)) {
			// $builder = HALOUIBuilder::getInstance('', 'content', array('zone' => 'license_log'));
			// $builder->addUI('htmllog', HALOUIBuilder::getInstance('', 'html', array('html' => 'testing')));
			// HALOResponse::updateZone($builder->fetch());

		// }
		HALOResponse::redirect(admin_url('update-core.php'));
		return HALOResponse::sendResponse();
    }
	
	/**
	 * Show installation
	 *
	 * @return View
	 */
	public function getUpgrade()
	{
		if(!HALOAuth::can('backend.upgrade') && !current_user_can('manage_options')){
			echo __halotext('Permission dennied');
			exit;
		}
		$isValid = Input::get('force', false);
			// Title
			$title = 'Upgrade';
			// Show the page
			return View::make('admin/tools/upgrade', compact('title', 'isValid'));
	}
	
	/**
	 * Show installation
	 *
	 * @return View
	 */
	public function postUpgrade()
	{
		if(!HALOAuth::can('backend.upgrade') && !current_user_can('manage_options')){
			echo __halotext('Permission dennied');
			exit;
		}

		$isValid = false;
		//process to unzip the upgrade package file if provided
		if(Input::hasFile('upgrade_pkg')) {
			$pkg = Input::file('upgrade_pkg');
			//setup the extract target
			$target_dir = HALO_PLUGIN_PATH;
			$originalFile = $pkg->getClientOriginalName();
			$tmp_dir = $pkg->getRealPath() . '_pkg';
			$pkg->move($tmp_dir, $originalFile);
			
			$pkg_file = $tmp_dir . '/' . $originalFile;
			$pkg_dir = $tmp_dir . '/' . basename($originalFile) . '.unzip';
			//upgrade pkg
			HALOArchive::extract($pkg_file, $pkg_dir);
			
			//valid the packge
			$halo_dir = $pkg_dir . '/halosocial';

			$directories = File::directories($pkg_dir);
			foreach($directories as $key => $dir) {
				if(basename($dir) === '.' || basename($dir) === '..') {
					unset($directories[$key]);
				}
			}
			if(count($directories) == 1 && File::exists($halo_dir . '/halosocial.php') && File::exists($halo_dir . '/composer.json')) {
				$isValid = true;
				//copy the package
				HALOArchive::extract($pkg_file, dirname($target_dir));
			}
			//cleanup
			File::deleteDirectory($tmp_dir);
		}
			// Title
			$title = 'Upgrade';
			
			// Show the page
			return Redirect::to('?app=admin&view=tools&task=upgrade');
			// return View::make('admin/tools/upgrade', compact('title', 'isValid'));
	}
	
	/*
		function to finish installation
	*/
	protected function finishInstall(){
		halo_finish_install();
		$installLock = HALO_PLUGIN_PATH . '/install/install.lock';
		file_put_contents($installLock,1);
		return true;
	}

	/* 
		check if database already seeded
	*/
	protected function alreadySeed(){
		$seedLock = HALO_PLUGIN_PATH . '/install/seed.lock';
		return file_exists($seedLock);
	}
	/*
		function to update theme
	*/
	protected function updateTheme(){
		$themeName = 'halodefault';
		$pkg_file = HALO_PLUGIN_PATH . '/' . $themeName . '.zip';
		$theme_dir = get_theme_root();
		if(is_file($pkg_file) && is_dir($theme_dir)){
			try {
				HALOArchive::extract($pkg_file,$theme_dir);
			} catch (Exception $e) {
				$message = '<br><strong>'. $e->getMessage() . '</strong>';
				HALOResponse::addMessage(HALOError::failed($message));
			}
		}
		return true;

	}

	/*
		function to update plugins
	*/
	protected function updatePlugins(){
		$pkg_file = HALO_PLUGIN_PATH . '/halo_plugins.zip';
		$tmp_dir = HALO_PLUGIN_PATH . '/tmp';
		if(!is_dir($tmp_dir)){
			File::createDir($tmp_dir);
		}
		
		if(is_file($pkg_file) && is_dir($tmp_dir)){
			try {
				HALOArchive::extract($pkg_file,$tmp_dir);
				
				//go through all plugins and update them
				$files = scandir($tmp_dir);
				foreach($files as $file){
					$fileInfo = pathinfo(strtolower($file));
					if(!empty($fileInfo['extension']) && in_array($fileInfo['extension'],array('zip','rar','tar','gz'))){
						$rtn = HALOPlugin::install($tmp_dir . '/' . $file);
						if(!$rtn->any()){
							//enable plugin for the first installation
							$plugin = HALOResponse::getData('plg');
							if($plugin->isNew()){
								$plugin->status = 1;
								$plugin->save();
							}
						}
					}
				}
			} catch (Exception $e) {
				var_dump($e);
				$message = '<br><strong>'. $e->getMessage() . '</strong>';
				HALOResponse::addMessage(HALOError::failed($message));
			}
		}
		return true;

	}
    /**
     * Migrate database tool
     *
     * @return JSON
     */

	public function ajaxInstallDatabase($action){
		$content = 'loading ...';
		$nextAction = '';
		$title = 'Database installation';
		$output = new BufferedOutput;
		try {
			switch($action){
				case 'install':
					if(!Schema::hasTable('migrations')){
						$content = '<br>init migrate:install...';
						$content .= Artisan::call('migrate:install', array(), $output);
						$content .= '<br>done migrate:install';
						$nextAction = 'migrate';
						break;
					}
				case 'migrate':
					$newInstall = false;
					if(!Schema::hasTable('migrations')){
						$newInstall = true;
					}
					$content = '<br>init migrate...';
					$content .= Artisan::call('migrate', array(), $output);
					$content .= '<br>done migrate';
					$nextAction = 'seed';
					
					break;
				case 'rollback':
					if(!Schema::hasTable('migrations')){
						$content = '<br>init migrate:rollback...';
						$content .= Artisan::call('migrate:rollback', array(), $output);
						$content .= '<br>done migrate:rollback';
						$nextAction = 'end';
						break;
					}
				case 'refresh':
					//includes database migrate folders
					$pluginIds = HALOPluginModel::lists('id');
					foreach($pluginIds as $id) {
						$plugin = HALOPluginModel::getInstance($id);
						//require files in migration folder
						$migrationDir = $plugin->getMigrationDir();
						if($migrationDir) {
							foreach (glob(HALO_PLUGIN_PATH . '/' . $migrationDir . "/*.php") as $filename) {
								include_once $filename;
							}
						}
					}
					//include core migration
					$migrationDir = '/app/database/migrations';
					foreach (glob(HALO_PLUGIN_PATH . '/' . $migrationDir . "/*.php") as $filename) {
						include_once $filename;
					}
					
					$content = '<br>init migrate refresh...';
					$content .= Artisan::call('migrate:refresh', array(), $output);
					$content .= '<br>done migrate refresh';
					$nextAction = 'seed';
					break;
				case 'seed':
					//only seed data if explicit defined
					$seedLock = HALO_PLUGIN_PATH . '/install/seed.lock';
					$seed = file_exists($seedLock);
					if(!$seed){
						$content = '<br>init table seeding ...';
						$rtn = Artisan::call('db:seed', array(), $output);
						if(HALOResponse::getMessage()->any()) {
							$nextAction = 'error';
						} else {
							$content .= '<br>done tables seeding';
							file_put_contents($seedLock,1);
							$nextAction = 'theme';
						}
					} else {
						$content = '<br>skip table seeding ...';
						$nextAction = 'theme';
					}
					break;
				case 'theme':
					//move to the next step
					$content = '<br>Update Theme';
					$this->updateTheme();
					$content .= '<br>done theme updating ...';
					$nextAction = 'plugins';
					
					break;
				case 'plugins':
					//move to the next step
					$content = '<br>Update Plugins';
					$this->updatePlugins();
					$content .= '<br>done plugins updating ...';
					$nextAction = 'done';
					
					break;
				case 'done':
					//move to the next step
					$content = '<br><button type="button" onclick="halo.util.redirect(\''. admin_url( 'admin.php?page=halo_dashboard') .'\')" class="halo-btn halo-btn-default">Done</button>';
					$nextAction = 'end';
					
					$this->finishInstall();

					break;
				case 'error':
					$content = '<br><h2><strong>Error occured.</strong></h2>';
					$nextAction = 'error';
					
					break;					
				default:
					//show the installation database form
					$content = '<br><h2><strong>Unknown Error</strong></h2>';
					$nextAction = 'error';
			}
		} catch (Exception $e) {
			//Response::make($e->getMessage(), 500);
			$content .= '<br><strong>'.$e->getMessage() . '</strong>';
			$nextAction = 'error';
		}	
		$uid = uniqid();
		if($nextAction == 'end'){
			$actionBtn = '<div class="block-center" data-halozone="tools-btn">
							<button type="button" onclick="halo.util.redirect(\''. admin_url( 'admin.php?page=halo_dashboard') .'\')" class="halo-btn halo-btn-default">Done</button>
						  </div>';
		} else if($nextAction == 'error'){
			$actionBtn = '<div class="block-center" data-halozone="tools-btn">
							<button type="button" onclick="halo.tools.installDatabase(\'install\')"" class="halo-btn halo-btn-default">Try again</button>
						  </div>';
		} else {
			$actionBtn = '<div class="block-center" data-halozone="tools-btn">
							<button id="halo_tools_btn'.$uid.'" data-timer="6" type="button" onclick="halo.tools.installDatabase(\''.$nextAction.'\')" class="halo-btn halo-btn-default">Next</button>
						  </div>';
		}

		HALOResponse::updateZone($actionBtn);
		HALOResponse::addScriptCall('halo.util.timeoutBtn','halo_tools_btn' .$uid);
		HALOResponse::insertZone('tools-console','<div class="halo-console-line">' . $content . '</div>');
		HALOResponse::insertZone('tools-console','<div class="halo-console-line">' . $output->fetch() . '</div>');
		return HALOResponse::sendResponse(false);
	}

    /**
     * function to flush all cache
     *
     * @return JSON
     */
    public function ajaxClearCache()
    {
        Artisan::call('cache:clear');
        HALOResponse::addMessage(HALOError::failed(__halotext('All cache data has been cleared')));
        return HALOResponse::sendResponse();
    }
}
