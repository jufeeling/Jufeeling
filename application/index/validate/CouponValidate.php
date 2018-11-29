<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:25
 */

namespace app\index\validate;


use app\lib\exception\ParameterException;

class CouponValidate extends BaseValidate
{
    protected $rule =
        [
            'coupon' => 'checkCoupons',
        ];

    protected $message =
        [
            'coupon' => '请传入正确的id',
        ];

    protected $scene =
        [
            'id' =>
                [
                    'coupon'
                ],
        ];

    protected $singleRule = [
        'id' => 'require|isPositiveInteger',
    ];



    /**
     * @param $values
     * @return bool
     * @throws ParameterException
     * 商品信息检查
     */
    protected function checkCoupons($values)
    {
        if (!is_array($values))
        {
            throw new ParameterException(
                [
                    'msg' => '购物券参数不正确'
                ]);
        }

        if (empty($values))
        {
            throw new ParameterException(
                [
                    'msg' => '购物券列表不能为空'
                ]);
        }

        foreach ($values as $value)
        {
            $this->checkCoupon($value);
        }
        return true;
    }

    protected function checkCoupon($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '购物券列表参数错误',
            ]);
        }
    }
}