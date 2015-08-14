<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloUserOauthTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_user_oauth')) return;
		//
        Schema::create('halo_user_oauth', function($table)
        {

            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->integer('consumer_id');
            $table->text('uid');
            $table->text('params')->nullable();
			
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
		//
        Schema::drop('halo_user_oauth');
	}

}