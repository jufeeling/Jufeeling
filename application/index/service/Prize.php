<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:44
 */

namespace app\index\service;

use app\index\model\PrizeOrder as PrizeOrderModel;
use app\index\model\Prize as PrizeModel;
use app\index\service\Token as TokenService;
use app\lib\exception\PrizeException;

class Prize
{
    public function prizeDraw($data)
    {
        $prize = PrizeModel::where('state', 0)
            ->find($data['id']);
        if ($prize) {
            $prize_order = PrizeOrderModel::where('prize_id', $data['id'])
                ->where('user_id', TokenService::getCurrentUid())
                ->find();
            if ($prize_order) {
                throw new PrizeException([
                    'code' => 803,
                    'msg' => '您已经抽过奖了',
                    'errorMsg' => 80003
                ]);
            }
            if (
            PrizeOrderModel::create([
                'form_id'  => $data['form_id'],
                'prize_id' => $data['id'],
                'user_id' => TokenService::getCurrentUid()
            ])
            ) ;
            else {
                throw new PrizeException();
            }
        } else {
            throw new PrizeException([
                'code' => 802,
                'msg' => '未找到该抽奖奖品',
                'errorMsg' => 80002
            ]);
        }
    }
}