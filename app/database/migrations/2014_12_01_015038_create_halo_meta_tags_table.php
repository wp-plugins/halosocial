<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloMetaTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_meta_tags')) return;
		//
        Schema::create('halo_meta_tags', function($table)
        {

			$table->bigIncrements('id');
			
			$table->text('url');
			$table->integer('published')->default(0);
			
            $table->bigInteger('creator_id');

			$table->text('params')->nullable();

			$table->timestamps();
			
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
        Schema::drop('halo_meta_tags');
	}

}