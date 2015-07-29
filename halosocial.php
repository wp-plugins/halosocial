<?php
/*
Plugin Name: HaloSocial
Plugin URI: http://halo.social/?return=true
Description: Social Network plugin for Wordpress
Version: 1.0.0
Author: HaloSocial
Author URI: http://halo.social/wordpress-plugins/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/** If you hardcode a WP.com API key here, all key config screens will be hidden */

$wp_rewrite = new WP_Rewrite();
/****************************** installation checking ***************************/
/**
 * Return list of additional download lib for this plugin.
 * @author Hung Tran <hungtran@halo.social>
 * @category Installer
 * @param string $type filter lib by type
 * @return string[] $libs
 */
function halo_get_pkg_list($type = 'all') {
	$pkgs = array(
		'lib_pkg_1' => array('id' => 'lib_pkg_1', 'title' => "HALO Lib 1", 'fileid' => "0Bzy0AS02wkInUVk1eEFteVFvMkU", 'ver' => '1'),
		'lib_pkg_2' => array('id' => 'lib_pkg_2', 'title' => "HALO Lib 2", 'fileid' => "0Bzy0AS02wkInZEM3RU1YNzl0UkU", 'ver' => '1'),
		'lib_pkg_3' => array('id' => 'lib_pkg_3', 'title' => "HALO Lib 3", 'fileid' => "0Bzy0AS02wkInd3ROY29EZ0c0MHc", 'ver' => '1'),
		'lib_pkg_4' => array('id' => 'lib_pkg_4', 'title' => "HALO Lib 4", 'fileid' => "0Bzy0AS02wkInZUdUM1BkdXBPTlU", 'ver' => '1'),
		'lib_pkg_5' => array('id' => 'lib_pkg_5', 'title' => "HALO Lib 5", 'fileid' => "0Bzy0AS02wkInQnF4MkV2aS1iemM", 'ver' => '1'),
		'lib_pkg_6' => array('id' => 'lib_pkg_6', 'title' => "HALO Lib 6", 'fileid' => "0Bzy0AS02wkInTUZzZHExRjdJY3M", 'ver' => '1'),
		'lib_pkg_7' => array('id' => 'lib_pkg_7', 'title' => "HALO Lib 7", 'fileid' => "0Bzy0AS02wkInSUdCRnpCTGVlTms", 'ver' => '1'),
		'lib_pkg_8' => array('id' => 'lib_pkg_8', 'title' => "HALO Lib 8", 'fileid' => "0Bzy0AS02wkInd2VrLTdzOUxZc0E", 'ver' => '1'),
		'lib_pkg_9' => array('id' => 'lib_pkg_9', 'title' => "HALO Lib 9", 'fileid' => "0Bzy0AS02wkIndDJERHNrZno4U2M", 'ver' => '1'),
	);
	$lockFile = dirname(__FILE__) . '/pkg.lock';
	switch($type) {
		case 'installed':
			if(file_exists($lockFile)) {
				$content = file_get_contents($lockFile);
				try {
					$installedPkgs = json_decode($content, true);
					return $installedPkgs;
				} catch (\Exception $e) {
					return array();
				}
			} else {
				return array();
			}
			break;
		case 'notInstalled':
			if(file_exists($lockFile)) {
				$content = file_get_contents($lockFile);
				try {
					$installedPkgs = json_decode($content, true);
					foreach($installedPkgs as $index => $pkg) {
						if(isset($pkgs[$index])) {
							unset($pkgs[$index]);
						}
					}
				} catch (\Exception $e) {
					return $pkgs;
				}
			}
			break;
		case 'all':
			break;
	}
	return $pkgs;
}


/**
 * Check if required libs are installed.
 * @author Hung Tran <hungtran@halo.social>
 * @category Installer
 * @param null
 * @return boolean
 */
function has_halo_pkgs() {
	$installedPkg = halo_get_pkg_list('installed');
	$allPkg = halo_get_pkg_list('all');
	$str1 = json_encode($installedPkg);
	$str2 = json_encode($allPkg);
	return ($str1 == $str2);
}

//add settings link in plugin page
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'halo_add_action_links' );

