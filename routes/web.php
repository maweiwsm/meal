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

// 多请求路由
Route::match(['get', 'post'], 'name', function () {
    return 'name';
});

Route::any('any', function () {
    return 'any';
});

//Route::get('user/{id}', function ($id) {
//    return 'User-id-' . $id;
//});

//Route::get('user/{name?}', function ($name = 'sean') {
//    return 'User-name-' . $name;
//})->where('name', '[a-z,A-Z]+');

Route::get('user/{id}/{name?}', function ($id, $name = 'sean') {
    return 'User-id-' . $id . ', User-name-' . $name;
})->where(['id' => '[0-9]+', 'name' => '[a-z,A-Z]+', ]);

// 路由别名
Route::get('user/center', ['as' => 'center', function () {
    return route('center');
}]);

Route::group(['prefix' => 'admin'], function () {
    Route::any('group', function () {
        return 'admin group';
    });
});

// 绑定控制器
//Route::get('info', 'HomeController@info');
// 多请求路由
Route::match(['get', 'post'], 'info/{id}', [
    'uses' => 'HomeController@info',
    'as' => 'info']);

// 初始化认证路由
Auth::routes();

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/order', 'OrderController');

Route::get('excel/export','ExcelController@export');
Route::get('excel/import','ExcelController@import');
