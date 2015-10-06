<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloOnlineusersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_online_users')) return;
		//
        Schema::create('halo_online_users', function($table)
        {

			$table->bigIncrements('id');
			
			$table->bigInteger('user_id');
			$table->string('ip_addr',39);
			
			$table->string('client_type')->default(0);
			
			$table->smallInteger('status')->unsigned()->default(0);
			$table->string('session');
			
			//client browser
			$table->string('b_userAgent')->nullable();
			$table->string('b_name')->nullable();
			$table->string('b_version')->nullable();
			$table->string('b_platform')->nullable();
			
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
        Schema::drop('halo_online_users');
	}

}