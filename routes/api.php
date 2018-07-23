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
    Route::post('send_code', 'AuthController@send_code');
    Route::post('login', 'AuthController@login');
    Route::post('reset', 'AuthController@reset_password');
    Route::post('send_reset', 'AuthController@send_reset');
    Route::put('change', 'AuthController@change')->middleware('jwt.auth');
    Route::post('confirm_code', 'AuthController@confirm_code')->middleware('jwt.auth');
    Route::post('confirm_reset', 'AuthController@reset_confirm');
});

Route::group(['prefix' => 'profile'], function() {
    Route::post('create', 'ProfileController@create');
    Route::put('edit','ProfileController@update_profile');
    Route::post('avatar','ProfileController@update_avatar');
    Route::get('show', 'ProfileController@show');
    Route::get('{id}', 'ProfileController@index')->name('profile.index');
    Route::get('{id}/products', 'ProductController@index')->name('profile.products');
});

Route::group(['prefix' => 'maps'], function() {
    Route::post('create', 'LocationController@create');
    Route::put('edit','LocationController@update');
    Route::get('show/{id}', 'LocationController@show');
    Route::get('show', 'LocationController@index');
});

Route::group(['prefix' => 'products'], function() {
    Route::post('create', 'ProductController@create');
    Route::put('edit/{id}','ProductController@update')->name('product.edit');
    Route::delete('delete/{id}', 'ProductController@destroy')->name('product.delete');
    Route::get('show', 'ProductController@show');
    Route::get('show/{id}', 'ProductController@show_by_id');
});

Route::group(['prefix' => 'review'], function() {
    Route::post('{id}/create', 'ReviewController@create')->name('review.create');
    Route::post('{id}/edit','ReviewController@update')->name('review.edit');
    Route::get('{id}/show', 'ReviewController@show')->name('review.show');
    Route::get('show', 'ReviewController@index');
});
});