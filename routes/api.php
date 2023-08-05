<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*User Routes*/
Route::group(['middleware' => 'api','prefix' => 'user'], function ($router) {
	Route::post('register', ['as' => 'register', 'uses' => 'App\Http\Controllers\API\UserController@register']);
	Route::post('login', ['as' => 'login', 'uses' => 'App\Http\Controllers\API\UserController@login']);
	Route::get('getProfile', ['as' => 'getProfile', 'uses' => 'App\Http\Controllers\API\UserController@get_profile']);
	Route::post('updateProfile', ['as' => 'updateProfile', 'uses' => 'App\Http\Controllers\API\UserController@update_profile']);
});

/*Categories Routes*/
Route::group(['middleware' => 'api','prefix' => 'category'], function ($router) {
	Route::post('add', ['as' => 'add', 'uses' => 'App\Http\Controllers\API\CategoryController@add']);
	Route::put('update/{id}', ['as' => 'update', 'uses' => 'App\Http\Controllers\API\CategoryController@update']);
	Route::delete('delete/{id}', ['as' => 'dalete', 'uses' => 'App\Http\Controllers\API\CategoryController@delete']);
	Route::get('view/{id}', ['as' => 'view', 'uses' => 'App\Http\Controllers\API\CategoryController@view']);
	Route::get('list', ['as' => 'list', 'uses' => 'App\Http\Controllers\API\CategoryController@list']);
});

/*Products Routes*/
Route::group(['middleware' => 'api','prefix' => 'product'], function ($router) {
	Route::post('add', ['as' => 'add', 'uses' => 'App\Http\Controllers\API\ProductController@add']);
	Route::post('update/{id}', ['as' => 'update', 'uses' => 'App\Http\Controllers\API\ProductController@update']);
	Route::delete('delete/{id}', ['as' => 'delete', 'uses' => 'App\Http\Controllers\API\ProductController@delete']);
	Route::get('view/{id}', ['as' => 'view', 'uses' => 'App\Http\Controllers\API\ProductController@view']);
	Route::get('list', ['as' => 'list', 'uses' => 'App\Http\Controllers\API\ProductController@list']);
	Route::put('updateQuantity/{id}', ['as' => 'updateQuantity', 'uses' => 'App\Http\Controllers\API\ProductController@update_quantity']);
});
