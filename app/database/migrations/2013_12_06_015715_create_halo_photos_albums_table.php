<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloPhotosAlbumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_photos_albums')) return;
		//
        Schema::create('halo_photos_albums', function($table)
        {

            $table->bigIncrements('id');
            $table->text('name');
            $table->text('description')->nullable();
			$table->smallInteger('published')->unsigned()->default(0);
            $table->bigInteger('owner_id');
            $table->bigInteger('cover_id')->nullable();
            $table->text('params')->nullable();
			
            $table->timestamps();
			
			$table->index('published');
			$table->index('owner_id');
			$table->index('cover_id');
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
        Schema::drop('halo_photos_albums');
	}

}