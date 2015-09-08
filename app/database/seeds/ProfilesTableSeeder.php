<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class ProfilesTableSeeder extends Seeder {

    public function run()
    {
		//insert profile
		$profiles = $this->getProfileSettings();
		foreach($profiles as $profile) {
			$data = $profile;
			if(isset($data['fields'])) unset($data['fields']);
			$isNew = HALOUtilHelper::insertNewIfNotExists('halo_profiles', $data, array('type', 'name'));
			if(!$isNew) return;	//seeder already run, skip it
		}
		//insert fields
		$fields = $this->getFieldSettings();
		foreach($fields as $field) {
			HALOUtilHelper::insertNewIfNotExists('halo_fields', $field, array('name', 'type', 'fieldcode'));
		}
		
		//map field to profile
		foreach($profiles as $profile) {
			HALOProfileModel::addFieldsToProfileArray($profile);
		}

    }

	public function getProfileSettings() {
		$profiles = array(
            array(
                'type'          => 'user',
                'published'     => 1,
                'default'		=> 1,
                'name'          => 'Default User Profile',
                'description'   => '',
                'params'        => '',

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
				
				'fields' 	=> array(
					array(
						'fieldcode'			=> 'FIELD_CORE_HALOUSER_SEX',
						'required'          => 0,
						'published'         => 1,
					),	
					array(
						'fieldcode'			=> 'FIELD_CORE_HALOUSER_BIRTHDAY',
						'required'          => 0,
						'published'         => 1,
					),
				)
            ),  
        );
		return $profiles;
	}
	
	public function getFieldSettings() {
		$fields = array(
            array(
                'name'          => 'Sex',
                'type'          => 'gender',
                'fieldcode'     => 'FIELD_CORE_HALOUSER_SEX',
                'params'        => '{"hint":"Select your sex","options":"[[\"title\"],[\"Male\"],[\"Female\"]]"}',

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ), 
            array(
                'name'          => 'Birthday',
                'type'          => 'date', 
                'fieldcode'     => 'FIELD_CORE_HALOUSER_BIRTHDAY',
                'params'        => '{"format":"d-m-Y","validate_rule":"","default":""}',

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ), 
		);
		return $fields;
	}
}
