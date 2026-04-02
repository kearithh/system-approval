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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api'], 'prefix' => '/approval-api'], function() {
    Route::post('/get-mission', 'ApiController@mission');
    Route::post('/get-all-user', 'ApiController@allUser');
    Route::post('/get-user-by-id', 'ApiController@userById');
});
