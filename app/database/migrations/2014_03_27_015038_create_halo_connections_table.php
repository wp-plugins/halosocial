<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloConnectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_connections')) return;
		//
        Schema::create('halo_connections', function($table)
        {

            $table->bigIncrements('id');
            $table->bigInteger('from_id');
            $table->bigInteger('to_id');
            $table->integer('role');
            $table->integer('status')->default(0);
            $table->text('params')->nullable();
			
            $table->timestamps();
			
			$table->index('from_id');
			$table->index('to_id');
			$table->index('role');
			$table->index('status');
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
        Schema::drop('halo_connections');
	}

}