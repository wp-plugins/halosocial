<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloReviewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_reviews')) return;
		//
        Schema::create('halo_reviews', function($table)
        {
					$table->bigIncrements('id');
					$table->bigInteger('actor_id');

					$table->text('message');
					$table->integer('rating');
					$table->integer('published')->default(1);
					
					//polymorphic relationship
					$table->bigInteger('reviewable_id')->nullable();
					$table->string('reviewable_type', 32)->nullable();
					
					$table->text('params')->nullable();

					$table->timestamps();
					
					$table->index('published');
					$table->index('actor_id');
					$table->index(array('reviewable_type', 'reviewable_id'));
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
        Schema::drop('halo_reviews');
	}

}