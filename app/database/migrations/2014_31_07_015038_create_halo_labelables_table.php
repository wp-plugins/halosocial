<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloLabelablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_labelables')) return;
		//
		Schema::create('halo_labelables', function($table)
		{

			$table->bigIncrements('id');
			
			$table->bigInteger('label_id');
			$table->bigInteger('labelable_id');
			$table->string('labelable_type', 32);
			
			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('label_id');
			$table->index(array('labelable_type', 'labelable_id'));
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
        Schema::drop('halo_labelables');
	}

}