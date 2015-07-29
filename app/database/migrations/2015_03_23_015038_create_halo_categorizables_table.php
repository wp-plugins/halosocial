<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloCategorizablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_categorizables')) return;
		//
        Schema::create('halo_categorizables', function($table)
        {

			$table->bigIncrements('id');
            $table->bigInteger('category_id');
			
			//polymorphic relationship
			$table->bigInteger('categorizable_id')->nullable();
			$table->string('categorizable_type', 32)->nullable();
			
			$table->text('params')->nullable();

			$table->index(array('categorizable_type', 'categorizable_id'));
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
        Schema::drop('halo_categorizables');
	}

}