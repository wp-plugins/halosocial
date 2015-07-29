<?php
use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('halo_users'))
		{
			return;
		}
		// Creates the users table
		Schema::create('halo_users', function($table)
		{

			$table->bigInteger('id')->primary('id');

			$table->bigInteger('user_id');
			$table->integer('block')->default(0);
			$table->integer('profile_id')->nullable();
			$table->bigInteger('view_count')->default(0);
			$table->bigInteger('point_count')->default(0);
			$table->bigInteger('like_count')->default(0);
			$table->dateTime('lastVisitDate')->nullable();
			
			$table->bigInteger('avatar_id')->nullable();
			$table->bigInteger('cover_id')->nullable();
			$table->bigInteger('location_id')->nullable();

			$table->smallInteger('user_role')->unsigned()->default(0);
			$table->string('slug',256)->nullable();

			$table->text('params')->nullable();

			$table->timestamps();
			
			$table->index('user_id');
			$table->index('profile_id');
			$table->index('block');
			$table->index('user_role');
		});
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('halo_users');
    }

}
