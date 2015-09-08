<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloViewcountersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_view_counters')) return;
		//
        Schema::create('halo_view_counters', function($table)
        {

			$table->bigIncrements('id');
			
			//polymorphic relationship
			$table->bigInteger('viewable_id');
			$table->string('viewable_type', 32);
			
			$table->integer('counter')->default(0);
			
			$table->index(array('viewable_type', 'viewable_id'));
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
        Schema::drop('halo_view_counters');
	}

}