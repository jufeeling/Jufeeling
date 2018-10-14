<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:13
 */

namespace app\index\validate;


use app\lib\exception\ParameterException;

class UserValidate extends BaseValidate
{
    protected $rule =
        [
            'id'       => 'require|isPositiveInteger',
            'check'    => 'require',
            'avatar'   => 'require',
            'nickname' => 'require',
            'orders'   => 'checkOrders'
        ];

    protected $message =
        [
            'check'    => '请选择您要使用的商品',
            'id'       => '请传入正确的id',
            'avatar'   => '头像不能为空',
            'nickname' => '昵称不能为空',
            'goods'    => 'checkOrders'
        ];

    protected $scene =
        [
            'check' =>
                [
                    'check'
                ],

            'id' =>
                [
                    'id'
                ],

            'info' =>
                [
                    'avatar',
                    'nickname'
                ],
            'delete' =>
                [
                    'orders'
                ]
        ];


    protected $singleRule = [
        'order_id' => 'require|isPositiveInteger',
    ];



    /**
     * @param $values
     * @return bool
     * @throws ParameterException
     * 商品信息检查
     */
    protected function checkOrders($values)
    {
        if (!is_array($values))
        {
            throw new ParameterException(
                [
                    'msg' => '订单参数不正确'
                ]);
        }

        if (empty($values))
        {
            throw new ParameterException(
                [
                    'msg' => '订单列表不能为空'
                ]);
        }

        foreach ($values as $value)
        {
            $this->checkOrder($value);
        }
        return true;
    }

    protected function checkOrder($value)
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