<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Jlbelanger\Tapioca\Traits\Resource;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, Resource;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'username',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
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
			'id' => $this->getKey(),
			'remember' => $remember,
		];
	}

	/**
	 * @return array
	 */
	public function rules() : array
	{
		$rules = [
			'data.attributes.username' => [$this->requiredOnCreate(), 'alpha_num', 'max:255', $this->unique('username')],
			'data.attributes.email' => [$this->requiredOnCreate(), 'email', 'max:255', $this->unique('email')],
		];

		if ($this->getKey()) {
			$rules['data.attributes.password'] = ['prohibited'];
		} else {
			$rules['data.attributes.password'] = [$this->requiredOnCreate(), Rules\Password::defaults()];
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
