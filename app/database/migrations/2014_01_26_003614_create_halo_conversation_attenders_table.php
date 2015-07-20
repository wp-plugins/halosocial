<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloConversationAttendersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_conversation_attenders')) return;
		//
		Schema::create('halo_conversation_attenders', function($table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('conv_id')->nullable();
            $table->bigInteger('attender_id')->nullable();
            $table->integer('display')->default(0);
			
            $table->bigInteger('lastseen_id')->default(0);
            $table->bigInteger('latest_id')->default(0);
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('conv_id');
			$table->index('attender_id');
			$table->index('display');
			$table->index('lastseen_id');
			$table->index('latest_id');
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
		Schema::drop('halo_conversation_attenders');
	}

}