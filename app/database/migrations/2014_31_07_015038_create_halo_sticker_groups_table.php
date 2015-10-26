<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloStickerGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_sticker_groups')) return;
		//
		Schema::create('halo_sticker_groups', function($table)
		{

			$table->bigIncrements('id');
			
			$table->string('name');
			$table->string('group_code', 80)->nullable();
			$table->string('group_type', 32);
			
			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('group_code');
			$table->index('group_type');
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
        Schema::drop('halo_sticker_groups');
	}

}