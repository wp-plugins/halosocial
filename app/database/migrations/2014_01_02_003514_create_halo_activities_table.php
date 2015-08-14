<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_activities')) return;
		//
		Schema::create('halo_activities', function($table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('actor_id');
            $table->string('action', 80);				//follow permission name rule
            $table->string('context', 32);				//context + target_id to generate the target object
            $table->bigInteger('target_id');

            $table->text('message')->nullable();
            $table->text('actor_list')->nullable();
            $table->text('followers')->nullable();
            $table->text('tagged_list')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->integer('access')->default(0);
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('actor_id');
			$table->index('location_id');
			$table->index('access');
			$table->index(array('context', 'target_id'));
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
		Schema::drop('halo_activities');
	}

}