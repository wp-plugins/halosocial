<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloPhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_photos')) return;
		//
		Schema::create('halo_photos', function($table)
		{

			$table->bigIncrements('id');
			$table->bigInteger('album_id')->default(0);
			$table->text('caption');
			$table->smallInteger('published')->unsigned()->default(0);
			$table->bigInteger('owner_id');
			$table->text('path');						//only need to store the original path, thumb or other resource will be calculated automatically
			$table->bigInteger('hit')->default(0);
			$table->integer('status')->default(0);
			$table->string('storage', 128)->default('file');
			$table->bigInteger('location_id')->nullable();

			$table->bigInteger('linkable_id')->nullable();
			$table->string('linkable_type', 32)->nullable();

			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('album_id');
			$table->index('published');
			$table->index('owner_id');
			$table->index('status');
			$table->index('location_id');
			$table->index(array('linkable_type', 'linkable_id'));
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
        Schema::drop('halo_photos');
	}

}