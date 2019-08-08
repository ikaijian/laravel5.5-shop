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
    return view('welcome');
});

//Route::get('/','PagesController@root')->name('root');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


//路由组
Route::group(['middleware'=>'auth'],function (){

    Route::get('/email_verify_notice','PagesController@emailVerifyNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify','EmailVerificationController@verify')->name('email_verification.verify');
    Route::get('/email_verification/send','EmailVerificationController@send')->name('email_verification.send');

    //用户收藏商品
    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    //用户取消收藏
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

    //验证邮箱中间件email_verified
    Route::group(['middleware' => 'email_verified'], function() {
        Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
        Route::get('user_addresses/create','UserAddressesController@create')->name('user_addresses.create');
        Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
        Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
        Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
        Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
        //我的收藏商品列表
        Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
        //添加购物车
        Route::post('cart', 'CartController@add')->name('cart.add');
        //购物车列表
        Route::get('cart', 'CartController@index')->name('cart.index');
        //移除购物车
        Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

        //订单
        Route::post('orders', 'OrdersController@store')->name('orders.store');
        //订单列表
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        //订单详情
        Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
    });
});

//商品模块路由
Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index'); //列表
Route::get('products/{product}', 'ProductsController@show')->name('products.show');//详情

////支付宝测试
//Route::get('alipay', function() {
//    return app('alipay')->web([
//        'out_trade_no' => time(),
//        'total_amount' => '1',
//        'subject' => 'test subject - 测试',
//    ]);
//});