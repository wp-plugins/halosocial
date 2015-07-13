<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloStickablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_stickables')) return;
		//
		Schema::create('halo_stickables', function($table)
		{

			$table->bigIncrements('id');
			
			$table->bigInteger('sticker_id');
			$table->bigInteger('stickable_id');
			$table->string('stickable_type', 32);
			
			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('sticker_id');
			$table->index(array('stickable_type', 'stickable_id'));
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
        Schema::drop('halo_stickables');
	}

}