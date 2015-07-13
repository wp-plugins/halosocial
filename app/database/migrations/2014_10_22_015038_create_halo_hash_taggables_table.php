<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloHashTaggablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_hash_taggables')) return;
		//
        Schema::create('halo_hash_taggables', function($table)
        {

            $table->bigInteger('tag_id');
			
			//polymorphic relationship
			$table->bigInteger('taggable_id');
			$table->string('taggable_type', 32);
			
			$table->text('params')->nullable();
			
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
        Schema::drop('halo_hash_taggables');
	}

}