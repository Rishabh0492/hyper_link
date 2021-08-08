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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'localization'], function(){

  Route::post('/register', 'API\UserController@register');
  Route::post('/login', 'API\UserController@login');
  //Route::post('/logout', 'API\UserController@logout');
  Route::post('/forgotPassword', 'API\UserController@forgotPassword');
  Route::post('/socialRegister', 'API\UserController@socialRegister');
  //Route::post('/changePassword', 'API\UserController@changePassword');

	Route::group(['middleware' => 'auth:api'], function(){
    Route::post('/logout', 'API\UserController@logout');
    Route::post('/updateDeviceToken', 'API\UserController@updateDeviceToken');
    Route::post('/changePassword', 'API\UserController@changePassword');
    Route::post('/updateProfile','API\UserController@updateProfile');
    
    //Chat
    Route::get('/getUserChatList', 'ChatController@getUserChatList'); // get user chat list
    Route::post('/saveLastMessage', 'ChatController@saveLastMessage'); // save last chat message
    Route::post('/markReadMessage', 'ChatController@markReadMessage'); // mark as read chat message
    Route::post('/getChatID', 'ChatController@getChatID');
    Route::post('/updateMessageMysql','Common\ChatController@updateMessageMysql');
    Route::post('/updateMedia','ChatController@updateMedia');
  });
});