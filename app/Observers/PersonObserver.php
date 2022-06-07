<?php

namespace App\Observers;

use App\Models\Person;

class PersonObserver
{
	/**
	 * @param  Person $person
	 * @return void
	 */
	public function updating(Person $person)
	{
		if (!$person->isDirty('slug')) {
			return;
		}

		// Rename files to match new slug.
		if ($person->filename) {
			$newFilename = $person->uploadedFilename('filename', $person->filename);
			$oldFilename = preg_replace('/\/' . $person->slug . '\./', '/' . $person->getOriginal('slug') . '.', $newFilename);
			$oldPath = public_path($oldFilename);
			$newPath = public_path($newFilename);
			if (file_exists($oldPath) && !file_exists($newPath)) {
				rename($oldPath, $newPath);
				$person->filename = $newFilename;
			}
		}
	}

	/**
	 * @param  Person $person
	 * @return void
	 */
	public function updated(Person $person)
	{
		// When uploading or removing file, delete the old file.
		if ($person->isDirty('filename')) {
			$filename = $person->getOriginal('filename');
			if ($filename) {
				$path = public_path($filename);
				if (file_exists($path)) {
					unlink($path);
				}
			}
		}
	}

	/**
	 * @param  Person $person
	 * @return void
	 */
	public function deleted(Person $person)
	{
		// Delete associated files.
		if ($person->filename) {
			$path = public_path($person->filename);
			if (file_exists($path)) {
				unlink($path);
			}
		}

		// Rename slug to allow new rows to be created with same slug.
		$person->slug = 'deleted-' . strtotime('now') . '-' . $person->slug;
		$person->filename = null;
		$person->save();
	}
}
