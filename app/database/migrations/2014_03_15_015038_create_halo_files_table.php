<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_files')) return;
		//
		Schema::create('halo_files', function($table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('folder_id')->default(0);
			$table->text('filename');
			$table->smallInteger('published')->unsigned()->default(0);
			$table->bigInteger('owner_id');
			$table->text('path');						//only need to store the original path, thumb or other resource will be calculated automatically
			$table->bigInteger('hit')->default(0);
			$table->integer('status')->default(0);
			$table->string('storage', 128)->default('file');

			$table->bigInteger('linkable_id')->nullable();
			$table->string('linkable_type', 32)->nullable();
			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('published');
			$table->index('owner_id');
			$table->index('status');
			
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
		Schema::drop('halo_files');
	}

}