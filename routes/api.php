<?php

use Illuminate\Support\Facades\Route;
use Jlbelanger\Tapioca\Exceptions\NotFoundException;

Route::get('/', function () {
	return response()->json(['success' => true]);
});

Route::group(['middleware' => ['api', 'guest']], function () {
	Route::post('/auth/login', '\App\Http\Controllers\Api\AuthController@login');
	Route::post('/auth/forgot-password', '\App\Http\Controllers\Api\AuthController@forgotPassword');
	Route::put('/auth/reset-password/{token}', '\App\Http\Controllers\Api\AuthController@resetPassword');
});

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::delete('/auth/logout', '\App\Http\Controllers\Api\AuthController@logout');

	Route::apiResources([
		'people' => '\App\Http\Controllers\Api\PersonController',
		'relationships' => '\App\Http\Controllers\Api\RelationshipController',
		'users' => '\App\Http\Controllers\Api\UserController',
	]);
});

Route::fallback(function () {
	throw NotFoundException::generate();
});
