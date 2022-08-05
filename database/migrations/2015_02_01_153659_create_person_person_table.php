<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonPersonTable extends Migration
{
	/**
	 * Runs the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('person_person', function (Blueprint $table) {
			$table->id();
			$table->foreignId('person_1_id')->references('id')->on('people')->constrained()->onDelete('restrict');
			$table->foreignId('person_2_id')->references('id')->on('people')->constrained()->onDelete('restrict');
			$table->string('relationship');
			$table->string('start_date', 10)->nullable();
			$table->string('end_date', 10)->nullable();
			$table->string('end_reason')->nullable();
			$table->boolean('take_last_name')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverses the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('person_person');
	}
}
