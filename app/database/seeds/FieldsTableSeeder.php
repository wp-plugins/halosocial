<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class FieldsTableSeeder extends Seeder {

    public function run()
    {
        $fields = array(
        	array(
        		// id is 1
            	'name'      	=> 'Title',
            	'type' 	        => 'text', 
                'fieldcode'     => 'FIELD_CORE_HALO_1', 
                'params'        => NULL,   
                             
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
        );

        foreach($fields as $field) {
            HALOUtilHelper::insertNewIfNotExists('halo_fields', $field, array('name', 'type', 'fieldcode'));
        }
    }

}
