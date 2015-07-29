<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_tags')) return;
		//
        Schema::create('halo_tags', function($table)
        {

			$table->bigIncrements('id');
			
			//polymorphic relationship
			$table->bigInteger('taggable_id')->nullable();
			$table->string('taggable_type', 32)->nullable();
			
			//nested table

            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index(array('taggable_type', 'taggable_id'));
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
        Schema::drop('halo_tags');
	}

}