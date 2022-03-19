<?php

namespace App\Observers;

use App\Models\Person;

class PersonObserver
{
	/**
	 * @param  Person $person
	 * @return void
	 */
	public function updated(Person $person)
	{
		if (!$person->isDirty('filename')) {
			return;
		}

		$filename = $person->getOriginal('filename');
		if (!$filename) {
			return;
		}

		$path = public_path($filename);
		if (file_exists($path)) {
			unlink($path);
		}
	}

	/**
	 * @param  Person $person
	 * @return void
	 */
	public function deleted(Person $person)
	{
		if (!$person->filename) {
			return;
		}

		$path = public_path($person->filename);
		if (file_exists($path)) {
			unlink($path);
		}
	}
}
