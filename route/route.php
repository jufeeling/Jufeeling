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
        Route::get('host','User/getUserHostParty');  //测试成功
        Route::get('join','User/getUserJoinParty');  //测试成功
    });
    Route::group('address',function (){
        Route::get('/','User/getUserDeliveryAddress'); //测试成功
    });
    Route::group('coupon',function (){
        //获取用户所有优惠券
       Route::get('/','User/getUserCoupon') ;  //测试成功
    });
    Route::group('goods',function (){
        //获取用户所有商品
       Route::get('/',      'user/getUserGoods');    //测试成功
        //用户选择聚会要使用的商品
       Route::post('select','user/selectUserGoods'); //测试成功
    });
    Route::group('order',function (){
        //获取用户所有订单
       Route::get('/',    'user/getUserOrder');  //测试成功
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
    Route::get('get',      'DeliveryAddress/getDeliveryAddress'); //测试成功
    Route::post('add',     'DeliveryAddress/addDeliveryAddress'); //测试成功
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
    Route::get('detail',  'Goods/getGoodsDetail');  //测试成功
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
    //图片上传
    Route::post('upload',    'File/uploadImage');
});

/**
 * 购物车
 */
Route::group('cart',function (){
    //添加商品到购物车
    Route::post('add','Cart/addShoppingCart');    //测试成功
    //获取购物车商品信息
    Route::get('info','Cart/getShoppingCartInfo');  //测试成功
    //修改购物车商品数量
    Route::post('count','Cart/changeCartCount');
    //删除购物车
    Route::delete('/','Cart/deleteCart');
    //选择物车
    Route::post('select','Cart/selectCart');
    //全选
    Route::post('all/select','Cart/selectAllCart');
});

/**
 * 支付
 */
Route::group('pay',function (){
    //支付
    Route::post('/',      'Pay/payOrder');
    Route::post('fail',   'Pay/payFail');
    Route::post('success','Pay/paySuccess');
});

/**
 * 订单
 */
Route::group('order',function (){
    //生成预订单
    Route::post('pre',     'Order/generatePreOrder');
    //生成订单
    Route::post('generate','Order/generateOrder');

});