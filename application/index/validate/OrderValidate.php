<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:56
 */

namespace app\index\validate;


use app\lib\exception\ParameterException;

class OrderValidate extends BaseValidate
{
    protected $rule =
        [
            'id'         => 'isPositiveInteger',
            'goods'      => 'checkProducts',
            'coupon_id'  => 'require|number',
            'receipt_id' => 'isPositiveInteger',
            'delivery_address' => 'require',
            'count'      => 'require|number',
            'goods_id' => 'require|number',
        ];

    protected $message =
        [
            'id'         => '订单号不能为空',
            'goods'      => '商品列表不能为空',
            'totalPrice' => '支付价格不能为空',
            'receipt_id' => '收获信息不能为空',
            'delivery_address' => '收货信息不能为空',
            'count'      => '数量不能为空',
            'goods_id' => 'goods_id不能为空'
        ];

    protected $scene =
        [
            'generate' =>
                [
                    'goods',
                    'coupon_id',
                    'receipt_id'
                ],
            'pay' =>
                [
                    'id'
                ],
            'pre' =>
                [
                    'delivery_address',
                    'goods_id',
                    'count'
                ]
        ];


    protected $singleRule = [
        'goods_id' => 'require|number',
        'count'    => 'require|number',
    ];



    /**
     * @param $values
     * @return bool
     * @throws ParameterException
     * 商品信息检查
     */
    protected function checkProducts($values)
    {
        if (!is_array($values))
        {
            throw new ParameterException(
                [
                    'msg' => '商品参数不正确'
                ]);
        }

        if (empty($values))
        {
            throw new ParameterException(
                [
                    'msg' => '商品列表不能为空'
                ]);
        }

        foreach ($values as $value)
        {
            $this->checkProduct($value);
        }
        return true;
    }

    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '商品列表参数错误',
            ]);
        }
    }



}