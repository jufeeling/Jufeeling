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

/**
 * 用户模块
 */
Route::group('user',function (){
    Route::group('party',function (){
        Route::get('host','User/getUserHostParty');
        Route::get('join','User/getUserJoinParty');
    });
    Route::group('address',function (){
        Route::get('/','User/getUserDeliveryAddress');
    });
    Route::group('coupon',function (){
       Route::get('/','User/getUserCoupon') ;
    });
    Route::group('goods',function (){
       Route::get('/',      'user/getUserGoods');
       Route::post('select','user/selectUserGoods'); //未测试
    });
    Route::group('order',function (){
       Route::get('/',    'user/getUserOrder');
       Route::get('info', 'user/getUserOrderInfo');
       Route::delete('/', 'user/deleteUserOrder');
    });
});

/**
 * 收货地址模块
 */
Route::group('address',function (){
    Route::get('get',      'DeliveryAddress/getDeliveryAddress');
    Route::post('add',     'DeliveryAddress/addDeliveryAddress');
    Route::post('update',  'DeliveryAddress/updateDeliveryAddress');
    Route::delete('delete','DeliveryAddress/deleteDeliveryAddress');
});

/**
 * 购物券模块
 */
Route::group('coupon',function (){
    Route::get('all',     'Coupon/getAllCoupon');
    Route::get('receive', 'Coupon/receiveCoupon');
});

/**
 * 商品模块
 */
Route::group('goods',function (){
    Route::get('all',     'Goods/getAllGoods');
    Route::get('detail',  'Goods/getGoodsDetail');
    Route::post('search', 'Goods/getSearchGoods');
});

/**
 * 派对模块
 */
Route::group('party',function (){
    Route::get('get',      'Party/getParty');
    Route::get('join',     'Party/joinParty');
    Route::post('host',    'Party/hostParty'); //举办派对
    Route::post('comment', 'Party/commentParty');

});

/**
 * 抽奖模块
 */
Route::group('prize',function (){
    Route::get('draw','Prize/prizeDraw');
});