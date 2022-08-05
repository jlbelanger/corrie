<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
	/**
	 * Runs the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('people', function (Blueprint $table) {
			$table->id();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('slug')->unique();
			$table->enum('gender', ['M', 'F'])->nullable();
			$table->string('birthdate', 10)->nullable();
			$table->string('deathdate', 10)->nullable();
			$table->boolean('is_current')->default(false);
			$table->integer('num_appearances')->default(-1);
			$table->string('appearances_date', 10)->nullable();
			$table->text('filename')->nullable();
			$table->timestamps();
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/**
	 * Reverses the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('people');
	}
}
