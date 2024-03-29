<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class RelationshipFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() : array
	{
		return [
			'person_1_id' => Person::factory(),
			'person_2_id' => Person::factory(),
			'relationship' => 'parent',
		];
	}
}
