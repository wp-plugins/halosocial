<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloMailqueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_mailqueue')) return;
		//
        Schema::create('halo_mailqueue', function($table)
        {

            $table->bigIncrements('id');
            $table->string('to');
            $table->string('subject');
            $table->text('plain_msg');
            $table->text('html_msg');
			$table->smallInteger('status')->unsigned()->default(0);
            $table->string('template');
            $table->string('source_str')->nullabel();
            $table->dateTime('scheduled')->nullable();
            $table->text('params')->nullable();

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
        Schema::drop('halo_mailqueue');
	}

}