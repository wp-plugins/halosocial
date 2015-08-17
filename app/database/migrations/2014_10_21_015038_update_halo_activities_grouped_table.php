<?php

use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class UpdateHaloActivitiesGroupedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('halo_activities', function($table) {
			$table->bigInteger('grouped')->default(0);
		});
	}
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('halo_activities', 'grouped'))
		{
			Schema::table('halo_activities', function($table)
			{
				$table->dropColumn('grouped');
			});
		}
	}
}