<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_labels')) return;
		//
		Schema::create('halo_labels', function($table)
		{

			$table->bigIncrements('id');
			
			$table->string('name');
			$table->string('label_code', 80)->nullable();
			$table->bigInteger('group_id');
			$table->string('label_type', 32);
			
			$table->text('params')->nullable();

			$table->timestamps();

			$table->index('label_code');
			$table->index('label_type');
			$table->index('group_id');
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
        Schema::drop('halo_labels');
	}

}