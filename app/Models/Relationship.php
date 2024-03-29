<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jlbelanger\Tapioca\Traits\Resource;

class Relationship extends Model
{
	use HasFactory, Resource;

	protected $table = 'person_person';

	protected $fillable = [
		'person_1_id',
		'person_2_id',
		'relationship',
		'start_date',
		'end_date',
		'end_reason',
		'take_last_name',
	];

	protected $casts = [
		'take_last_name' => 'boolean',
		'created_at' => 'datetime:Y-m-d',
	];

	/**
	 * @return array
	 */
	public function additionalAttributes() : array
	{
		return ['name', 'date_added'];
	}

	/**
	 * @return string
	 */
	protected function dataType() : string
	{
		return 'relationships';
	}

	/**
	 * @return string
	 */
	protected function getDateAddedAttribute() : string
	{
		return (string) $this->created_at;
	}

	/**
	 * @return string
	 */
	public function getNameAttribute() : string
	{
		return $this->person1()->first()->getNameAttribute() . ' & ' . $this->person2()->first()->getNameAttribute();
	}

	public function getOtherPersonAttribute($id)
	{
		$person1 = $this->person1()->first();
		if ($person1->id != $id) {
			return $person1;
		}
		return $this->person2()->first();
	}

	/**
	 * @return BelongsTo
	 */
	public function person1() : BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_1_id');
	}

	/**
	 * @return BelongsTo
	 */
	public function person2() : BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_2_id');
	}

	/**
	 * @return array
	 */
	public function rules() : array
	{
		return [
			'data.relationships.person_1' => [$this->requiredOnCreate()],
			'data.relationships.person_2' => [$this->requiredOnCreate()],
			'data.attributes.relationship' => [$this->requiredOnCreate(), 'max:255'],
			'data.attributes.start_date' => ['nullable', 'regex:/^\d{4}(-\d{2})?(-\d{2})?$/'],
			'data.attributes.end_date' => ['nullable', 'regex:/^\d{4}(-\d{2})?(-\d{2})?$/'],
			'data.attributes.end_reason' => ['nullable', 'max:255'],
			'data.attributes.take_last_name' => ['boolean'],
		];
	}

	/**
	 * @return array
	 */
	public function singularRelationships() : array
	{
		return ['person_1', 'person_2'];
	}
}
