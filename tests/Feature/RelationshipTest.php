<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/api/relationships';

	protected $person1;

	protected $person2;

	protected $relationship;

	protected $user;

	protected function setUp() : void
	{
		parent::setUp();
		$this->person1 = Person::factory()->create(['slug' => 'ken-barlow', 'first_name' => 'Ken', 'last_name' => 'Barlow']);
		$this->person2 = Person::factory()->create(['slug' => 'mike-baldwin', 'first_name' => 'Mike', 'last_name' => 'Baldwin']);
		$this->relationship = Relationship::factory()->create([
			'person_1_id' => $this->person1->id,
			'person_2_id' => $this->person2->id,
			'created_at' => null,
		]);
		$this->user = User::factory()->create();
	}

	public function testIndex() : void
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->relationship->id,
					'type' => 'relationships',
					'attributes' => [
						'date_added' => '',
						'end_date' => null,
						'end_reason' => null,
						'name' => 'Ken Barlow & Mike Baldwin',
						'relationship' => 'parent',
						'start_date' => null,
						'take_last_name' => false,
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
						'type' => 'relationships',
						'attributes' => [
							'end_date' => null,
							'end_reason' => null,
							'relationship' => 'parent',
							'start_date' => null,
							'take_last_name' => false,
						],
						'relationships' => [
							'person_1' => [
								'data' => [
									'id' => '%person_1_id%',
									'type' => 'people',
								],
							],
							'person_2' => [
								'data' => [
									'id' => '%person_2_id%',
									'type' => 'people',
								],
							],
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'relationships',
						'attributes' => [
							'date_added' => '2001-02-03 04:05:06',
							'end_date' => null,
							'end_reason' => null,
							'name' => 'Kevin Webster & Sally Seddon',
							'relationship' => 'parent',
							'start_date' => null,
							'take_last_name' => false,
						],
					],
				],
				'code' => 201,
			]],
		];
	}

	#[DataProvider('storeProvider')]
	public function testStore(array $args) : void
	{
		$this->travelTo(new \Carbon\Carbon('2001-02-03 04:05:06'));
		$person1 = \App\Models\Person::factory()->create(['slug' => 'kevin-webster', 'first_name' => 'Kevin', 'last_name' => 'Webster']);
		$person2 = \App\Models\Person::factory()->create(['slug' => 'sally-seddon', 'first_name' => 'Sally', 'last_name' => 'Seddon']);
		$args['body'] = $this->replaceToken('%person_1_id%', (string) $person1->id, $args['body']);
		$args['body'] = $this->replaceToken('%person_2_id%', (string) $person2->id, $args['body']);
		$args['response'] = $this->replaceToken('%person_1_id%', (string) $person1->id, $args['response']);
		$args['response'] = $this->replaceToken('%person_2_id%', (string) $person2->id, $args['response']);

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
						'type' => 'relationships',
						'attributes' => [
							'date_added' => '',
							'end_date' => null,
							'end_reason' => null,
							'name' => 'Ken Barlow & Mike Baldwin',
							'relationship' => 'parent',
							'start_date' => null,
							'take_last_name' => false,
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	#[DataProvider('showProvider')]
	public function testShow(array $args) : void
	{
		$args['response'] = $this->replaceToken('%id%', (string) $this->relationship->id, $args['response']);
		$args['response'] = $this->replaceToken('%person_1_id%', (string) $this->relationship->person_1_id, $args['response']);
		$args['response'] = $this->replaceToken('%person_2_id%', (string) $this->relationship->person_2_id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->relationship->id);
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
						'type' => 'relationships',
						'attributes' => [
							'end_date' => null,
							'end_reason' => null,
							'relationship' => 'parent',
							'start_date' => null,
							'take_last_name' => false,
						],
						'relationships' => [
							'person_1' => [
								'data' => [
									'id' => '%person_1_id%',
									'type' => 'people',
								],
							],
							'person_2' => [
								'data' => [
									'id' => '%person_2_id%',
									'type' => 'people',
								],
							],
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'relationships',
						'attributes' => [
							'date_added' => '',
							'end_date' => null,
							'end_reason' => null,
							'name' => 'Kevin Webster & Sally Seddon',
							'relationship' => 'parent',
							'start_date' => null,
							'take_last_name' => false,
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	#[DataProvider('updateProvider')]
	public function testUpdate(array $args) : void
	{
		$person1 = \App\Models\Person::factory()->create(['slug' => 'kevin-webster', 'first_name' => 'Kevin', 'last_name' => 'Webster']);
		$person2 = \App\Models\Person::factory()->create(['slug' => 'sally-seddon', 'first_name' => 'Sally', 'last_name' => 'Seddon']);
		$args['body'] = $this->replaceToken('%id%', (string) $this->relationship->id, $args['body']);
		$args['body'] = $this->replaceToken('%person_1_id%', (string) $person1->id, $args['body']);
		$args['body'] = $this->replaceToken('%person_2_id%', (string) $person2->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', (string) $this->relationship->id, $args['response']);
		$args['response'] = $this->replaceToken('%person_1_id%', (string) $person1->id, $args['response']);
		$args['response'] = $this->replaceToken('%person_2_id%', (string) $person2->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->relationship->id, $args['body']);
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

	#[DataProvider('destroyProvider')]
	public function testDestroy(array $args) : void
	{
		$response = $this->actingAs($this->user)->json('DELETE', $this->path . '/' . $this->relationship->id);
		if (!empty($args['response'])) {
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}
}
