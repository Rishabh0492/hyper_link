<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', function () {
  return view('welcome');
})->middleware('auth');
Auth::routes();


Route::group(['middleware' => ['web']], function ()
{
	Auth::routes(['verify'=>true]);
	Route::get('/check-email-exsist', 'UserController@emailExsist');
	Route::get('confirm_email', 'UserController@confirmEmail');
	Route::get('/check-number-exsist', 'UserController@mobilenumberExsist');

	Route::get('/', 'HomeController@index');
	Route::get('/home', function () { return redirect('/');});

	Route::group(['middleware' => ['auth']], function()
	{
		Route::get('/home','HomeController@index');

		Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function() {

			// Dashboard
			Route::get('/dashboard', 'DashboardController')->name('adminDashboard'); //Dashboard page
			Route::post('/dashboardFilterData', 'DashboardController@dashboardFilterData'); //Dashboard page

			// admin profile Routing
			Route::resource('/profile','ProfileController');


			// User Routing
			Route::resource('/users','UserController');
			Route::post('/users/status-change', 'UserController@changeStatus');
	
			//Roles Routing
			// Route::resource('/roles','RoleController');
			Route::resource('roles','Admin\RoleController');
			Route::post('/permission/getPermissions', 'Admin\RoleController@getPermissions');

			//Role Users Routing
			// Route::resource('/roleuser','RoleUserController');
			Route::resource('/roleuser','Admin\RoleUserController');

			Route::get('/booking','Admin\BookingController@index');
			Route::get('/booking/create','Admin\BookingController@create')->name('create-booking');
			Route::post('/booking/store','Admin\BookingController@store')->name('booking.store');
			Route::get('/booking/getfair/{id}','Admin\BookingController@getFair');


		});

	});
});
