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
        //获取用户所有优惠券
       Route::get('/','User/getUserCoupon') ;
    });
    Route::group('goods',function (){
        //获取用户所有商品
       Route::get('/',      'user/getUserGoods');    //测试成功
        //用户选择聚会要使用的商品
       Route::post('select','user/selectUserGoods'); //测试成功
    });
    Route::group('order',function (){
        //获取用户所有订单
       Route::get('/',    'user/getUserOrder');
       //获取用户单个订单详情
       Route::get('info', 'user/getUserOrderInfo');
       //用户删除订单
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
    Route::get('all',     'Goods/getAllGoods');     //测试成功
    Route::get('detail',  'Goods/getGoodsDetail');
    Route::post('search', 'Goods/getSearchGoods');
});

/**
 * 派对模块
 */
Route::group('party',function (){
    //获取派对详情
    Route::get('get',      'Party/getParty');
    //参加派对
    Route::get('join',     'Party/joinParty');
    //获取派对所需要的商品
    Route::get('goods',    'Party/getPartyGoods'); //测试成功
    //举办派对
    Route::post('host',    'Party/hostParty');
    Route::post('comment', 'Party/commentParty');
});

/**
 * 抽奖模块
 */
Route::group('prize',function (){
    Route::get('draw','Prize/prizeDraw');
});
/**
 *文件
 */
Route::group('file',function (){
    Route::post('upload',    'File/uploadImage');
});
Route::get('test','User/test');