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

Route::get('/', function () {
    return redirect()->route('threads.index');
});


// Route::post('/threads/{thread}/replies', 'ReplyController@store')->name('add_reply');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('threads', 'ThreadController@index')->name('threads.index');
Route::get('threads/create', 'ThreadController@create');
Route::get('threads/search', 'SearchController@show');
Route::get('threads/{channel}', 'ThreadController@index');
Route::get('threads/{channel}/{thread}', 'ThreadController@show')->name('threads.show');
Route::patch('threads/{channel}/{thread}', 'ThreadController@update')->name('threads.update');
Route::post('threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store')->name('threadSubscription.store')->middleware('auth');
Route::delete('threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy')->name('threadSubscription.destroy')->middleware('auth');
Route::delete('threads/{channel}/{thread}', 'ThreadController@destroy')->name('threads.destroy');
Route::post('threads', 'ThreadController@store')->middleware('must-confirm-email')->name('threads');

Route::post('/lock-threads/{thread}', 'LockThreadController@store')->name('lock-threads.store')->middleware('is-admin');
Route::delete('/lock-threads/{thread}', 'LockThreadController@destroy')->name('lock-threads.destroy')->middleware('is-admin');

Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index')->name('get_reply');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store')->name('add_reply');
Route::post('/replies/{reply}/favourites', 'FavouriteController@store')->name('reply.favor');
Route::delete('/replies/{reply}/favourites', 'FavouriteController@destroy')->name('reply.unfavor');
Route::delete('/replies/{reply}', 'ReplyController@destroy')->name('reply.destroy');
Route::patch('/replies/{reply}', 'ReplyController@update')->name('reply.update');
Route::post('/replies/{reply}/best', 'BestReplyController@store')->name('best-reply.store');

Route::get('/profile/{user}', 'ProfileController@show')->name('profile.show');
Route::delete('/profile/{user}/notifications/{notification}', 'UserNotificationController@destroy')->name('user.notifications.destroy');
Route::get('/profile/{user}/notifications', 'UserNotificationController@index')->name('user.notifications.index');
Route::get('/api/users', 'Api\UserController@index');
Route::post('/api/users/{user}/avatar', 'Api\AvatarController@store')->middleware('auth')->name('avatar');

Route::get('/register/confirm', 'Auth\RegisterConfirmController@index')->name('register.confirm');