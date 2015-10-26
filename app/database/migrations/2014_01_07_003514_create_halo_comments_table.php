<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_comments')) return;
		//
		Schema::create('halo_comments', function($table)
		{
			$table->bigIncrements('id');
            $table->bigInteger('actor_id');

            $table->text('message');
            $table->text('tagged_list')->nullable();
            $table->text('actor_list')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->integer('published')->default(1);
			
			//polymorphic relationship
			$table->bigInteger('commentable_id')->nullable();
			$table->string('commentable_type', 32)->nullable();
			
			//nested table
			$table->bigInteger('parent_id')->nullable();
			$table->bigInteger('lft')->nullable();
			$table->bigInteger('rgt')->nullable();
			$table->integer('depth')->nullable();

            $table->text('params')->nullable();

			$table->timestamps();

			$table->index('parent_id');
			$table->index('lft');
			$table->index('rgt');
			$table->index('actor_id');
			$table->index('published');
			$table->index(array('commentable_type', 'commentable_id'));
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
		Schema::drop('halo_comments');
	}

}