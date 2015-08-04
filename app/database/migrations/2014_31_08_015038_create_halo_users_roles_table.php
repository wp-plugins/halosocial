<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloUsersRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_users_roles')) return;
		//
		Schema::create('halo_users_roles', function($table)
		{

			$table->bigIncrements('id');
			
			$table->bigInteger('user_id');
			$table->bigInteger('role_id');
			$table->text('params')->nullable();

			$table->index('user_id');
			$table->index('role_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('halo_users_roles');
	}

}