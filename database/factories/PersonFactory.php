<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'first_name' => 'Ken',
			'last_name' => 'Barlow',
			'slug' => 'ken-barlow',
			'created_at' => null,
		];
	}
}
