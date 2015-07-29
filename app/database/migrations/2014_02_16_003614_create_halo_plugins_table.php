<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloPluginsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_plugins')) return;
		//
		Schema::create('halo_plugins', function($table)
		{
			$table->increments('id');
            $table->text('name');
            $table->text('description');
            $table->text('folder');
            $table->text('element');
            $table->integer('status')->default(0);	//0 = disable, 1 = enabled
            $table->integer('ordering')->default(0);	
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('status');
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
		Schema::drop('halo_plugins');
	}

}