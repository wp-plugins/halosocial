<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_fields'))
		{
			return;
		}
		//
		Schema::create('halo_fields', function($table)
		{
			$table->increments('id');
            $table->string('name', 255);
            $table->string('type', 32);
            $table->string('fieldcode', 80)->nullable();

            $table->text('tips')->nullable();
			
            $table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('type');
			$table->index('fieldcode');
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
		Schema::drop('halo_fields');
	}

}