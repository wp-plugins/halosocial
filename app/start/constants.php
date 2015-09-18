<?php
/**
 */
if(!app()->runningInConsole()){
define('HALO_USER_TOKEN_HEADER', 'X-Halo-User-Token');
define('HALO_CLIENT_TYPE_HEADER', 'X-Halo-Client-Type');
define('HALO_CLIENT_VERSION_HEADER', 'X-Halo-Client-Version');

define('REDIS_METADATA_LAST_UPDATE_KEY', 'halo_meta_date');

define('HALO_ROOT_PATH', ABSPATH);
define('HALO_APP_PATH', HALO_PLUGIN_PATH . '/app');
define('HALO_ROOT_URL', get_site_url());


// cms user table define
define('HALO_USER_TABLE','users');
define('HALO_USER_ID_COL','ID');
define('HALO_USER_DISPLAY_NAME_COL','display_name');				//user display name
define('HALO_USER_USERNAME_COL','user_login');			//username
define('HALO_USER_EMAIL_COL','user_email');				//email

$uploadDir = wp_upload_dir();

define('HALO_MEDIA_BASE_DIR',$uploadDir['basedir'] . '/halosocial');		//media upload dir
define('HALO_MEDIA_BASE_URL',$uploadDir['baseurl'] . '/halosocial');		//media upload url
/** media status value */
define('HALO_MEDIA_STAT_TEMP',0);
define('HALO_MEDIA_STAT_READY',1);
define('HALO_MEDIA_STAT_LOCK',2);

define('HALO_IMAGE_PLACE_HOLDER_S', HALOAssetHelper::getAssetUrl('assets/images/imageholder_s.gif'));
define('HALO_IMAGE_PLACE_HOLDER_R', HALOAssetHelper::getAssetUrl('assets/images/imageholder_r.gif'));

/** boolean True if a Windows based host */
define('HALO_PATH_ISWIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
/** boolean True if a Mac based host */
define('HALO_PATH_ISMAC', (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC'));

define('HALO_DATE_TIME_FORMAT','Y-m-d H:i T');

/** privacy value */
define('HALO_PRIVACY_PUBLIC',0);
define('HALO_PRIVACY_MEMBER',20);
define('HALO_PRIVACY_FOLLOWER',30);
define('HALO_PRIVACY_ONLYME',40);
define('HALO_PRIVACY_INHERRIT',50);

}