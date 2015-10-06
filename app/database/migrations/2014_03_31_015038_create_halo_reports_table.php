<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_reports')) return;
		//
        Schema::create('halo_reports', function($table)
        {

			$table->bigIncrements('id');
			
			$table->bigInteger('actor_id');
			
			$table->bigInteger('owner_id');
			//polymorphic relationship
			$table->bigInteger('reportable_id')->nullable();
			$table->string('reportable_type', 32)->nullable();
			
            $table->text('message')->nullable();
            $table->text('type')->nullable();

			$table->integer('status')->default(0);
			
			$table->timestamps();
			
			$table->index('actor_id');
			$table->index('owner_id');
			$table->index('status');
			$table->index(array('reportable_type', 'reportable_id'));
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
        Schema::drop('halo_reports');
	}

}