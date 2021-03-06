<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloFiltersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_filters'))
		{
			return;
		}
		//
		Schema::create('halo_filters', function($table)
		{
			$table->bigIncrements('id');
            $table->string('name', 80);
            $table->string('type', 32);

            $table->text('description')->nullable();
            $table->text('on_display_handler')->nullable();
            $table->text('on_apply_handler')->nullable();
			$table->smallInteger('published')->unsigned()->default(0);
			$table->integer('ordering')->default(0);
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('name');
			$table->index('type');
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
		Schema::drop('halo_filters');
	}

}