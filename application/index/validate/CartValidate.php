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
            'goods'      => 'checkProducts',
        ];

    protected $message =
        [
            'goods'      => '商品列表不能为空',
        ];

    protected $scene =
        [
            'add' =>
                [
                    'goods',
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