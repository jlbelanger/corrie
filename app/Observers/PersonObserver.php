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
			$oldFilename = $person->filename;
			$newFilename = $person->uploadedFilename('filename', $oldFilename);
			$oldPath = public_path($oldFilename);
			$newPath = public_path($newFilename);
			if (file_exists($oldPath) && !file_exists($newPath)) {
				$folder = preg_replace('/\/[^\/]+$/', '', $newPath);
				if (!is_dir($folder)) {
					mkdir($folder);
				}
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
