<?php

use Illuminate\Database\Migrations\Migration;

class ConfideSetupUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Creates the users table
		Schema::create('users', function($table)
		{
		    $table->increments('id');
		    $table->string('username');
		    $table->string('email');
		    $table->string('password');
		    $table->boolean('confirmed');
		    $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
