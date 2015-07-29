<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloNotificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('halo_notification')) {
		//
		Schema::create('halo_notification', function($table)
		{
			$table->bigIncrements('id');
            $table->text('actors');
            $table->text('actor_list')->nullable();
            $table->string('action', 80);				//follow permission name rule
            $table->string('context', 32);				//context + target_id to generate the target object
            $table->bigInteger('target_id');
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('action');
			$table->index(array('context', 'target_id'));
		});
		}

		if (!Schema::hasTable('halo_notification_receivers')) {
		//
		Schema::create('halo_notification_receivers', function($table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('notification_id');
            $table->integer('status')->default(0);
			
            $table->text('params')->nullable();
			
			$table->index('user_id');
			$table->index('notification_id');
			$table->index('status');

		});
		}
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('halo_notification');
		if (Schema::hasTable('halo_notification_receivers')) {
			Schema::drop('halo_notification_receivers');
		}
	}

}