<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class ConfigSeeder extends Seeder {

	public function run()
	{
		//global config
		HALOConfig::set('template.name','default');
		HALOConfig::set('global.defaultLimit',20);
		HALOConfig::set('global.commentDisplayLimit',4);
		HALOConfig::set('global.updateInterval',30000);
		HALOConfig::set('global.enableLike',1);
		HALOConfig::set('global.enableDisLike',0);
		HALOConfig::set('global.activityQueryLimit',12);
		HALOConfig::set('global.activityDefaultPrivacy',0);
		HALOConfig::set('global.activityShowPrivacy',1);
		HALOConfig::set('global.commentDisplayInput',1);
		HALOConfig::set('global.enableSecure',0);
		HALOConfig::set('global.enableDebug',0);
		HALOConfig::set('global.enableTemplateDebug',0);
		
		//user config
		HALOConfig::set('user.defaultProfile', 1);
		HALOConfig::set('user.defaultDisplayLimit',10);
		HALOConfig::set('user.suggestFriendsOnly',1);
		
		//push server
		// HALOConfig::set('pushserver.address','push.halo.social:8100');
		
		//message config
		HALOConfig::set('conv.showrecentdays',4);
		HALOConfig::set('message.defaultDisplayLimit',10);
		
		//photo config
		HALOConfig::set('photo.allowedExtensions','png,jpg,jpeg');
		HALOConfig::set('photo.avatarSize',80);
		HALOConfig::set('photo.thumbSize',60);
		HALOConfig::set('photo.smallSize',60);
		HALOConfig::set('photo.listSize',40);
		HALOConfig::set('photo.coverSize',1024);
		HALOConfig::set('photo.coverSmall',320);
		HALOConfig::set('photo.gallerySize',320);
		
		//file config
		HALOConfig::set('file.allowedExtensions','pdf,txt,zip,rar');

		//userpoint config
		HALOConfig::set('userpoint.level.1','50');
		HALOConfig::set('userpoint.level.2','200');
		HALOConfig::set('userpoint.level.3','500');
		HALOConfig::set('userpoint.level.4','1000');
		HALOConfig::set('userpoint.level.5','2000');
		
		//google app
		HALOConfig::set('global.googleAPIClientId','652132003717-39fjglde4tti4akd7cjtq3ea0euvaqbe.apps.googleusercontent.com');
		HALOConfig::set('global.googleAPIKey','AIzaSyCgqhjM_8HOWGAkka3cGZYXmsV3c_VAkDE');

		HALOConfig::store();
		
		//label seeding

		$this->labelSeeder();
    }
	
	public function labelSeeder() {
		$allowedRoles = array('Admin', 'Mod');
		$my = HALOUserModel::getUser();

		//status group label
		$groupLabel = array('name' => 'System Labels', 'group_code' => 'HALO_SYSTEM_LABELS',	'group_type' => 0);
		$groupLabelModel = HALOLabelGroupModel::firstOrCreate($groupLabel);
		
		//featured label
		$label = array('name' => 'FEATURED', 'label_code' => 'HALO_SYSTEM_LABEL_FEATURED',
						'group_id' => $groupLabelModel->id, 'label_type' => 'manual');
		$labelModel = HALOLabelModel::firstOrCreate($label);
		if(!$labelModel->params) {		//new create label
			$labelModel->params = json_encode(array('style' => 'danger', 'allowedRoles'=> $allowedRoles));
			$labelModel->save();
		}
		
		//popular label
		$label = array('name' => 'POPULAR', 'label_code' => 'HALO_SYSTEM_LABEL_POPULAR',
						'group_id' => $groupLabelModel->id, 'label_type' => 'manual');
		$labelModel = HALOLabelModel::firstOrCreate($label);
		if(!$labelModel->params) {		//new create label
			$labelModel->params = json_encode(array('style' => 'success', 'allowedRoles'=> $allowedRoles));
			$labelModel->save();
		}
		
		//new label
		$label = array('name' => 'NEW', 'label_code' => 'HALO_SYSTEM_LABEL_NEW',
						'group_id' => $groupLabelModel->id, 'label_type' => 'timer');
		$labelModel = HALOLabelModel::firstOrCreate($label);
		if(!$labelModel->params) {		//new create label
			$labelModel->params = json_encode(array('style' => 'primary', 'lifetime'=> 12, 'allowedRoles'=> $allowedRoles));
			$labelModel->save();
		}
		HALOConfig::set('user.label.status', 'HALO_SYSTEM_LABELS');
		HALOConfig::set('user.label.new', 'HALO_SYSTEM_LABEL_NEW');	
		HALOConfig::store();
	}

}
