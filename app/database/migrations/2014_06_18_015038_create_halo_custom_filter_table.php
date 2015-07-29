<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloCustomFilterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_custom_filters')) return;
		//
		Schema::create('halo_custom_filters', function($table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('creator_id');
			$table->bigInteger('filter_id');
			$table->string('name');
			$table->text('filter_str');
			$table->smallInteger('status')->unsigned()->default(0);

			$table->text('params')->nullable();
			$table->timestamps();
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
        Schema::drop('halo_custom_filters');
	}

}