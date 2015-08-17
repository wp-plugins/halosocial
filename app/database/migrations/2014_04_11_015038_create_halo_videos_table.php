<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_videos')) return;
		//
		Schema::create('halo_videos', function($table)
		{

			$table->bigIncrements('id');
			$table->integer('category_id')->default(0);
			$table->text('title');
			$table->text('description')->nullable();
			$table->string('provider');
			$table->smallInteger('published')->unsigned()->default(0);
			$table->bigInteger('owner_id');
			$table->text('path');						//only need to store the original path, thumb or other resource will be calculated automatically
			$table->text('thumbnail');						//only need to store the original path, thumb or other resource will be calculated automatically
			$table->integer('status')->default(0);

			$table->bigInteger('linkable_id')->nullable();
			$table->string('linkable_type', 32)->nullable();

			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('owner_id');
			$table->index('published');
			$table->index('status');
			$table->index('category_id');
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
        Schema::drop('halo_videos');
	}

}