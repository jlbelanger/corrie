<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
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
	 * @param  array  $data
	 * @param  string $method
	 * @return array
	 */
	protected function rules(array $data, string $method) : array // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
	{
		$rules = [
			'attributes.email' => ['filled', 'email', 'max:255'],
			'attributes.username' => ['filled', 'alpha_num', 'max:255'],
		];

		$unique = Rule::unique($this->getTable(), 'email');
		if ($this->id) {
			$unique->ignore($this->id);
		}
		$rules['attributes.email'][] = $unique;

		$unique = Rule::unique($this->getTable(), 'username');
		if ($this->id) {
			$unique->ignore($this->id);
		}
		$rules['attributes.username'][] = $unique;

		return $rules;
	}

	public function setPasswordAttribute($value)
	{
		if ($value !== null) {
			$this->attributes['password'] = bcrypt($value);
		}
	}
}
