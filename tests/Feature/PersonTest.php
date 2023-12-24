<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/api/people';

	protected function setUp() : void
	{
		parent::setUp();
		$this->person = Person::factory()->create();
		$this->user = User::factory()->create();
	}

	public function testIndex() : void
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->person->id,
					'type' => 'people',
					'attributes' => [
						'appearances_date' => null,
						'birthdate' => null,
						'date_added' => '',
						'deathdate' => null,
						'filename' => null,
						'first_name' => 'Ken',
						'gender' => null,
						'is_current' => false,
						'last_name' => 'Barlow',
						'name' => 'Ken Barlow',
						'num_appearances' => -1,
						'slug' => 'ken-barlow',
					],
				],
			],
		]);
		$response->assertStatus(200);
	}

	public static function storeProvider() : array
	{
		return [
			[[
				'body' => [
					'data' => [
						'type' => 'people',
						'attributes' => [
							'first_name' => 'Mike',
							'last_name' => 'Baldwin',
							'slug' => 'mike-baldwin',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'people',
						'attributes' => [
							'appearances_date' => null,
							'birthdate' => null,
							'date_added' => '2001-02-03 04:05:06',
							'deathdate' => null,
							'filename' => null,
							'first_name' => 'Mike',
							'gender' => null,
							'is_current' => false,
							'last_name' => 'Baldwin',
							'name' => 'Mike Baldwin',
							'num_appearances' => -1,
							'slug' => 'mike-baldwin',
						],
					],
				],
				'code' => 201,
			]],
		];
	}

	/**
	 * @dataProvider storeProvider
	 */
	public function testStore(array $args) : void
	{
		$this->travelTo(new \Carbon\Carbon('2001-02-03 04:05:06'));
		$response = $this->actingAs($this->user)->json('POST', $this->path, $args['body']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
		$this->travelBack();
	}

	public static function showProvider() : array
	{
		return [
			[[
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'people',
						'attributes' => [
							'appearances_date' => null,
							'birthdate' => null,
							'date_added' => '',
							'deathdate' => null,
							'filename' => null,
							'first_name' => 'Ken',
							'gender' => null,
							'is_current' => false,
							'last_name' => 'Barlow',
							'name' => 'Ken Barlow',
							'num_appearances' => -1,
							'slug' => 'ken-barlow',
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	/**
	 * @dataProvider showProvider
	 */
	public function testShow(array $args) : void
	{
		$args['response'] = $this->replaceToken('%id%', (string) $this->person->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->person->id);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function updateProvider() : array
	{
		return [
			[[
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'people',
						'attributes' => [
							'first_name' => 'Mike',
							'last_name' => 'Baldwin',
							'slug' => 'mike-baldwin',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'people',
						'attributes' => [
							'appearances_date' => null,
							'birthdate' => null,
							'date_added' => '',
							'deathdate' => null,
							'filename' => null,
							'first_name' => 'Mike',
							'gender' => null,
							'is_current' => false,
							'last_name' => 'Baldwin',
							'name' => 'Mike Baldwin',
							'num_appearances' => -1,
							'slug' => 'mike-baldwin',
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate(array $args) : void
	{
		$args['body'] = $this->replaceToken('%id%', (string) $this->person->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', (string) $this->person->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->person->id, $args['body']);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function destroyProvider() : array
	{
		return [
			[[
				'code' => 204,
			]],
		];
	}

	/**
	 * @dataProvider destroyProvider
	 */
	public function testDestroy(array $args) : void
	{
		$response = $this->actingAs($this->user)->json('DELETE', $this->path . '/' . $this->person->id);
		if (!empty($args['response'])) {
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}
}
