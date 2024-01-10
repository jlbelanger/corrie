<?php

use App\Helpers\CorrieHelper;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['throttle:web']], function () {
	Route::get('/', function (Request $request) {
		$people = Person::where('first_name', '!=', '?')
			->orderBy('num_appearances', 'DESC')
			->get()
			->pluck('name', 'id');
		$peopleOutput = [];
		foreach ($people as $value => $label) {
			$peopleOutput[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		$p1 = $request->has('p1') ? (int) $request->get('p1') : 208;
		$p2 = $request->has('p2') ? (int) $request->get('p2') : 291;
		$response = null;
		$title = '';
		if ($request->has('p1') && $request->has('p2')) {
			$response = CorrieHelper::findRelationship($p1, $p2);
			if (!empty($response['path'])) {
				$title = $response['path'][0]['name'] . ' & ' . $response['path'][count($response['path']) - 1]['name'] . ' | ';
			}
		}

		return view('home')
			->with('people', $peopleOutput)
			->with('p1', $p1)
			->with('p2', $p2)
			->with('response', $response)
			->with('title', $title);
	});

	Route::get('/search', function (Request $request) {
		$p1 = (int) $request->get('p1');
		$p2 = (int) $request->get('p2');
		$response = CorrieHelper::findRelationship($p1, $p2);
		return response()->json($response);
	});
});
