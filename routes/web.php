<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/','Pages\\HomeController@index');

Auth::routes();

// admin
Route::prefix('admin')->namespace('Admin')->middleware('auth')->group(function()
{
    Route::get('/', 'HomeController@showHome')->name('admin.home');
});

// ajax
Route::prefix('ajax')->namespace('Ajax')->middleware('auth')->group(function()
{
    Route::resource('users','UserController');
    Route::resource('groups','GroupController');
    Route::resource('permissions','PermissionController');
});
