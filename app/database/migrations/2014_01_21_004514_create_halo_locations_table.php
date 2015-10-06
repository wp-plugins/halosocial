<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_locations')) return;
		//
		Schema::create('halo_locations', function($table)
		{
			$table->bigIncrements('id');
			$table->string('name', 255);
			
			//$table->Point('location');
			$table->float('lng');
			$table->float('lat');
			
			$table->bigInteger('hit')->default(0);
			
			$table->bigInteger('district_id')->nullable();
			$table->text('district_name')->nullable();

			$table->bigInteger('city_id')->nullable();
			$table->text('city_name')->nullable();

			$table->timestamps();			
			$table->text('params')->nullable();
			
			$table->index('district_id');
			$table->index('city_id');

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
		Schema::drop('halo_locations');
	}

}