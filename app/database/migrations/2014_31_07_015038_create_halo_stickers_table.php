<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloStickersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_stickers')) return;
		//
		Schema::create('halo_stickers', function($table)
		{

			$table->bigIncrements('id');
			
			$table->string('name');
			$table->string('sticker_code', 80)->nullable();
			$table->bigInteger('group_id');
			$table->string('sticker_type', 32);
			
			$table->text('params')->nullable();

			$table->timestamps();

			$table->index('sticker_code');
			$table->index('sticker_type');
			$table->index('group_id');
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
        Schema::drop('halo_stickers');
	}

}