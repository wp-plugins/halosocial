<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloLikesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_likes')) return;
		//
		Schema::create('halo_likes', function($table)
		{
			$table->bigIncrements('id');

            $table->text('like')->nullable();
            $table->text('dislike')->nullable();
			
			//polymorphic relationship
			$table->bigInteger('likeable_id')->nullable();
			$table->string('likeable_type', 32)->nullable();			

            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index(array('likeable_type', 'likeable_id'));
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
		Schema::drop('halo_likes');
	}

}