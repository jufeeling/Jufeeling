<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 17:28
 */

namespace app\index\validate;

use app\lib\exception\ParameterException;

class PartyValidate extends BaseValidate
{
    protected $rule =
        [
            'id'          => 'require|isPositiveInteger',
            'content'     => 'require',
            'description' => 'require',
            'image'       => 'require',
            'way'         => 'require',
            'people_no'   => 'require|isPositiveInteger',
            'date'        => 'require',
            'time'        => 'require',
            'site'        => 'require',
            'orders'      => 'checkOrders',
            'latitude'    => 'require',
            'longitude'   => 'require',
            'code'        => 'require'
        ];

    protected $message =
        [
            'id'          => '请传入正确的id',
            'content'     => '评论内容不能为空',
            'description' => '聚说不能为空',
            'way'         => '方式不能为空',
            'people_no'   => '人数不能为空',
            'date'        => '日期不能为空',
            'time'        => '时间不能为空',
            'site'        => '地点不能为空',
            'url'         => '图片地址不能为空',
            'orders'      => '订单列表不能为空',
            'latitude'    => '纬度不能为空',
            'longitude'   => '经度不能为空',
            'code'        => 'code不能为空'
        ];

    protected $scene =
        [
            'id' =>
                [
                    'id'
                ],

            'comment' =>
                [
                    'id',
                    'content'
                ],

            'host' =>
                [
                    'description',
                    'way',
                    'people_no',
                    'date',
                    'time',
                    'site',
                    'image',
                    'orders',
                    'latitude',
                    'longitude'
                ],
        ];

    protected $singleRule =
        [
            'order_id' => 'require|number',
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