<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloConversationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_conversations')) return;
		//
		Schema::create('halo_conversations', function($table)
		{
			$table->bigIncrements('id');
			
            $table->text('attenders')->nullable();
            $table->text('name')->nullable();

            $table->text('params')->nullable();

			$table->timestamps();
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
		Schema::drop('halo_conversations');
	}

}