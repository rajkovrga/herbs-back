<?php

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

Route::middleware([])->group(function () {
    Route::get('/images/{path}', 'UserController@image'); // !!!!+
    Route::post('/contact','UserController@contactAdmin');
    Route::get('/herbs/filter/page/{page}', 'HerbsController@filter');
    Route::get('/herbs/comments/{id}/page/{page}', 'HerbsController@comments');
    Route::get('/periods', 'PeriodController@index');
    Route::get('/periods/{id}', 'PeriodController@show');
    Route::get('/pickpart', 'PickPartController@index');
    Route::get('/pickpart/{id}', 'PickPartController@show');
    Route::get('/herbs/page/{page}', 'HerbsController@index');
    Route::get('/herbs/{id}', 'HerbsController@show');
    Route::post('/login', 'AuthController@login');
    Route::post('/registration', 'AuthController@register');
    Route::post('/forgot', 'AuthController@forgotPassword');
    Route::get('/verify', 'AuthController@verify')->name('verify.email');
    Route::post('/verify/again', 'AuthController@verifyAgain');
    Route::get('/resetPassword', 'AuthController@resetPassword')->name('reset.password');
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/permissions/{id}','UserController@getPermissionsForRole');
    Route::get('/roles','UserController@getRoles');
    Route::get('/permissions','UserController@getPermissions');
    Route::put('/change/role','UserController@changeRoleToUser');
    Route::post('/add/permissions','UserController@addPermissionsForRole');
    Route::post('/permission','UserController@addNewPermissions');
    Route::post('/role','UserController@addNewRole');
    Route::delete('/herbs/delete/{id}','HerbsController@destroy');
    Route::put('/password/change', 'UserController@passwordChange');
    Route::put('/password/reset', 'UserController@passwordReset');
    Route::post('/periods', 'PeriodController@store');
    Route::delete('/periods/{id}', 'PeriodController@destroy');
    Route::put('/periods/{id}', 'PeriodController@update');
    Route::post('/pickpart', 'PickPartController@store');
    Route::delete('/pickpart/{id}', 'PickPartController@destroy');
    Route::put('/pickpart/{id}', 'PickPartController@update');
    Route::post('/change/email', 'UserController@changeEmail');
    Route::get('/profile','UserController@profile');
});

Route::middleware(['jwt.auth'])->prefix('herbs')->group(function () {
    Route::post('/image/change/{id}', 'HerbsController@imageChange');
    Route::put('/update/{id}', 'HerbsController@update');
    Route::post('/', 'HerbsController@store');
});

Route::middleware(['jwt.auth'])->prefix('users')->group(function () {
    Route::post('/image/change', 'UserController@changeImage');
    Route::delete('/profile', 'UserController@destroy');
    Route::put('/', 'UserController@update');
    Route::get('/page/{page}', 'UserController@index');
    Route::get('/{id}', 'UserController@show');
    Route::post('/like/{id}', 'UserController@likeHerb');
    Route::post('/like/comment/{id}', 'UserController@likeComment');
    Route::delete('/like/{id}', 'UserController@destroyLikeHerb');
    Route::delete('/like/comment/{id}', 'UserController@destroyLikeComment');
    Route::delete('/comment/{id}', 'UserController@commentDestroy');
    Route::put('/comment/{id}', 'UserController@commentUpdate');
    Route::post('/comment/{id}', 'UserController@commentCreate');
});
