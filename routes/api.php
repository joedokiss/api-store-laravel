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

/**
 * (1) create a store branch
 * (2) update a store branch
 * (3) delete a store branch along with all of its children
 * (4) move a store branch (along with all of its children) to a different store branch
 * (5) view all store branches with all of their children
 * (6) view one specific store branch with all of its children
 * (7) view one specific store branch without any children
 */


/*
(5) view all store branches with all of their children
Route::get('/stores', 'storesController@index')->name('stores.index');

(6) view one specific store branch with all of its children
(7) view one specific store branch without any children
Route::get('/stores/{store}', 'storesController@show')->name('stores.show');

(1) create a store branch
Route::post('/stores', 'storesController@store')->name('stores.store');

(2) update a store branch
(4) move a store branch (along with all of its children) to a different store branch
Route::patch('/stores/{store}', 'storesController@update')->name('stores.update');

(3) delete a store branch along with all of its children
Route::delete('/stores/{store}', 'storesController@destroy')->name('stores.destroy');
 */

Route::group(['middleware' => 'auth:api'], function(){
    Route::resource('stores', 'StoresController', ['except' => 'edit', 'create']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');