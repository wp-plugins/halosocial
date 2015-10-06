<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class DatabaseSeeder extends Seeder {

    public function run()
    {
        Eloquent::unguard();
         try{

			// Add calls to Seeders here
			//$this->call('UsersTableSeeder');
			//$this->call('RolesTableSeeder');
			//$this->call('PermissionsTableSeeder');

			//$this->call('CitiesTableSeeder');
			//$this->call('AppSettingsTableSeeder');

			$this->call('ProfilesTableSeeder');
			$this->call('FieldsTableSeeder');
			$this->call('FiltersTableSeeder');
			$this->call('ProfilesFieldsTableSeeder');

			$this->call('CommonCategoriesTableSeeder');

			$this->call('AuthSeeder');

			$this->call('ConfigSeeder');

			//$this->call('PostsTableSeeder');
			$this->call('CommentsTableSeeder');
		} catch ( \Exception $e){
			HALOResponse::addMessage(HALOError::failed($e->getMessage()));
		}
    }

}