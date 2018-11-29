<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/18
 * Time: 15:36
 */

namespace app\index\validate;

use app\lib\exception\ParameterException;

class CartValidate extends BaseValidate
{
    protected $rule =
        [
            'goods_id' => 'require|isPositiveInteger',
            'count'    => 'require|isPositiveInteger',
            'id'       => 'require|isPositiveInteger',
            'select'   => 'require',
            'goods'    => 'checkProducts'
        ];

    protected $message =
        [
            'goods'  => '商品列表不能为空',
            'count'  => '数量不能为空',
            'id'     => 'id不能为空',
            'select' => '选择的状态不能为空'
        ];

    protected $scene =
        [
            'add' =>
                [
                    'goods'
                ],

            'count' =>
                [
                    'count',
                    'id'
                ],

            'id' =>
                [
                    'id'
                ],

            'select' =>
                [
                    'id',
                    'select'
                ],

            'all' =>
                [
                    'select'
                ],
        ];


    protected $singleRule = [
        'goods_id' => 'require|isPositiveInteger',
        'count'    => 'require|isPositiveInteger',
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