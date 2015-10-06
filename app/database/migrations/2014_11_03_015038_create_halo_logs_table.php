<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_logs')) return;
		//
        Schema::create('halo_logs', function($table)
        {

			$table->bigIncrements('id');
			
			$table->bigInteger('user_id');
			$table->bigInteger('action_id');

			//polymorphic relationship 0
			$table->bigInteger('arg0_id')->nullable();
			$table->string('arg0_type')->nullable();			
			//polymorphic relationship 1
			$table->bigInteger('arg1_id')->nullable();
			$table->string('arg1_type')->nullable();			
			//polymorphic relationship 2
			$table->bigInteger('arg2_id')->nullable();
			$table->string('arg2_type')->nullable();			
			
			$table->text('content');
			$table->text('params')->nullable();

			$table->timestamps();
        });

        Schema::create('halo_log_actions', function($table)
        {

			$table->bigIncrements('id');
					
			$table->text('action_name');

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
        Schema::drop('halo_logs');
        Schema::drop('halo_log_actions');
	}

}