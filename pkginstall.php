<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category installer
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */
/*******************************************************************************/
/*******************************************************************************/
/***************************** Hook define section *****************************/
/*******************************************************************************/
/*******************************************************************************/

add_action('shutdown', function(){
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		var_dump('HALO Fatal:', $last_error['message'], $last_error['file'], $last_error['line']);	
	}	
	
});

//ajax task registration
if ( is_admin() )
{
	add_action('wp_ajax_halo_setup_ajax', 'halo_setup_ajax_process');
	add_action('wp_ajax_nopriv_halo_setup_ajax', 'halo_setup_ajax_process');

} else {

}

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

function halo_admin_page(){
	//include admin code

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

function halo_logout() {

	session_destroy();
}

function halo_setup_ajax_process() {
	//var_dump($_GET);
	$pkgId = $_GET['pkg_id'];
	$pkgs = halo_get_pkg_list();
	if(isset($_GET['pkg_id']) && ($pkgId = $_GET['pkg_id'])){
		try {
			$source = "https://drive.google.com/uc?export=download&id=" . $pkgs[$pkgId]['fileid'];
			$destination = dirname(__FILE__) . '/' . $pkgId . '.zip';
			$extractTo = dirname(__FILE__);
			//download the file
			$pkgData = file_get_contents($source);
			file_put_contents($destination, $pkgData);

			//unzip the package
			$zip = new ZipArchive;
			$res = $zip->open($destination);
			if ($res === TRUE) {
				$zip->extractTo($extractTo);
				$zip->close();
				echo '1';
			} else {
				echo 'Invalid package file';
			}
			
			//clean up file
			if(file_exists($destination)) {
				unlink($destination);
			}
			//update the installed pkg list
			halo_set_installed_pkg($pkgs[$pkgId]);
			
		} catch(\Exception $e) {
			echo $e->getMessage();
		}
	} else {
		echo 0;
	}
	exit;
}

function halo_set_installed_pkg($pkg) {
	$lockFile = dirname(__FILE__) . '/pkg.lock';
	$pkgs = halo_get_pkg_list('installed');
	$pkgs[$pkg['id']] = $pkg;
	ksort($pkgs);
	file_put_contents($lockFile, json_encode($pkgs));
}
function halosocial_admin_enqueue_scripts() {

}

function halosocial_admin_enqueue_styles() {

}

function halo_get_version_id($verStr) {
	$version = explode('.', $verStr);
	return ($version[0] * 10000 + $version[1] * 100 + $version[2]);
}
/**
* Check system requirment
* @return	boolean	true/false
*/
function halo_collectRequirements() {
	$rtn = array();
	/************ PHP version ***********/
	if (!defined('PHP_VERSION_ID')) {
		$verId = halo_get_version_id(PHP_VERSION);
		define('PHP_VERSION_ID', $verId);
	}
	$require = '5.3.0';
	$passed = PHP_VERSION_ID >= halo_get_version_id($require);
	$rtn[] = array('title' => 'PHP Version', 'requirement' => $require . '+', 'actual' => PHP_VERSION, 'passed' => $passed);
	
	/************ WordPress version: 3.0 ***********/
	$wpVer = get_bloginfo( 'version' );
	$require = '3.0.0';
	$passed = halo_get_version_id($wpVer) >= halo_get_version_id($require);
	$rtn[] = array('title' => 'WordPress Version', 'requirement' => $require . '+', 'actual' => $wpVer, 'passed' => $passed);
	
	/************ max_execution_time: 30 ***********/
	$checkItem = ini_get('max_execution_time');
	$require = 30;
	$passed = $checkItem >= $require || $checkItem <= 0;
	$rtn[] = array('title' => 'max_execution_time', 'requirement' => $require, 'actual' => $checkItem, 'passed' => $passed);
	
	/************ max_input_time: 30 ***********/
	$checkItem = ini_get('max_input_time');
	$require = 30;
	$passed = $checkItem >= $require || $checkItem <= 0;
	$rtn[] = array('title' => 'max_input_time', 'requirement' => $require, 'actual' => $checkItem, 'passed' => $passed);

	/************ memory_limit: 64 ***********/
	$checkItem = ini_get('memory_limit');
	$require = 64;
	$passed = intval($checkItem) >= $require;
	$rtn[] = array('title' => 'memory_limit', 'requirement' => $require . 'M', 'actual' => $checkItem, 'passed' => $passed);
	
	/************ post_max_size: 8 ***********/
	$checkItem = ini_get('post_max_size');
	$require = 8;
	$passed = intval($checkItem) >= $require;
	$rtn[] = array('title' => 'post_max_size', 'requirement' => $require . 'M', 'actual' => $checkItem, 'passed' => $passed);
	
	/************ upload_max_filesize: 8 ***********/
	$checkItem = ini_get('upload_max_filesize');
	$require = 8;
	$passed = intval($checkItem) >= $require;
	$rtn[] = array('title' => 'upload_max_filesize', 'requirement' => $require . 'M', 'actual' => $checkItem, 'passed' => $passed);
	
	/************ zip extension ***********/
	$checkItem = 'zip';
	$passed = extension_loaded($checkItem);
	$rtn[] = array('title' => $checkItem . ' extension', 'requirement' => '', 'actual' => '', 'passed' => $passed);
	
	/************ PDO extension ***********/
	$checkItem = 'pdo';
	$passed = extension_loaded($checkItem);
	$rtn[] = array('title' => $checkItem . ' extension', 'requirement' => '', 'actual' => '', 'passed' => $passed);
	
	/************ mbstring extension ***********/
	$checkItem = 'mbstring';
	$passed = extension_loaded($checkItem);
	$rtn[] = array('title' => $checkItem . ' extension', 'requirement' => '', 'actual' => '', 'passed' => $passed);
	
	/************ MCrypt extension ***********/
	$checkItem = 'mcrypt';
	$passed = extension_loaded($checkItem);
	$rtn[] = array('title' => $checkItem . ' extension', 'requirement' => '', 'actual' => '', 'passed' => $passed);
	
	/************ cURL extension ***********/
	$checkItem = 'curl';
	$passed = extension_loaded($checkItem);
	$rtn[] = array('title' => $checkItem . ' extension', 'requirement' => '', 'actual' => '', 'passed' => $passed);
	
	return $rtn;
}

function halo_isPassedRequirements($requirements) {
	foreach($requirements as $req) {
		if(!$req['passed']) {
			return false;
		}
	}
	return true;
}

function halo_get_meet_icon($meet){
	if($meet) {
		return "<span class=\"halo-meet-icon halo-yes\">YES</span>";
	} else {
		return "<span class=\"halo-meet-icon halo-no\">NO</span>";
	}
}
function halo_admin_content(){
	$requirements = halo_collectRequirements();
	if(!halo_isPassedRequirements($requirements)) {
	?>
		<div class="wrap">
			<style>  
				.halo-meet-icon {
					border-radius: 10%;
					padding: 2px 10px;
					font-weight: bold;
					color: white;
				}
				.halo-yes {
					background-color: green;
				}
				.halo-no {
					background-color: red;
				}
				.halo-require-failed {
					background-color: antiquewhite;
				}
			</style>
			<h2>System Requirements.</h2>
			<h3>HaloSocial can't be installed on this server. Bellow are the changes you need to make. Visit our <a target="_blank" href="http://docs.halo.social/System_Requirements">document</a>  for more details about system requirements.</h3>
			<div class="">
				<table class="widefat fixed" cellspacing="0">
					<thead>
						<tr>
							<th class="manage-column column-cb check-column" scope="col">Requirement</th>
							<th class="manage-column column-cb check-column" scope="col">Minimum</th>
							<th class="manage-column column-cb check-column" scope="col">Current</th>
							<th class="manage-column column-cb check-column" scope="col">Passed</th>
						</tr>
					</thead>
					<?php foreach($requirements as $require) {?>
						<tr class="alternate <?php if(!$require['passed']) echo 'halo-require-failed';?>">
							<td class="column-columnname"><?php echo $require['title'];?></td>
							<td class="column-columnname"><?php echo $require['requirement'];?></td>
							<td class="column-columnname"><?php echo $require['actual'];?></td>
							<td class="column-columnname"><?php echo halo_get_meet_icon($require['passed'])?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	<?php
	} else {
	?>
		<div class="wrap">
			<h2>HaloSocial Setup</h2>
			<h3>Additional download is required to complete HaloSocial setup. Please make sure you have internet access to continue the setup process</h3>
			<button id="halo_start_btn" onclick="halo_start_download()">Start downloading</button>
			<button id="halo_continue_btn" onclick="halo_continue_setup()" style="display:none">Continue to setup page</button>
			<div class="halo-progress-wrapper">
				<ul class="halo-log">
				</ul>
			</div>
			<script>
				var halo_ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
				var halo_pkgs = <?php echo json_encode(halo_get_pkg_list('notInstalled'));?>;
				
				function halo_start_download() {
					//disable the start button
					jQuery('#halo_start_btn').attr('disabled', 'disabled');
					for(var i in halo_pkgs) {
						var pkg = halo_pkgs[i];
						halo_download_pkg(pkg);
					}
				}
				
				function halo_update_loading(stick, loading){
					setTimeout(function() {
						if(loading.hasClass('stop')) {
							loading.text('');
						} else {
							stick++;
							if(stick >= 10) {
								stick = 1;
							} 
							var text = '';
							for(var i = 0; i < stick; i++) {
								text = text + '.';
							}
							loading.text(text);
						}
						halo_update_loading(stick, loading)
					}, 500)			
				}
				
				function halo_download_pkg(pkg) {
					var log = jQuery('#' + pkg.id);
					if(!log.length) {
						var log = jQuery('<li id="' + pkg.id + '"> Downloading: ' + pkg.title.replace('HALO', 'HaloSocial') 
											+ '<span class="loading" style="margin-left: 10px; width:30px;"></span>'
											+ '<span class="status" style="margin-left: 80px; color:green;"></span>'
											+ '<span class="error" style="margin-left: 120px; color:red;"></span>'
										+'</li>')
						jQuery('.halo-log').append(log);
						//bind loading animation
						var loading = log.find('.loading');
						halo_update_loading(0, loading);
						
					};
					//call ajax request to install the package
					jQuery.ajax({	url: halo_ajax_url,
									data: {
										'action':'halo_setup_ajax',
										'pkg_id' : pkg.id
									},
								})
						.done(function(data) {
							jQuery('#' + pkg.id + ' .loading').addClass('stop');
							if(data == '1'){
								jQuery('#' + pkg.id + ' .status').text('Done');
								jQuery('#' + pkg.id + ' .status').addClass('done');
								jQuery('#' + pkg.id + ' .error').text('');
								//check for download completed
								if(halo_is_complete()){
									jQuery('#halo_start_btn').hide();
									jQuery('#halo_continue_btn').show();
								}
							} else {
								jQuery('#' + pkg.id + ' .error').text(data);
								var countdown = 5;
								halo_download_retry(countdown, pkg);
							}
						})
						.error(function(errorThrown){
							jQuery('#' + pkg.id + ' .loading').addClass('stop');
							halo_download_retry(5, pkg);
							console.log(errorThrown);
						})
						;
				}
				function halo_download_retry(countdown, pkg) {
					setTimeout(function() {
						countdown--;
						if(countdown <= 0) {
							jQuery('#' + pkg.id + ' .status').text('Retrying');
							jQuery('#' + pkg.id + ' .loading').removeClass('stop');
							halo_download_pkg(pkg);
						} else {
							jQuery('#' + pkg.id + ' .status').text('Failed | Retry in ' + countdown + 'second(s)');
							halo_download_retry(countdown, pkg);
						}
					}, 1000)
				}
				
				function halo_is_complete() {
					var status = jQuery('.status.done');
					var pkgCount = 0;
					for(var i in halo_pkgs) {
						pkgCount ++;
					}
					if(status.length == pkgCount) {
						return true;
					} else {
						return false;
					}
				}
				
				function halo_continue_setup() {
					location.reload();
				}
			</script>
		</div>
	<?php
	}
}
