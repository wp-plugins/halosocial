<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloNotificationRecieversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
		if (Schema::hasTable('halo_notification_receivers')) {
			Schema::drop('halo_notification_receivers');
		}
	}

}