<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/api/users';

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

	public function storeProvider() : array
	{
		return [
			[[
				'body' => [
					'data' => [
						'type' => 'users',
						'attributes' => [
							'email' => 'bar@example.com',
							'username' => 'bar',
							'password' => 'example',
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

	/**
	 * @dataProvider storeProvider
	 */
	public function testStore(array $args) : void
	{
		$response = $this->actingAs($this->user)->json('POST', $this->path, $args['body']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public function showProvider() : array
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

	/**
	 * @dataProvider showProvider
	 */
	public function testShow(array $args) : void
	{
		$args['response'] = $this->replaceToken('%id%', $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->user->id);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public function updateProvider() : array
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

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate(array $args) : void
	{
		$args['body'] = $this->replaceToken('%id%', $this->user->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->user->id, $args['body']);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public function destroyProvider() : array
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
	public function testDestroy($args)
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
