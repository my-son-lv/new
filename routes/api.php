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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// 前台登陆
Route::prefix('login')->group(function(){
    Route::any('login', 'Api\LoginControlle@login');               //注册
    Route::any('imgsum', 'Api\LoginControlle@imgsum');             //图片验证码
    Route::any('sum', 'Api\LoginControlle@sum');                   //手机验证码
}); 