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

Route::get('/', 'FrontController@index')->name('beranda');
Route::get('/home', 'FrontController@index')->name('home');
Route::get('/explore', 'FrontController@eventList')->name('event.browse');

Route::group(['prefix' => '/view-event'], function () {
  Route::get('/{event_slug}', 'FrontController@eventView')->name('event.view');
  Route::get('/{event_slug}/donasi', 'FrontController@eventDonation')->name('event.donation');
  Route::post('/{event_slug}/donasi', 'FrontController@eventDonationStore')->name('event.donation.store');  
});

Auth::routes();
Route::group(['prefix' => '/account'], function () {
  Route::get('/dashboard', 'AccountController@dashboard')->name('account.dashboard');
  Route::resource('/event', 'EventController');
  Route::resource('/profile', 'AccountController');
  Route::get('/change-password', 'AccountController@change_password')->name('account.change.password');
  Route::put('/change-password', 'AccountController@update_password')->name('account.change.password');

});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});