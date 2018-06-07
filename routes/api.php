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
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::group(['prefix' => 'v1'], function() {

Route::group(['prefix' => 'account'], function() {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('reset', 'AuthController@reset_password');
    Route::put('change', 'AuthController@change')->middleware('jwt.auth');
    Route::post('confirm_code', 'AuthController@confirm_code')->middleware('jwt.auth');
    Route::post('confirm_reset', 'AuthController@reset_confirm');
});
Route::group(['prefix' => 'profile'], function() {
    Route::post('create', 'ProfileController@create');
    Route::put('edit','ProfileController@update_profile');
    Route::post('avatar','ProfileController@update_avatar');
    Route::get('show', 'ProfileController@show');
    Route::get('index', 'ProfileController@show')
});
});