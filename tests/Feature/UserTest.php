<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/api/users';

	protected $user;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
	}

	public function testIndex() : void
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->user->id,
					'type' => 'users',
					'attributes' => [
						'email' => 'foo@example.com',
						'username' => 'foo',
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
						'type' => 'users',
						'attributes' => [
							'email' => 'bar@example.com',
							'username' => 'bar',
							'password' => '12345678',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'email' => 'bar@example.com',
							'username' => 'bar',
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
		$response = $this->actingAs($this->user)->json('POST', $this->path, $args['body']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function showProvider() : array
	{
		return [
			[[
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'email' => 'foo@example.com',
							'username' => 'foo',
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
		$args['response'] = $this->replaceToken('%id%', (string) $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->user->id);
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
						'type' => 'users',
						'attributes' => [
							'email' => 'bar@example.com',
							'username' => 'bar',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'email' => 'bar@example.com',
							'username' => 'bar',
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
		$args['body'] = $this->replaceToken('%id%', (string) $this->user->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', (string) $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->user->id, $args['body']);
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
		$response = $this->actingAs($this->user)->json('DELETE', $this->path . '/' . $this->user->id);
		if (!empty($args['response'])) {
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}
}
