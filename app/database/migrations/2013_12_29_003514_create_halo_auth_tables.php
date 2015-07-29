<?php
use Illuminate\Database\Migrations\Migration;
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database migration
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CreateHaloAuthTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

		if (!Schema::hasTable('halo_roles')){ 
        // Creates the roles table
        Schema::create('halo_roles', function($table)
        {
            $table->increments('id');
            $table->string('name', 80);
            $table->string('type', 32);								// system, context, dynamic, group
            $table->text('description')->nullable();
            $table->text('params')->nullable();
			
			$table->index('name');
			$table->index('type');
        });

		}
		if (!Schema::hasTable('halo_permissions')){ 
        // Creates the permissions table
        Schema::create('halo_permissions', function($table)
        {
            $table->increments('id');
            $table->string('name', 80);					//action in domain format: <asset>.<action>
            $table->text('description');
			
			$table->index('name');
			
        });
		}
		if (!Schema::hasTable('halo_permission_role')){ 
        // Creates the permission_role (Many-to-Many relation) table
        Schema::create('halo_permission_role', function($table)
        {
            $table->increments('id');
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('ordering')->unsigned()->default(0);
			
			$table->index('role_id');
			$table->index('permission_id');
        });
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		if (Schema::hasTable('halo_permission_role')){ 
        Schema::drop('halo_permission_role');
		}
		if (Schema::hasTable('halo_roles')){ 
        Schema::drop('halo_roles');
		}
		if (Schema::hasTable('halo_permissions')){ 
        Schema::drop('halo_permissions');
		}
    }

}
