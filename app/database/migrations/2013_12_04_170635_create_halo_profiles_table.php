<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_profiles'))
		{
			return;
		}
		//
		Schema::create('halo_profiles', function($table)
		{
			$table->increments('id');
			$table->string('name', 255);
			$table->string('type', 32);												//available type: post,user,category,event,group
			$table->smallInteger('published')->unsigned()->default(0);
			$table->text('description')->nullable();
			$table->smallInteger('default')->unsigned()->default(0);

			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('type');
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
		Schema::drop('halo_profiles');
	}

}