/**
 * Wordpress hook call back to add Setting link to the plugins page.
 * @author Hung Tran <hungtran@halo.social>
 * @category Installer
 */
function halo_add_action_links ( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'admin.php?page=halo_dashboard' ) . '">Settings</a>',
	);
	return array_merge( $links, $mylinks );
}

register_activation_hook(__FILE__, 'halo_plugin_activate');
add_action('admin_init', 'halo_plugin_setting_redirect');

function halo_plugin_activate() {
    add_option('halosocial_plugin_do_activation_redirect', true);
}

function halo_plugin_setting_redirect() {
    if (get_option('halosocial_plugin_do_activation_redirect', false)) {
        delete_option('halosocial_plugin_do_activation_redirect');
		wp_redirect("admin.php?page=halo_dashboard");
    }
}

/****************************** installation checking ***************************/
if(!has_halo_pkgs()) {
	require_once(dirname(__FILE__) . '/pkginstall.php');
} else {
	/*******************************************************************************/
	/*******************************************************************************/
	/***************************** Hook define section *****************************/
	/*******************************************************************************/
	/*******************************************************************************/
	//plugin init
	add_action('init', 'halo_init');

	add_action('shutdown', function(){
		$last_error = error_get_last();
		if ($last_error['type'] === E_ERROR) {
			var_dump('HALO Fatal:', $last_error['message'], $last_error['file'], $last_error['line']);	
		}	
		
	});

	//ajax task registration
	if ( is_admin() )
	{
		add_action('wp_ajax_halo_ajax', 'halo_ajax_process');
		add_action('wp_ajax_nopriv_halo_ajax', 'halo_ajax_process');

		add_action('wp_ajax_halo_dismiss', 'halo_dismiss_notice');
		add_action('wp_ajax_nopriv_halo_dismiss', 'halo_dismiss_notice');

		add_filter( 'wp_prepare_themes_for_js', 'halo_hide_halo_theme' );

	} else {

	}

	//login handling
	//add_filter('authenticate', 'halo_authenticate',20,3);


	//logout handling
	//add_action('wp_logout', 'halo_logout');


	//admin menu
	add_action('admin_menu', 'halo_admin_page');

	/*******************************************************************************/
	/*******************************************************************************/
	/***************************** End Hook define section *************************/
	/*******************************************************************************/
	/*******************************************************************************/

	function halo_strip_wp_magic_quotes(){
		$_GET    = stripslashes_deep( $_GET    );
		$_POST   = stripslashes_deep( $_POST   );
		$_COOKIE = stripslashes_deep( $_COOKIE );
		$_REQUEST = stripslashes_deep( $_REQUEST );
	}

	define ('HALO_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

	define ('HALO_PLUGIN_VER','1.0.0');

	define ('HALO_RELEASE_DATE', '2015-07-29');
	
	define ('HALO_PLUGIN_PRODUCT_TYPE', 'STARTER');

	// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
	define( 'EDD_HALOSOCIAL_STORE_URL', ' http://www.halo.social/' );

	// the name of your product. This should match the download name in EDD exactly
	define( 'EDD_HALOSOCIAL_ITEM_NAME', 'HaloSocial Starter' );
	define( 'EDD_HALOSOCIAL_DOWNLOAD_ID', 121 );

	
	define( 'EDD_HALOSOCIAL_CHECKOUT_URL', 'https://www.halo.social/checkout/' );
	
	/**
	 * Bootstrap the HaloSocial framework.
	 * @author Hung Tran <hungtran@halo.social>
	 * @category bootstrap
	 */
	function halo_init() {
		//detect request to halosocial
		if(halo_isHalo()){
			$halo_pageId = halo_getPageId(); //need to detect the page_id for halosocial

			haloBoot();

			haloStartSession();

			remove_action('wp_head', 'rel_canonical');//override default canonical url
			
			//enqueue meta head
			add_action('wp_head', 'halosocial_enqueue_meta');

			//enqueue css to the page header
			add_action('wp_head', 'halosocial_enqueue_styles');

			//enqueue metatag the page header
			add_action('wp_head', 'halo_render_metatags');

			//enqueue script to the end of document
			add_action('wp_footer', 'halosocial_enqueue_scripts');
			//add_action('tlsocial_enqueue_script', 'tlsocial_enqueue_scripts');

			//plugin content
			add_shortcode('HALO_CONTENT', 'halo_content');

			//load subscribers
			HALOSubscriber::load(array('system','activity','notification','quota','userpoint','stream','photo'));

			//listen to custom events
			Event::listen('system.onAfterChangeConfig', function()
			{
				//sync halo configure with wordpress configure
				update_option('halo_use_halo_avatar', HALOConfig::get('user.usehaloavatar', 1));
			});
			
			//load query log
			//HALOSubscriber::load(array('queryLog'));

			//add rewrite rules
			HALORouter::insertRewriteRules();
			
			//register hook to hide page info
			halo_hide_page_info();

			////////////////////////////////////////////////////////////////////////////
			//list of special route that need to be processed before rendering content//
			////////////////////////////////////////////////////////////////////////////
			
			//for logout
			$requestUrl = Request::url();
			if((Input::get('view') == 'user' && Input::get('task') == 'logout') || 
				(get_option('permalink_structure') && Url::to('?view=user&task=logout') == $requestUrl)){
				HALOUserModel::logout();
				wp_redirect(get_permalink( $halo_pageId ));
				exit;
			}
			
			//for forgot password
			if(Input::get('view') == 'user' && Input::get('task') == 'forgot'){
				wp_redirect(wp_lostpassword_url( get_permalink( $halo_pageId ) ));
				exit;
			}
			
			//for post login
			if(Input::isMethod('post') && Input::get('view') == 'user' && Input::get('task') == 'login'){
				$controller = new UserController();
				$redirect = $controller->postLogin();
				wp_redirect($redirect->getTargetUrl());
				exit;			
			}

			//for post register
			if(Input::isMethod('post') && Input::get('view') == 'user' && Input::get('task') == 'register'){
				$controller = new UserController();
				$redirect = $controller->postRegister();

				if(!is_object($redirect)){
					wp_redirect($redirect);
					exit;
				}
				//clear any preprocessing message
				HALOResponse::clearMessages();
			}
			
			//for oauth login
			if(Input::get('view') == 'user' && Input::get('task') == 'oauthLogin' && Input::get('uid') && Input::get('code')){
				$controller = new UserController();
				$redirect = $controller->getOauthLogin(Input::get('uid'));
				wp_redirect($redirect->getTargetUrl());
				exit;			
			}

			//start rendering response content
			startHALOResponse();
		}
	}

	function isHALOInstalled(){
		return file_exists(HALO_PLUGIN_PATH . '/install/install.lock');
	}

	//auto configure theme for this page post
	add_action('setup_theme', 'halo_theme_redirect');


	/**
	 * Wordpress theme redirect hook.
	 * @author Hung Tran <hungtran@halo.social>
	 * @category system
	 */
	function halo_theme_redirect() {
		if ( isset($_GET['preview']) ) return;
		if(halo_isHalo()){
			haloBoot();
			if(HALOConfig::get('global.theme', 'active_theme') == 'halo_theme') {
				$halo_pageId = halo_getPageId(); //need to detect the page_id for halosocial
				$current_theme = wp_get_theme();
				add_filter( 'template', 'halo_configure_theme' );
				add_filter( 'stylesheet', 'halo_configure_theme' );

				// Prevent theme mods to current theme being used on theme being previewed
				add_filter( 'pre_option_mods_' . $current_theme->name, '__return_empty_array' );
			} else {
				//add css for wordpress template
				add_action('wp_head', 'halo_theme_comp_head');
				HALOAssetHelper::addCss('assets/css/wptheme_comp.css');
				HALOAssetHelper::addScript('assets/js/wptheme_comp.js');
			}
			//load default css
			HALOAssetHelper::loadDefaultCss();
			//load modernize js
			HALOAssetHelper::addScript('assets/js/modernizr.js');
			HALOAssetHelper::loadDefaultScript();
		}
	}

	function halo_hide_halo_theme( $themes) {
		if(isset($themes['halodefault'])) {
			unset($themes['halodefault']);
		}
		return $themes;
	}
	
	function halo_hide_page_info() {
		//filter to remove edit page link
		add_filter( 'get_edit_post_link', 'halo_remove_edit_page_link', 10, 3 );
		
		//insert marker to suppress page title
		// add_filter( 'the_title', 'halo_suppress_title', 10, 2 );

		//filter to hide page time
		add_filter('the_time', 'halo_suppress_content');

		//filter to hide page date
		add_filter('the_date', 'halo_suppress_content');

		//filter to hide page date
		add_filter('the_author', 'halo_suppress_content');
	}

	function halo_suppress_content($text){
		return '';
	}

	function halo_remove_edit_page_link($id, $id, $context) {
		return '';
	}

	function halo_theme_comp_head() {
		echo HALOUIBuilder::getInstance('', 'module.head', array())->fetch();
	}

	//reset user sync cache on new user inserted
	add_action ('user_register', 'halo_insert_user_action_hook');

	function halo_insert_user_action_hook($userid) {
		haloBoot();
		Cache::forget('halo_sync_user');
	}


	if(isHALOInstalled() && get_option('halo_use_halo_avatar', 1)){
		// avatar filter
		add_filter( 'get_avatar' , 'halo_custom_avatar' , 1 , 5 );

		function halo_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
			$user = false;
			if ( is_numeric( $id_or_email ) ) {
				$id = (int) $id_or_email;
				$user = get_user_by( 'id' , $id );
			} elseif ( is_object( $id_or_email ) ) {
				if ( ! empty( $id_or_email->user_id ) ) {
					$id = (int) $id_or_email->user_id;
					$user = get_user_by( 'id' , $id );
				}
			} else {
				$user = get_user_by( 'email', $id_or_email );	
			}
			$avatar = '';
			if ( $user && is_object( $user ) ) {
				$avatarSizes = get_user_option('halo_user_avatar_urls', $user->data->ID);
				if(empty($avatarSizes)) $avatarSizes = array();
				if(!isset($avatarSizes[$size])) {
					haloBoot();
					$haloUser = HALOUserModel::getUser($user->data->ID);
					$avatarUrl = $haloUser->getAvatar($size);
					$avatarSizes[$size] = $avatarUrl;
					update_user_option($user->data->ID, 'halo_user_avatar_urls', $avatarSizes);
				}
				
				$avatarUrl = $avatarSizes[$size];			
				$avatar = "<img alt='{$alt}' src='{$avatarUrl}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			}
			return $avatar;
		}
	}

	//blocked user
	add_action( 'wp_login', 'halo_user_login', 10, 2 );
	global $__haloUserFlag;
	function halo_user_login($user_login, $user) {

		// Get user meta
		$blocked = (boolean)get_user_option( 'halo_blocked_user', $user->ID);
		$confirmed = get_user_option( 'halo_confirmed_user', $user->ID);
		
		if($confirmed === false) {
			//default is confirmed
			$confirmed = 1;
		}
		
		// Is the use logging in disabled?
		global $__haloUserFlag;
		if(!$__haloUserFlag) {
			if ( $blocked || !$confirmed) {
				// Clear cookies, a.k.a log user out
				wp_clear_auth_cookie();
				// Build login URL and then redirect
				$login_url = site_url( 'wp-login.php', 'login' );
				if($blocked) {
					$login_url = add_query_arg( 'blocked', '1', $login_url );
				} elseif (!$confirmed) {
					$login_url = add_query_arg( 'confirmed', '1', $login_url );
				}
				wp_redirect( $login_url );
				exit;
			}
		}
	}

	function halo_get_subcribe_url(){
		return 'http://tiny.cc/halosocial-pricing';
	}
	
	function halo_get_comparision_url(){
		return 'http://tiny.cc/halosocial-pricing';
	}
	
	function halo_check_license_stat(){
		$now = time();
		$lastCheck = intval ( get_option( 'edd_halo_social_last_check_stick' ) );
		$licenseKey = get_option( 'edd_halo_social_license_key' );
		if(empty($licenseKey)){
			return 1;
		}
		$stat = 1;
		if(($now - $lastCheck) > 60 * 60 * 24){
			haloBoot();
			$license = HALOConfig::loadLicense();
			if($license){
				if($license->license !== 'valid') {
					if(isset($license->error)) {
						switch($license->error) {
							case 'expired':
								$stat = 2;
								break;
							default:
								$stat = 1;
								break;
						}
					} else {
						$stat = 4;
					}
				} else {
					$stat = 0;
				}
			} else {
				$stat = 4;
			}
			update_option( 'edd_halo_social_last_check_stick', $now );
			update_option( 'edd_halo_social_license_stat', $stat );
		}
		return get_option( 'edd_halo_social_license_stat' );
	}
	
	function halo_admin_notice() {
		$haloSocialLink = '<a target="_black" href="'. EDD_HALOSOCIAL_STORE_URL .'">HaloSocial</a>';
		$subscribeLink = '<a target="_black" href="'. halo_get_subcribe_url() .'">Subscribe</a>';
		$registerDomainLink = '<a target="_black" href="'. EDD_HALOSOCIAL_STORE_URL .'">here</a>';
		$dismissLink = '<a href="javascript:void(0)" onclick="jQuery(\'.halo-starter-notice\').hide();jQuery.post(ajaxurl, {\'action\': \'halo_dismiss\'}, function(response) {});">Dismiss</a>';
		$comparisonLink = '<a target="_black" href="'. halo_get_comparision_url() .'">Click here for a comparison table.</a>';
		$licenseLink = '<a href="'. admin_url( 'admin.php?page=halo_dashboard&view=tools&task=license' ) .'">License Page</a>';
		$message = '';
		
		if(HALO_PLUGIN_PRODUCT_TYPE === 'STARTER'){
			if(!get_option( 'halo_social_notice_dismiss' )){
				$message = sprintf("You are using HaloSocial Starter. To get access to all the great features like events, groups, pages, messages, classifieds and more, please upgrade to Professional or Agency versions. [%s] | %s", $comparisonLink, $dismissLink);
			}
		} else {
			$licenseStat = halo_check_license_stat();
			switch ($licenseStat){
				case 0:	//valid
					if(HALO_PLUGIN_PRODUCT_TYPE === 'PROFESSIONAL'){
						if(!get_option( 'halo_social_notice_dismiss' )){
							$message = sprintf("You are using HaloSocial Professional. To get access to more great features like pages, ACL, classifieds, labels and more, please upgrade to Agency version. [%s] | %s", $comparisonLink, $dismissLink);
						}
					}
					break;
				case 1:	//empty
					$message = sprintf("License for HaloSocial is missing or invalid. Please visit %s and enter a valid license to activate it.", $licenseLink);
					break;
				case 2: //expired
					$message = sprintf("License for HaloSocial has expired. Please renew your license on %s", $haloSocialLink);
					break;
				case 3:	//wrong domain
					$message = sprintf("This domain is not registered, you can still use HaloSocial, but you will need to register your domain to get technical support. You can do it %s.", $registerDomainLink);
					break;
				case 4:	//server down
					break;
			}
		}
		if($message){
		?>
		<div class="error halo-starter-notice halo-notice-hide">
			<p><?php echo $message?></p>
		</div>
		<?php
		}


	}
	add_action( 'admin_notices', 'halo_admin_notice' );

	add_filter( 'login_message', 'halo_user_login_message');

	function halo_user_login_message( $message ) {

		// Show the error message if it seems to be a disabled user
		if ( isset( $_GET['blocked'] ) && $_GET['blocked'] == 1 ) {
			$message =  '<div id="login_error">' . __( 'Your account has been blocked. Please contact the administrator for more information.') . '</div>';
		}
		
		if ( isset( $_GET['confirmed'] ) && $_GET['confirmed'] == 1 ) {
			$message =  '<div id="login_error">' . __( 'Your account has not been actived.') . '</div>';
		}
		
		return $message;
	}


	/**
	 * Suppress HaloSocial page title hook.
	 * @author Hung Tran <hungtran@halo.social>
	 */
	function halo_suppress_title( $title, $id = null ) {
		return '';
	}

	function halo_configure_theme( $template = '' ) {
		return 'halodefault';
	}

	function halo_admin_page(){
		//include admin code

		// haloStartSession();
		
		$parent_slug = 'halo_dashboard';
		$handle = 'halo_admin_content';
		//menu page
		$page = add_menu_page( 	'HaloSocial',
						'HaloSocial',
						//'manage_options',
						'publish_pages',
						$parent_slug,
						$handle,
						plugins_url( 'halosocial/app/views/default/assets/ico/favicon-16x16.png' ),
						6 );
		/* Using registered $page handle to hook stylesheet loading */
		add_action( 'admin_print_styles-' . $page, 'halosocial_admin_enqueue_styles' );
		add_action( 'admin_print_scripts-' . $page, 'halosocial_admin_enqueue_scripts' );
	}


	/**
	 * Handling user logout.
	 * @author Hung Tran <hungtran@halo.social>
	 * @category system
	 * @return void
	 */
	function halo_logout() {

		session_destroy();
	}


	function halo_ajax_process() {
		haloBoot();
		startHALOResponse();
		//forward to ajax controller
		$ajaxController = new AjaxController();
		echo $ajaxController->call();
		exit;
	}

	function halo_dismiss_notice(){
		update_option( 'halo_social_notice_dismiss', 1 );
		exit;
	}

	function halosocial_enqueue_scripts($loadjQuery=false,$loadBootstrap=true) {
		haloBoot();
		HALOAssetHelper::loadDefaultScript();
	}

	function halosocial_admin_enqueue_scripts() {
		haloBoot();
		HALOAssetHelper::loadDefaultScript('admin');

	}

	function halosocial_admin_enqueue_styles() {
		haloBoot();
		HALOAssetHelper::loadDefaultCss('admin');
	}

	function halosocial_enqueue_meta() {
		if(halo_isHalo()){
			haloBoot();
			echo HALOUIBuilder::getInstance('', 'module.head', array())->fetch();
		}
	}

	function halosocial_enqueue_styles() {
		haloBoot();
		//HALOAssetHelper::loadDefaultCss();
	}

	function halo_render_metatags() {
		haloBoot();
		Event::fire('module.onLoadModule', 'metatag', 'default');
	}

	function halo_remove_comments_template_on_pages( $file ) {
		if ( is_page() )
		$file = dirname(__FILE__) . '/blank.php';
		return $file;
	}

	function halo_admin_content(){

		haloBoot();
		//enqueue css to the page header
		//add_action('wp_head', 'halosocial_enqueue_styles');

		//enqueue script to the end of document
		//add_action('wp_footer', 'halosocial_enqueue_scripts');

		//load query log
		//HALOSubscriber::load(array('queryLog'));

		startHALOResponse();
		//forward to halo_content process
		halo_content();
	}

	function haloBoot(){
		static $booted = false;
		if(!$booted){
			try {
				halo_strip_wp_magic_quotes();
				//init the autoload
				require_once __DIR__.'/bootstrap/autoload.php';

				//init  application
				require_once __DIR__.'/bootstrap/start.php';

				//init facades
				require_once __DIR__.'/app/facade.php';

				//prepare request before processing
				$app = app();
				$app->prepareRequest($app['request']);

				$app->boot();

				//setup session cookies
				
				//turn off error reporting
				if(!HALOConfig::get('global.reportErrors', 0)) {
					error_reporting(0);
				}

			} catch(\Exception $e){
				var_dump($e->getMessage());
				exit;
			}
		}
	}

	function haloStartSession() {
		if(halo_getPageId()){
			haloBoot();
			if (Session::getName() && !isset($_COOKIE[Session::getName()])) {
				setcookie(Session::getName(), Session::getId(), strtotime('+30 day'));
			}
		}
	}

	function finishHALOResponse(){
		Session::save();
	}
	function startHALOResponse(){
		//start session
		$app = app();
		$sessionReject = $app->bound('session.reject') ? $app['session.reject'] : null;
		$client = with(new \Stack\Builder)
						->push('Illuminate\Cookie\Guard', $app['encrypter'])
						->push('Illuminate\Cookie\Queue', $app['cookie'])
						->push('HALOSessionMiddleware', $app['session'], $sessionReject);

		$stack = $client->resolve($app);

		$response = $stack->handle($app['request']);
		//$response->sendHeaders();

	}

	function halo_content($attr = array()){
		//use default tempalte (will be configured later) @otc
		try {
			//HALOConfig::set('template.folder','default');
		} catch (Exception $e){

		}
		
		//debug enabled
		if(Input::get('debug',false) && HALOAuth::can('backend.view')){
			\Debugbar::enable();
		}
		//transform filter s2p
		HALOFilter::transformS2P();
		
		//disable wordpress page comment
		add_filter( 'comments_template', 'halo_remove_comments_template_on_pages', 20 );
		//init default view
		if(is_admin()){
			$view = 'config';
		} else {
			//$view = 'frontpage';
			$view = 'home';
		}
		$task = '';
		if(!empty($attr)){
			if(isset($attr['view'])){
				$view = $attr['view'];
			}
			if(isset($attr['task'])){
				$task = $attr['task'];
			}
		}

		//merge the query vars parsed by wordpress with to Input object
		if(HALOURL::isSEOEnabled()){
			global $wp;
			if(!empty($wp->query_vars)) {
				Input::merge($wp->query_vars);
			}
		}
		$view   = Input::get('view', $view);
		$task   = Input::get('task', $task);
		$uid	= Input::get('uid', null);
		if ($task != 'halo_ajax')
		{

			// Normal call
			//add controller prefix
			if(is_admin()){
				$controllerName = 'Admin' . ucfirst($view) . 'Controller';
			} else {
				$controllerName = ucfirst($view) . 'Controller';
			}
			if(!isHALOInstalled()){
				//redirect to install
				if(is_admin()){
					$controllerName = 'AdminToolsController';
					$task = 'getInstall';
				} else {
					echo Lang::get('Installation is required');
					return;
				}
			} else {
				//make sure the page_id options is configured
				if(!halo_getPageId()) {
					try{
						$pageId = HALOConfig::get('global.pageId');
						//the page id is not stored in the wp options, just add it back
						if($pageId) {
							delete_option('halo_page_id');
							add_option('halo_page_id',$pageId,'','yes');
						}
					} catch(Exception $e){

					}				
				}
			}
			//check for upgrade process required
			if(is_admin() && isHALOInstalled() && get_option( 'halo_plugin_ver' ) != HALO_PLUGIN_VER){
				$controllerName = 'AdminToolsController';
				$task = 'getUpgrade';		
			}
			//add post/get method prefix depend on the Controller support it or not
			$altTask = null;
			if(empty($task) && Request::getMethod() === 'GET' ){
			//if no task found, then set the altTask as getIndex
				$altTask = 'getIndex';
			}else if(Request::getMethod() === 'POST'){
				$altTask = 'post' . ucfirst($task);
			} elseif (Request::getMethod() === 'GET'){
				$altTask = 'get' . ucfirst($task);
			}

			//prepare args, add $uid as the first parameter
			$args = array();
			if($uid !== null){
				$args[] = $uid;
			}
			//check controller exists
			if ( ! class_exists($controllerName))
			{
				//Redirect::to('index.php')->with('error', 'controller not found');
				echo 'controller not found: ' . $controllerName;
				exit;
			}
			//intialize controller
			$controller = new $controllerName();
			//check if controller task exist
			if( method_exists($controllerName,$task)){
				$ret = call_user_func_array(array($controller,$task),$args);
			} else if (method_exists($controllerName,$altTask)){
				$ret = call_user_func_array(array($controller,$altTask),$args);
			} else {
				//Redirect::to('index.php')->with('error', 'task not found');
				echo 'task not found:' . $controllerName . '@' . $task . ',' . $altTask;
				exit;
			}

			echo $ret->render();
			finishHALOResponse();
			return;
		}
	}

	function halo_finish_install(){
		//check for the page
		$pageId = halo_getPageId();

		if(empty($pageId)){
			try{
				$pageId = HALOConfig::get('global.pageId');
				//the page id is not stored in the wp options, just add it back
				delete_option('halo_page_id');
				add_option('halo_page_id',$pageId,'','yes');
			} catch(Exception $e){

			}
		}
		//create page if not exists
		if(!get_page($pageId)){
			// Create post object
			$haloPage = array(
				'post_title'    => 'HaloSocial',
				'post_content'  => '[HALO_CONTENT]',
				'post_status'   => 'publish',
				'post_type'   => 'page',
				'post_author'   => 1
			);

			// Insert the post into the database
			$pageId = wp_insert_post( $haloPage );
			if(!get_option('halo_page_id')){
				add_option('halo_page_id',$pageId,'','yes');
			} else {
				update_option('halo_page_id',$pageId,'','yes');		
			}
			//store the pageId to db for re-install purpose
			HALOConfig::set('global.pageId',$pageId);
		}
		//store current version
		if(!get_option('halo_plugin_ver')){
			add_option( 'halo_plugin_ver', HALO_PLUGIN_VER, '', 'yes' );
		} else {
			update_option( 'halo_plugin_ver', HALO_PLUGIN_VER, '', 'yes' );
		}
		HALOConfig::set('global.version',HALO_PLUGIN_VER);
		HALOConfig::store();
	}

	/**
	* Return page id of halo page
	* @return	integer
	*/
	function halo_getPageId(){
		static $pageId = null;
		if(is_null($pageId)){
			$pageId = get_option('halo_page_id');
			$pageId = intval($pageId);
		}
		
		return $pageId;
	}

	/**
	* Check if the current request access to halo page
	* @return	boolean	true/false
	*/
	function halo_isHalo() {
		$halo_pageId = halo_getPageId(); //need to detect the page_id for halosocial
		$frontPageId = get_option('page_on_front');
		
        if(!$halo_pageId) return false;
        if(isset($_GET['page_id']) && $_GET['page_id'] == $halo_pageId)	return true;	//no permalink enabled
        
        $path = parse_url(get_permalink($halo_pageId), PHP_URL_PATH);
        $requestUri = $_SERVER['REQUEST_URI'];

        if ($halo_pageId == $frontPageId && $path == $requestUri) {
            return true;
        }

		//if permanlink enabled
		if (get_option('permalink_structure')) {
            if ($halo_pageId == $frontPageId) {
                $path .= 'halosocial';
            }
            $path = str_replace('/', '\/', $path);
            if (preg_match('/\/$/', $path) && preg_match('/^' . $path . '/', $requestUri)) {
                return true;
            }
            return preg_match('/^' . $path . '[\/\?]/', $requestUri);
		}

		return false;	
	}

	// define additional widget
	if(HALO_PLUGIN_PRODUCT_TYPE !== 'STARTER'){
		require_once(dirname(__FILE__) . '/widgets.php');
	}

	// trigger action on complete bootstrap

}


/**
* Check system requirment
* @return	boolean	true/false
*/
function halo_checkRequirement() {
	/************ PHP version ***********/
	if (!defined('PHP_VERSION_ID')) {
		$version = explode('.', PHP_VERSION);

		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}
	if(PHP_VERSION_ID < 50300) {
		return false;
	}
	return true;
}

if( !class_exists( 'Halo_EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

if(HALO_PLUGIN_PRODUCT_TYPE !== 'STARTER') {
	// retrieve our license key from the DB
	$license_key = trim( get_option( 'edd_halo_social_license_key' ) );
	if(get_option( 'edd_halo_social_license_status')) {
		// setup the updater
		$ver = str_replace(array('_pro', '_agency'), '', HALO_PLUGIN_VER);
		$edd_updater = new Halo_EDD_SL_Plugin_Updater( EDD_HALOSOCIAL_STORE_URL . '/edd-api/', __FILE__, array( 
				'version' 	=> $ver, 		// current version number
				'license' 	=> $license_key, 	// license key (used get_option above to retrieve from DB)
				'item_name' => EDD_HALOSOCIAL_ITEM_NAME, 	// name of this plugin
				'author' 	=> 'HaloSocial',  // author of this plugin
				'url'       => home_url()
			)
		);
		//for expired license, skip auto update
		
	}	
}
