<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;
use Jlbelanger\LaravelJsonApi\Traits\Resource;

class Person extends Model
{
	use HasFactory, Resource;

	protected $fillable = [
		'first_name',
		'last_name',
		'slug',
		'gender',
		'birthdate',
		'deathdate',
		'is_current',
		'num_appearances',
		'appearances_date',
		'filename',
	];

	protected $casts = [
		'created_at' => 'datetime:Y-m-d',
		'is_current' => 'boolean',
		'num_appearances' => 'integer',
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
	protected function getDateAddedAttribute() : string
	{
		return (string) $this->created_at;
	}

	/**
	 * @return string
	 */
	public function getNameAttribute() : string
	{
		// First name.
		$name = $this->first_name;

		// Birth last name.
		$names = [];
		if ($this->last_name !== '?') {
			$names[] = $this->last_name;
		}

		// Adoptive last name.
		$adoptiveParents = $this->getAdoptiveParentsAttribute();
		foreach ($adoptiveParents->get() as $parent) {
			if ($parent->pivot->take_last_name && $parent->last_name !== '?') {
				$names[] = $parent->last_name;
			}
		}

		// Men.
		if ($this->gender === 'M') {
			$names = array_unique($names);
			$name = trim($name . ' ' . implode(' ', $names));
			return $name;
		}

		// Married last names.
		$marriages = $this->getMarriagesAttribute();
		foreach ($marriages->get() as $marriage) {
			$spouse = $marriage->getOtherPersonAttribute($this->id);
			if ($spouse->last_name !== '?') {
				$names[] = $spouse->last_name;
			}
		}

		$names = array_unique($names);
		return trim($name . ' ' . implode(' ', $names));
	}

	/**
	 * @return BelongsToMany
	 */
	public function getAdoptiveParentsAttribute() : BelongsToMany
	{
		return $this->belongsToMany(self::class, 'person_person', 'person_2_id', 'person_1_id')
			->withPivot('take_last_name')
			->where('person_person.relationship', '=', 'adoptive parent')
			->orderBy('people.gender', 'DESC');
	}

	/**
	 * @return BelongsToMany
	 */
	public function getBiologicalParentsAttribute() : BelongsToMany
	{
		return $this->belongsToMany(self::class, 'person_person', 'person_2_id', 'person_1_id')
			->where('person_person.relationship', '=', 'parent')
			->orderBy('person_person.relationship', 'DESC')
			->orderBy('people.gender', 'DESC');
	}

	public function getFullSiblingsAttribute()
	{
		$parents = $this->getBiologicalParentsAttribute();
		if ($parents->count() <= 0) {
			return self::whereNull('id');
		}
		$parents = $parents->get();

		$fullSiblingsIds = [];
		$num = 0;
		foreach ($parents as $parent) {
			$id = Relationship::where('person_1_id', '=', $parent->id)
				->where('relationship', '=', 'parent')
				->whereNotNull('person_2_id')
				->where('person_2_id', '!=', $this->id)
				->pluck('person_2_id')
				->toArray();
			if (!empty($id)) {
				$fullSiblingsIds[] = $id;
				$num++;
			}
		}

		if ($num >= 2) {
			$fullSiblingsIds = array_intersect($fullSiblingsIds[0], $fullSiblingsIds[1]);
		}

		if (!empty($fullSiblingsIds)) {
			return self::whereIn('id', $fullSiblingsIds)
				->orderBy('birthdate', 'ASC');
		}

		return self::whereNull('id');
	}

	public function getMarriagesAttribute()
	{
		$id = $this->id;
		return Relationship::where(function ($query) use ($id) {
			$query->where('person_1_id', '=', $id)
				->orWhere('person_2_id', '=', $id);
		})
		->where('relationship', 'LIKE', '%spouse%')
		->orderBy('start_date', 'ASC');
	}

	public function getPrimaryRelationshipsAttribute()
	{
		$rows = $this->belongsToMany('\App\Models\Person', 'person_person', 'person_1_id', 'person_2_id')
			->withPivot('id')
			->withPivot('relationship')
			->withPivot('start_date')
			->withPivot('end_date')
			->withPivot('end_reason')
			->withPivot('take_last_name')
			->orderBy('start_date', 'ASC')
			->get();

		$output = [];
		foreach ($rows as $row) {
			$output[] = [
				'id' => $row->pivot->id,
				'person_2_id' => $row->id,
				'relationship' => $row->pivot->relationship,
				'start_date' => $row->pivot->start_date,
				'end_date' => $row->pivot->end_date,
				'end_reason' => $row->pivot->end_reason,
				'take_last_name' => $row->pivot->take_last_name,
				'name' => $row->getNameAttribute(),
			];
		}
		return $output;
	}

	public function getSecondaryRelationshipsAttribute()
	{
		$rows = $this->belongsToMany('\App\Models\Person', 'person_person', 'person_2_id', 'person_1_id')
			->withPivot('id')
			->withPivot('relationship')
			->withPivot('start_date')
			->withPivot('end_date')
			->withPivot('end_reason')
			->withPivot('take_last_name')
			->orderBy('start_date', 'ASC')
			->get();

		$output = [];
		foreach ($rows as $row) {
			$output[] = [
				'id' => $row->pivot->id,
				'person_1_id' => $row->id,
				'relationship' => $row->pivot->relationship,
				'start_date' => $row->pivot->start_date,
				'end_date' => $row->pivot->end_date,
				'end_reason' => $row->pivot->end_reason,
				'take_last_name' => $row->pivot->take_last_name,
				'name' => $row->getNameAttribute(),
			];
		}
		return $output;
	}

	/**
	 * @param  array  $data
	 * @param  string $method
	 * @return array
	 */
	protected function rules(array $data, string $method) : array // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
	{
		$rules = [
			'attributes.first_name' => ['filled', 'max:255'],
			'attributes.last_name' => ['filled', 'max:255'],
			'attributes.slug' => ['filled', 'max:255'],
			'attributes.gender' => ['in:M,F'],
			'attributes.birthdate' => ['regex:/^\d{4}(-\d{2})?(-\d{2})?$/'],
			'attributes.deathdate' => ['regex:/^\d{4}(-\d{2})?(-\d{2})?$/'],
			'attributes.is_current' => ['boolean'],
			'attributes.num_appearances' => ['integer'],
			'attributes.appearances_date' => ['regex:/^\d{4}(-\d{2})?(-\d{2})?$/'],
		];

		$unique = Rule::unique($this->getTable(), 'slug');
		if ($this->id) {
			$unique->ignore($this->id);
		}
		$rules['attributes.slug'][] = $unique;

		return $rules;
	}

	/**
	 * @param  string $key
	 * @param  string $filename
	 * @return string
	 */
	public function uploadedFilename(string $key, string $filename) : string // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
	{
		$pathInfo = pathinfo($filename);
		return '/uploads/person/' . $this->slug . '.' . $pathInfo['extension'];
	}
}
