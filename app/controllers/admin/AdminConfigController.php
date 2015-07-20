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

class AdminConfigController extends AdminController
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
		$title = __halotext('Configuration');

		// Toolbar
		HALOToolbar::addToolbar('Clear Cache','','',"halo.tools.clearCache()",'chain-broken');
		HALOToolbar::addToolbar('Save','','',"halo.form.submit('halo-admin-form')",'floppy-o');
		HALOToolbar::addToolbar('Cancel','','',"halo.util.reload()",'undo');

        // Grab all the configuration
		$config = array();
		//init all avaliable settings here
		//1. global settings

		$config['global'] = $this->getGlobalCfg();
		
		//4. Photo settings	
		// $config['photo'] = $this->getPhotoCfg();
		
		//6. Social settings
		$config['social'] = $this->getSocialCfg();
		
		//7. Notification settings
		$config['notification'] = $this->getNotificationCfg();
		
		//8. Userpoint settings
		$config['userpoint'] = $this->getUserpointCfg();
		
		//for plugin trigger settings
		$settings = new stdClass();
		$settings->config = $config;
		Event::fire('config.loadSettings',array(&$settings));
		$config = $settings->config;
		
		// Show the page
		return View::make('admin/config/index', compact('config', 'title'));
  }
	
	/*
		save the configuration to database
	*/
	public function saveConfig(){
		$data = Input::all();
		unset($data['_token']);
		if(!HALOAuth::can('backend.view')){
			return Redirect::to('?app=admin&view=config')->with('error',__halotext('Permission dennied'));
		}
		//var_dump($data);exit;
		//update all the change
		foreach($data as $key=>$val){
			$key = str_replace('_','.',$key);
			HALOConfig::set($key,$val);
		}
		
		//store to database
		HALOConfig::store();
		
		Event::fire('system.onAfterChangeConfig', array());
		
		return Redirect::to('?app=admin&view=config')->with('success',__halotext('Configuration was updated succesfully'));
	}
	
	/*
		return HALOUIBuilder instance for global configuration tab
	*/
	protected function getGlobalCfg(){
		$globalCfg = new stdClass();
		$globalCfg->title = "Global";
		$globalCfg->icon = "";
		$globalCfg->builder = HALOUIBuilder::getInstance('global_cfg','config.group',array('name'=>'global_cfg'));
		
		//licensing section
		// $section = HALOUIBuilder::getInstance('license','config.section',array('title'=>__halotext('License')))
							// ->addUI('global.edd_itemname', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.edd_itemname',
																											// 'title'=>__halotext('Product name'),
																											// 'value'=>HALOConfig::get('global.edd_itemname'))))
							// ->addUI('global.edd_license', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.edd_license','placeholder'=>__halotext('Enter Halo license key'),
																											// 'title'=>__halotext('License'),
																											// 'helptext'=>__halotext('You can get the license number from the HaloSocial website'),
																											// 'value'=>HALOConfig::get('global.edd_license'))))
							// ;
		// $globalCfg->builder->addUI('section@array',$section);					
		//homepage section
		$section = HALOUIBuilder::getInstance('homepage','config.section',array('title'=>__halotext('Homepage')))
							->addUI('global.updateInterval', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.updateInterval','placeholder'=>__halotext('Auto update interval in milliseconds'),
																											'title'=>__halotext('Update Interval'),
																											'helptext'=>__halotext('Configure this value to change the Ajax update interval.'),
																											'value'=>HALOConfig::get('global.updateInterval'))))
							->addUI('global.showNavigation', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.showNavigation', 'title'=>__halotext('Show main menu'),
																											'options'=>array(HALOObject::getInstance(array('value'=>'1','title'=>__halotext('Yes'))),
																															HALOObject::getInstance(array('value'=>'0','title'=>__halotext('No')))
																															),
																											'value'=>HALOConfig::get('global.showNavigation', '1'))))
							->addUI('global.maxNavLinks', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.maxNavLinks','placeholder'=>__halotext('Enter a number'),
																											'title'=>__halotext('Maximum number of items to display on the menu'),
																											'helptext'=>__halotext('Set the number of links to display on the main menu.'),
																											'value'=>HALOConfig::get('global.maxNavLinks', 4))))
							;
		$globalCfg->builder->addUI('section@array',$section);					
		//registration section

		//Activity section
		$section = HALOUIBuilder::getInstance('activities','config.section',array('title'=>__halotext('Activities')))
							->addUI('global.activityDisplayLimit', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.activityDisplayLimit',
																							'title'=>__halotext('Activity Display Limit'),
																							'helptext'=>__halotext('Limit the number of activities returned per request'),
																							'value'=>HALOConfig::get('global.activityDisplayLimit'))))
							->addUI('global.activityDefaultPrivacy', HALOUIBuilder::getInstance('','form.privacy',array('name'=>'global.activityDefaultPrivacy',
																							'title'=>__halotext('Default Activity Privacy'),
																							'helptext'=>__halotext('Set default privacy for posting new activity'),
																							'value'=>HALOConfig::get('global.activityDefaultPrivacy'))))
							->addUI('global.activityShowPrivacy', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.activityShowPrivacy',
																							'title'=>__halotext('Show Privacy'),
																							'helptext'=>__halotext('Show or hide the privacy icon on each stream title'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.activityShowPrivacy'))))
							;
		$globalCfg->builder->addUI('section@array',$section);					

		//Comment section
		$section = HALOUIBuilder::getInstance('comment','config.section',array('title'=>__halotext('Comment Feature')))
							->addUI('global.commentDisplayInput', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.commentDisplayInput',
																							'title'=>__halotext('Always Display Comment Input'),
																							'helptext'=>__halotext('Display or hide the comment input by default'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.commentDisplayInput', 1))))
							;
		$globalCfg->builder->addUI('section@array',$section);					

		//Security section
		$section = HALOUIBuilder::getInstance('security','config.section',array('title'=>__halotext('Security')))
							->addUI('global.enableSecure', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.enableSecure',
																							'title'=>__halotext('Enable Secure mode'),
																							'helptext'=>__halotext('Enable or disable http secure (https) mode'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.enableSecure'))))
							;
		$globalCfg->builder->addUI('section@array',$section);					

		//Debug section
		$section = HALOUIBuilder::getInstance('debug','config.section',array('title'=>__halotext('Debug')))
							->addUI('global.enableDebug', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.enableDebug',
																							'title'=>__halotext('Enable Debug mode'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.enableDebug'))))
							->addUI('global.reportErrors', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.reportErrors',
																							'title'=>__halotext('Report all error messages'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.reportErrors', 0))))
							;
		$globalCfg->builder->addUI('section@array',$section);

		//Like 
		$section = HALOUIBuilder::getInstance('like','config.section',array('title'=>__halotext('Like Feature')))
							->addUI('global.enableLike', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.enableLike',
																							'title'=>__halotext('Enable Like'),
																							'helptext'=>__halotext('Enable or disable the Like function'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.enableLike'))))
							->addUI('global.enableDisLike', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.enableDisLike',
																							'title'=>__halotext('Enable Dislike'),
																							'helptext'=>__halotext('Enable or disable the Dislike function'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.enableDisLike', 0))))
							;
		$globalCfg->builder->addUI('section@array',$section);					

		//Template section
		$templates = array_filter(glob(HALO_PLUGIN_PATH . '/app/views/*'), 'is_dir');
		$templateOptions = array();
		foreach($templates as $template) {
			$name = basename($template);
			$templateOptions[] = HALOObject::getInstance(array('value'=>$name,'title'=>$name));
		}
		$section = HALOUIBuilder::getInstance('template','config.section',array('title'=>__halotext('Template')))
							->addUI('global.theme', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.theme', 'title'=>__halotext('Theme'),
																											'helptext'=>__halotext('Select a theme for the social page.'),
																											'options'=>array(HALOObject::getInstance(array('value'=>'halo_theme','title'=>__halotext('HaloSocial default theme'))),
																															HALOObject::getInstance(array('value'=>'active_theme','title'=>__halotext('WordPress active theme')))
																															),
																											'value'=>HALOConfig::get('global.theme', 'active_theme'))))
							->addUI('template.name', HALOUIBuilder::getInstance('','form.select',array('name'=>'template.name',
																							'title'=>__halotext('Template name'),
																							'helptext'=>__halotext('Select the active template for the frontend'),
																							'options' => $templateOptions,
																							'value'=>HALOConfig::get('template.name'))))
							->addUI('global.footerScript', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.footerScript',
																							'title'=>__halotext('Load Javascripts in page footer'),
																							'helptext'=>__halotext('Load all javascript at the page footer. Normally, this option should be turned on, but in themes that do not support footer script enqueuing, this option should be turned off'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.footerScript',1))))
							->addUI('global.cssMinify', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.cssMinify',
																							'title'=>__halotext('Minify CSS'),
																							'helptext'=>__halotext('Enable/disable minify CSS mode'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																											),
																							'value'=>HALOConfig::get('global.cssMinify',0))))
							->addUI('global.commentDisplayLimit', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.commentDisplayLimit',
																							'title'=>__halotext('Comment Display Limit'),
																							'helptext'=>__halotext('If the total number of comments is greater then this value, a View more link will be displayed'),
																							'value'=>HALOConfig::get('global.commentDisplayLimit'))))
							->addUI('global.notificationDisplayLimit', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.notificationDisplayLimit',
																							'title'=>__halotext('Notification Display Limit'),
																							'helptext'=>__halotext('If the total number of notifications is greater then this value, a View more link will be displayed'),
																							'value'=>HALOConfig::get('global.notificationDisplayLimit', 12))))
							;
		$globalCfg->builder->addUI('section@array',$section);

		//message
		if(HALOAuth::can('feature.message')){
			//settings section
			$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Messages')));
			
			$section->addUI('message.enable', HALOUIBuilder::getInstance('','form.radio',array('name'=>'message.enable',
																					'title'=>__halotext('Enable Messaging Feature'),
																					'helptext'=>__halotext('Enable/disable messaging feature'),
																					'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																									HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																									),
																					'value'=>HALOConfig::get('message.enable', 1))));
			$section->addUI('message.showpopup', HALOUIBuilder::getInstance('','form.radio',array('name'=>'message.showpopup',
																					'title'=>__halotext('Show chat bar'),
																					'helptext'=>__halotext('Show/hide popup message panel'),
																					'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Yes'))),
																									HALOObject::getInstance(array('value'=>0,'title'=>__halotext('No')))
																									),
																					'value'=>HALOConfig::get('message.showpopup', 1))));
			$section->addUI('conv.showrecentdays', HALOUIBuilder::getInstance('','form.text',array('name'=>'conv.showrecentdays',
																					'title'=>__halotext('Show conversations for most recent days'),
																					'helptext'=>__halotext('Only conversations from the last N days are displayed; others are shown by clicking on the Show earlier messages link'),
																					'value'=>HALOConfig::get('conv.showrecentdays'))));
			$section->addUI('message.defaultDisplayLimit', HALOUIBuilder::getInstance('','form.text',array('name'=>'message.defaultDisplayLimit',
																					'title'=>__halotext('Message Display Limit'),
																					'helptext'=>__halotext('Limit number of messages displayed in a conversation'),
																					'value'=>HALOConfig::get('message.defaultDisplayLimit'))));
			$globalCfg->builder->addUI('section@array',$section);
		}

		//settings section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('File Upload')));
		
		$section->addUI('file.allowedExtensions', HALOUIBuilder::getInstance('','form.text',array('name'=>'file.allowedExtensions',
																				'title'=>__halotext('Allowed Extensions'),
																				'helptext'=>__halotext('Only files with approved extensions are permitted. Separate multiple extensions by using the comma (,) character'),
																				'value'=>HALOConfig::get('file.allowedExtensions'))));
		$globalCfg->builder->addUI('section@array',$section);

		//Push server section
		if(HALOAuth::can('feature.push')){
			$section = HALOUIBuilder::getInstance('pushserver','config.section',array('title'=>__halotext('Push Server')))
							->addUI('pushserver.enable', HALOUIBuilder::getInstance('','form.radio',array('name'=>'pushserver.enable',
																							'title'=>__halotext('Enable push server'),
																							'helptext'=>__halotext('Push server is used for realtime notifications and content updating'),
																							'options'=>array(HALOObject::getInstance(array('value'=>1,'title'=>__halotext('Remote'))),
																											HALOObject::getInstance(array('value'=>0,'title'=>__halotext('Local')))
																											),
																							'value'=>HALOConfig::get('pushserver.enable',0))))
							->addUI('pushserver.address', HALOUIBuilder::getInstance('','form.text',array('name'=>'pushserver.address','title'=>'Push Server Address',
																										'placeholder' => 'Ex: ' . $_SERVER['HTTP_HOST'] . ':8100',
																										'helptext'=>__halotext('Configure the push server address. The push server is used for realtime notifications, chats & streaming service'),
																										'value'=>HALOConfig::get('pushserver.address'))))
							;
			$globalCfg->builder->addUI('section@array',$section);					
		}
		
		return $globalCfg;
	}
		
	protected function getPhotoCfg(){
		$photoCfg = new stdClass();
		$photoCfg->title = "Photos";
		$photoCfg->icon = "";
		$photoCfg->builder = HALOUIBuilder::getInstance('photo_cfg','config.group',array('name'=>'photo_cfg'));
		
		//settings section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Settings')));
		
		$section->addUI('photo.allowedExtensions', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.allowedExtensions',
																				'title'=>__halotext('Allowed Extensions'),
																				'helptext'=>__halotext('Only photo with approved extensions are permitted. Separate multiple extensions by using the comma (,) character'),
																				'value'=>HALOConfig::get('photo.allowedExtensions'))));
		$section->addUI('photo.avatarSize', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.avatarSize',
																				'title'=>__halotext('Avatar Size'),
																				'helptext'=>__halotext('Photo size used in photo listing, photo thumbnail'),
																				'value'=>HALOConfig::get('photo.avatarSize'))));
		$section->addUI('photo.thumbSize', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.thumbSize',
																				'title'=>__halotext('Thumb Size'),
																				'helptext'=>__halotext("Photo size used in user's thumbnail"),
																				'value'=>HALOConfig::get('photo.thumbSize'))));
		$section->addUI('photo.smallSize', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.smallSize',
																				'title'=>__halotext('Small Size'),
																				'helptext'=>__halotext("Photo size used as actor's thumbnail in stream and notifications"),
																				'value'=>HALOConfig::get('photo.smallSize'))));
		$section->addUI('photo.listSize', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.listSize',
																				'title'=>__halotext('List Size'),
																				'helptext'=>__halotext('Photo size used in auto suggest search result'),
																				'value'=>HALOConfig::get('photo.listSize'))));
		$section->addUI('photo.coverSize', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.coverSize',
																				'title'=>__halotext('Cover Size'),
																				'helptext'=>__halotext('Photo size used in cover picture'),
																				'value'=>HALOConfig::get('photo.coverSize'))));
		$section->addUI('photo.coverSmall', HALOUIBuilder::getInstance('','form.text',array('name'=>'photo.coverSmall',
																				'title'=>__halotext('Cover Small Size'),
																				'helptext'=>__halotext('Small photo size used in cover picture'),
																				'value'=>HALOConfig::get('photo.coverSmall'))));
		$photoCfg->builder->addUI('section@array',$section);
		
		return $photoCfg;	
	}
		
	protected function getOAuthCfg(){
		$oAuthCfg = new stdClass();
		$oAuthCfg->title = "OAuth Settings";
		$oAuthCfg->icon = "";
		$oAuthCfg->builder = HALOUIBuilder::getInstance('oauth_cfg','config.group',array('name'=>'oauth_cfg'));
		
		//facebook section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Facebook')));
		
		$section->addUI('oauth.facebookClientId', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.facebookClientId',
																				'title'=>__halotext('Facebook API Key'),
																				'helptext'=>__halotext('Configure Facebook API Key'),
																				'value'=>HALOConfig::get('oauth.facebookClientId'))));
		$section->addUI('oauth.facebookClientSecret', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.facebookClientSecret',
																				'title'=>__halotext('Facebook API Secret'),
																				'helptext'=>__halotext('Configure Facebook API Secret'),
																				'value'=>HALOConfig::get('oauth.facebookClientSecret'))));
		$section->addUI('oauth.facebookScope', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.facebookScope',
																				'title'=>__halotext('Facebook Scope'),
																				'helptext'=>__halotext('Configure Facebook Access Scope'),
																				'value'=>HALOConfig::get('oauth.facebookScope','email'))));
		$section->addUI('oauth.redirect', HALOUIBuilder::getInstance('','form.alert',array('title'=>__halotext('Facebook redirect URIs'),
																				'helptext'=>__halotext('Google redirect URIs'),
																				'type' => 'success'
																				)));
		$oAuthCfg->builder->addUI('section@array',$section);
		
		//google section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Facebook')));
		
		$section->addUI('oauth.googleClientId', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.googleClientId',
																				'title'=>__halotext('Google Client ID'),
																				'helptext'=>__halotext('Configure Google Client ID'),
																				'value'=>HALOConfig::get('oauth.googleClientId'))));
		$section->addUI('oauth.googleClientSecret', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.googleClientSecret',
																				'title'=>__halotext('Google Client Secret'),
																				'helptext'=>__halotext('Configure Google Client Secret'),
																				'value'=>HALOConfig::get('oauth.googleClientSecret'))));
		$section->addUI('oauth.googleScope', HALOUIBuilder::getInstance('','form.text',array('name'=>'oauth.googleScope',
																				'title'=>__halotext('Google Scope'),
																				'helptext'=>__halotext('Configure Google Access Scope'),
																				'value'=>HALOConfig::get('oauth.googleScope','userinfo_email,userinfo_profile'))));
		$section->addUI('oauth.redirect', HALOUIBuilder::getInstance('','form.alert',array('title'=>__halotext('Google Redirect URLs'),
																				'helptext'=>__halotext('Google redirect URIs'),
																				'type' => 'success'
																				)));
		$oAuthCfg->builder->addUI('section@array',$section);
		
		return $oAuthCfg;	
	}
	
	protected function getSocialCfg(){
		//load social settings
		
		$settings = HALOUtilHelper::getSocialSettings();

		$oSocialCfg = new stdClass();
		$oSocialCfg->title = $settings->social->_meta->title;
		$oSocialCfg->icon = $settings->social->_meta->icon;
		$oSocialCfg->help = $settings->social->_meta->help;
		$oSocialCfg->builder = HALOUIBuilder::getInstance('social_cfg','config.group',array('name'=>'social_cfg'));

		foreach (HALOUtilHelper::getSettingList($settings,'social') as $secName => $secVal){
			$section = HALOUIBuilder::getInstance('','config.section',array('title'=>$secVal->_meta->title));
			foreach(HALOUtilHelper::getSettingList($settings,'social.' . $secName) as $fieldName => $fieldVal){
				$section->addUIJSON($fieldName, HALOObject::getInstance($fieldVal));
			}
			$oSocialCfg->builder->addUI('section@array',$section);
		}


		// Google Analytics Settings
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Google Analytics Settings')));
		
		$section->addUI('ga.enable', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.GAEnable',
																				'title'=>__halotext('Enable Google Analytics Tracking'),
																				'helptext'=>__halotext('Enable or disable customized Google Analytics tracking function'),
																				'options'=>array(array('value'=>1,'title'=>__halotext('Yes')),
																								array('value'=>0,'title'=>__halotext('No'))
																				),
																				'value'=>HALOConfig::get('global.GAEnable', 0))))
				->addUI('ga.trackingId', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.GATrackingId',
																				'title'=>__halotext('Google Analytics Tracking ID'),
																				'helptext'=>__halotext('Configure Google Analytics Tracking ID'),
																				'value'=>HALOConfig::get('global.GATrackingId', ''))))
				->addUI('ga.viewId', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.GAViewId',
																				'title'=>__halotext('Google Analytics View ID'),
																				'helptext'=>__halotext('Configure Google Analytics View ID'),
																				'value'=>HALOConfig::get('global.GAViewId', ''))))
				->addUI('ga.enableDebug', HALOUIBuilder::getInstance('','form.radio',array('name'=>'global.GAEnableDebug',
																				'title'=>__halotext('Enable Google Analytics Debugging'),
																				'helptext'=>__halotext('Enable/disable Google Analytics debugging mode (for developer only)'),
																				'options'=>array(array('value'=>1,'title'=>__halotext('Yes')),
																								array('value'=>0,'title'=>__halotext('No'))
																				),
																				'value'=>HALOConfig::get('global.GAEnableDebug', 0))))
				->addUI('ga.pageGroupDimension', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.GAPageTypeDimension',
																				'title'=>__halotext('Google Analytics Dimension Index for page type'),
																				'helptext'=>__halotext('Configure Google Analytics Dimension index for page type tracking. Page types include: homepage, profile page, photo page.'),
																				'value'=>HALOConfig::get('global.GAPageTypeDimension', ''))))
				->addUI('ga.regsiteredUserDimension', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.GARegsiteredUserDimension',
																				'title'=>__halotext('Google Analytics Dimension Index for registered user'),
																				'helptext'=>__halotext('Configure Google Analytics Dimension index for user type tracking.'),
																				'value'=>HALOConfig::get('global.GARegsiteredUserDimension', ''))))
		;

		$oSocialCfg->builder->addUI('section@array',$section);
		
		//Google API
		// $section = HALOUIBuilder::getInstance('googleapi','config.section',array('title'=>__halotext('Google API Auth')))
							// ->addUI('global.googleapiclient', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.googleAPIClientId','title'=>'Google API ClientId',
																										// 'helptext'=>__halotext('Google API ClientId for your site'),
																										// 'value'=>HALOConfig::get('global.googleAPIClientId'))))
							// ->addUI('global.googleapikey', HALOUIBuilder::getInstance('','form.text',array('name'=>'global.googleAPIKey','title'=>'Google API Key',
																										// 'helptext'=>__halotext('Google API Key for your site'),
																										// 'value'=>HALOConfig::get('global.googleAPIKey'))))
							// ;
		// $oSocialCfg->builder->addUI('section@array',$section);					

		return $oSocialCfg;	
		
	}
	
	protected function getNotificationCfg(){
		$notifCfg = new stdClass();
		$notifCfg->title = "Notifications";
		$notifCfg->icon = "";
		$notifCfg->builder = HALOUIBuilder::getInstance('notif_cfg','config.group',array('name'=>'notif_cfg'));
		
		//default notification section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Default Notification Settings')));
		
		$notifSettings = HALONotificationAPI::loadNotificationSettings();
		$section->addUI('notification.default', HALOUIBuilder::getInstance('','notification.default',array('settings'=>$notifSettings)));
		$notifCfg->builder->addUI('section@array',$section);
				
		return $notifCfg;	
	}
		
	protected function getUserpointCfg(){
		$userpointCfg = new stdClass();
		$userpointCfg->title = "User Points";
		$userpointCfg->icon = "";
		$userpointCfg->builder = HALOUIBuilder::getInstance('userpoint_cfg','config.group',array('name'=>'userpoint_cfg'));
		
		//karma section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Karma')));
		
		$section->addUI('userpoint.level.1', HALOUIBuilder::getInstance('','form.text',array('name'=>'userpoint.level.1',
																				'title'=>__halotext('Minimum points for level 1'),
																				'helptext'=>__halotext('Configure level 1 user points'),
																				'value'=>HALOConfig::get('userpoint.level.1'))));
		$section->addUI('userpoint.level.2', HALOUIBuilder::getInstance('','form.text',array('name'=>'userpoint.level.2',
																				'title'=>__halotext('Minimum points for level 2'),
																				'helptext'=>__halotext('Configure level 2 user points'),
																				'value'=>HALOConfig::get('userpoint.level.2'))));
		$section->addUI('userpoint.level.3', HALOUIBuilder::getInstance('','form.text',array('name'=>'userpoint.level.3',
																				'title'=>__halotext('Minimum points for level 3'),
																				'helptext'=>__halotext('Configure level 3 user points'),
																				'value'=>HALOConfig::get('userpoint.level.3'))));
		$section->addUI('userpoint.level.4', HALOUIBuilder::getInstance('','form.text',array('name'=>'userpoint.level.4',
																				'title'=>__halotext('Minimum points for level 4'),
																				'helptext'=>__halotext('Configure level 4 user points'),
																				'value'=>HALOConfig::get('userpoint.level.4'))));
		$section->addUI('userpoint.level.5', HALOUIBuilder::getInstance('','form.text',array('name'=>'userpoint.level.5',
																				'title'=>__halotext('Minimum points for level 5'),
																				'helptext'=>__halotext('Configure level 5 user points'),
																				'value'=>HALOConfig::get('userpoint.level.5'))));
		$userpointCfg->builder->addUI('section@array',$section);
		
		//rule section
		$section = HALOUIBuilder::getInstance('','config.section',array('title'=>__halotext('Rules')));
		$userpointSettings = HALOUserpointAPI::loadUserpointSettings();
		$section->addUI('userpoint.rules', HALOUIBuilder::getInstance('','userpoint_settings',array('settings'=>$userpointSettings)));
		$userpointCfg->builder->addUI('section@array',$section);
		
		return $userpointCfg;	
	}
		
}
