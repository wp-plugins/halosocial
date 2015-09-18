<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloAppSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_app_settings'))
		{
			return;
		}
		Schema::create('halo_app_settings', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('platform', 32);
            $table->timestamp('metadata_update_date')->nullable();
            $table->string('minimum_support_version', 16);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('halo_app_settings');
	}

}
