<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloUserFieldsValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_user_fields_values')) return;
		//
		Schema::create('halo_user_fields_values', function($table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('field_id')->unsigned();			
            $table->text('value');
			$table->smallInteger('access')->unsigned()->default(0);			
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('user_id');
			$table->index('field_id');
			$table->index('access');
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
		Schema::drop('halo_user_fields_values');
	}

}