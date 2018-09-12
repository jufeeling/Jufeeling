<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;


/**
 * Token
 */
Route::group('token',function (){
    Route::post('user',   'Token/getToken');
    Route::post('verify', 'Token/verifyToken');
});

Route::get('/','Index/index');
/**
 * 用户模块
 */
Route::group('user',function (){
    Route::group('party',function (){
        Route::get('host','User/getUserHostParty');
        Route::get('join','User/getUserJoinParty');
    });
    Route::group('address',function (){
        Route::get('/',   'User/getUserDeliveryAddress');
        Route::post('add','User/addUserDeliveryAddress');
    });

});