<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloTaggingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_tagging')) return;
		//
        Schema::create('halo_tagging', function($table)
        {

			$table->bigIncrements('id');
            $table->bigInteger('tag_id');
			
			//polymorphic relationship
			$table->bigInteger('tagging_id')->nullable();
			$table->string('tagging_type', 32)->nullable();
			
			$table->text('params')->nullable();
			
			$table->index(array('tagging_type', 'tagging_id'));

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
        Schema::drop('halo_tagging');
	}

}