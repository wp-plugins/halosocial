<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloBanActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_ban_actions')) return;
		//
        Schema::create('halo_ban_actions', function($table)
        {

			$table->bigIncrements('id');
			
			$table->bigInteger('actor_id');
			$table->bigInteger('target_id');
			
            $table->text('action');

			$table->dateTime('expired_at');
			
			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('actor_id');
			$table->index('target_id');
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
        Schema::drop('halo_ban_actions');
	}

}