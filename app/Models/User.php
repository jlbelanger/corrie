<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Jlbelanger\Tapioca\Traits\Resource;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, Resource;

	protected $fillable = [
		'username',
		'email',
		'password',
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * @param  boolean $remember
	 * @return array
	 */
	public function getAuthInfo(bool $remember) : array
	{
		return [
			'id' => $this->id,
			'remember' => $remember,
		];
	}

	/**
	 * @param  array  $data
	 * @param  string $method
	 * @return array
	 */
	protected function rules(array $data, string $method) : array // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
	{
		$required = $method === 'POST' ? 'required' : 'filled';
		$rules = [
			'attributes.username' => [$required, 'alpha_num', 'max:255'],
			'attributes.email' => [$required, 'email', 'max:255'],
			'attributes.password' => ['prohibited'],
		];

		$unique = Rule::unique($this->getTable(), 'username');
		if ($this->id) {
			$unique->ignore($this->id);
		}
		$rules['attributes.username'][] = $unique;

		$unique = Rule::unique($this->getTable(), 'email');
		if ($this->id) {
			$unique->ignore($this->id);
		}
		$rules['attributes.email'][] = $unique;

		if ($method === 'POST') {
			$rules['attributes.password'] = [$required, Rules\Password::defaults()];
		}

		return $rules;
	}

	public function setPasswordAttribute($value)
	{
		if ($value !== null && request()->path() === 'api/users' && request()->method() === 'POST') {
			$this->attributes['password'] = Hash::make($value);
		} else {
			$this->attributes['password'] = $value;
		}
	}
}
