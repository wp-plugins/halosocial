<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloCityDistrictTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('halo_cities')){ 
		//
		Schema::create('halo_cities', function($table)
		{
			$table->bigIncrements('id');
			$table->string('name');
			$table->text('params')->nullable();
		});
		}
		if (!Schema::hasTable('halo_districts')){ 
		Schema::create('halo_districts', function($table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('city_id');
			$table->string('name');
			$table->text('params')->nullable();
			
			$table->index('city_id');
		});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        Schema::drop('halo_cities');
        Schema::drop('halo_districts');
	}

}