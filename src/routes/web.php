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

Route::get('/test1', 'UserController@test');
//注册
Route::post('/register', 'AuthController@register');
//登录
Route::post('/login', 'AuthController@login');
//获取
Route::post('/get', 'AuthController@me');
//退出
Route::post('/logout', 'AuthController@logout');

//发送消息
Route::post('/send/message', 'MessageController@sendMessage');
//获取主页列表数据
Route::get('/home/message', 'MessageController@getHomeMessage');
//获取聊天框好友消息
Route::post('/home/message/detail', 'MessageController@getMessageList');
//添加(剔除)好友到聊天室
Route::post('/message/add', 'MessageController@addFriend');
//获取我的好友通讯录
Route::get('/home/friend', 'MessageController@getMyfriendList');
//添加好友到我的通讯录
Route::post('/home/friend/add', 'MessageController@addBookFriend');


