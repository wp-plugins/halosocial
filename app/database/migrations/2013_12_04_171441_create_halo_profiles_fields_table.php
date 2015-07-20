<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloProfilesFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_profiles_fields'))
		{
			return;
		}
		//
		Schema::create('halo_profiles_fields', function($table)
		{
			$table->increments('id');
			$table->integer('profile_id')->unsigned();
			$table->integer('field_id')->unsigned();
			$table->integer('ordering')->unsigned()->default(0);
			
			$table->smallInteger('required')->unsigned()->default(0);
			$table->smallInteger('published')->unsigned()->default(0);
		

            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('profile_id');
			$table->index('field_id');
			$table->index('published');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('halo_profiles_fields');
	}

}