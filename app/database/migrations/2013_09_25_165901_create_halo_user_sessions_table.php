<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloUserSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_user_sessions'))
		{
			return;
		}
		Schema::create('halo_user_sessions', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('token', 64);
            $table->timestamp('valid_until');

			$table->timestamps();
			
			$table->index('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('halo_user_sessions');
	}

}
