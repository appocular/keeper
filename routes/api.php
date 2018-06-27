<?php

use Illuminate\Http\Request;

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

// add "auth:" before api to enable auth
// Checkout https://github.com/erjanmx/laravel-api-auth
Route::middleware('api')->post('/image', 'ImageStoreController@create');
Route::middleware('api')->get('/image/{id}', 'ImageStoreController@get');
