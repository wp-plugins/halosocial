<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloFollowersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_followers')) return;
		//
		Schema::create('halo_followers', function($table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('follower_id');
			
			//polymorphic relationship
			$table->bigInteger('followable_id')->nullable();
			$table->string('followable_type', 32)->nullable();
			
			//nested table

			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index(array('followable_type', 'followable_id'));
			$table->index('follower_id');
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
		Schema::drop('halo_followers');
	}

}