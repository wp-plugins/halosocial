<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_messages')) return;
		//
		Schema::create('halo_messages', function($table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('actor_id');
            $table->text('message')->nullable();
            $table->bigInteger('conv_id');
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('actor_id');
			$table->index('conv_id');
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
		Schema::drop('halo_messages');
	}

}