<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloCommonCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_common_categories')) return;
		//
        Schema::create('halo_common_categories', function($table)
        {

            $table->increments('id');
            $table->string('name', 80);
            $table->text('description')->nullable();
			$table->smallInteger('published')->unsigned()->default(0);
			
			//nested table
			$table->integer('parent_id')->nullable();
			$table->integer('lft')->nullable();
			$table->integer('rgt')->nullable();
			$table->integer('depth')->nullable();

            $table->integer('scope_id')->default(0);
 
            $table->text('params')->nullable();
 
            $table->timestamps();

			$table->index('parent_id');
			$table->index('lft');
			$table->index('rgt');
			$table->index('scope_id');
			$table->index('published');
			$table->index('name');
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
        Schema::drop('halo_common_categories');
    }

}