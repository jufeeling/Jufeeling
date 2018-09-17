<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 17:32
 */

namespace app\index\service;

use app\index\model\Message;
use app\index\model\OrderId;
use app\index\model\Party as PartyModel;
use app\index\model\PartyOrder;
use app\index\service\Token as TokenService;
use app\lib\enum\IdentityEnum;
use app\lib\enum\PartyEnum;
use app\lib\exception\PartyException;
use app\index\service\File as FileService;
use think\facade\Cache;

class Party
{
    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws PartyException
     * 参加聚会
     */
    public function joinParty($data)
    {
        $party = PartyModel::find($data['id']);
        if ($party) {
            $party_order = PartyOrder::where('party_id', $data['id'])
                ->where('user_id', TokenService::getCurrentUid())
                ->find();
            if ($party_order) {
                throw new PartyException([
                    'code' => '606',
                    'msg' => '您已参加了该派对',
                    'errorMsg' => '60006'
                ]);
            } else {
                $start_time = $party['date'] . ' ' . $party['time'];
                if ($party['user_id'] == TokenService::getCurrentUid()) {
                    throw new PartyException([
                        'code' => '608',
                        'msg' => '您是该派对的发起者,已参加',
                        'errorMsg' => '60008'
                    ]);
                } else if ($party['state'] == PartyEnum::CLOSE) {
                    throw new PartyException([
                        'code' => '602',
                        'msg' => '该派对暂时不能参加',
                        'errorMsg' => '60002'
                    ]);
                } else if (strtotime($start_time) < time()) {
                    throw new PartyException([
                        'code' => '603',
                        'msg' => '抱歉,已经过了报名时间',
                        'errorMsg' => '60003'
                    ]);
                } //判断该聚会是否符合10人以上规格
                else if ($party['people_no'] != 11) {
                    if ($party['remaining_people_no'] == 0) {
                        throw new PartyException([
                            'code' => '604',
                            'msg' => '抱歉,报名人数已满',
                            'errorMsg' => '60004'
                        ]);
                    }
                } else if (
                PartyOrder::create([
                    'party_id' => $party['id'],
                    'user_id' => TokenService::getCurrentUid(),
                    'status' => 0
                ])
                ) {
                    $party['remaining_people_no'] -= 1;
                    $party->save();
                } else {
                    throw new PartyException([
                        'code' => '605',
                        'msg' => '服务器内部错误',
                        'errorMsg' => '60005'
                    ]);
                }
            }
        } else {
            throw new PartyException();
        }
    }

    /**
     * @param $data
     * @throws PartyException
     * 评论派对
     */
    public function commentParty($data)
    {
        $party = PartyModel::find($data['id']);
        if ($party) {
            if (
            Message::create([
                'user_id' => TokenService::getCurrentUid(),
                'party_id' => $data['id'],
                'content' => $data['content']
            ])
            ) ;
            else {
                throw new PartyException([
                    'code' => '605',
                    'msg' => '服务器内部错误',
                    'errorMsg' => '60005'
                ]);
            }
        } else {
            throw new PartyException();
        }
    }

    /**
     * @param $data
     * @return \think\response\Json
     * @throws PartyException
     * 举办派对
     */
    public function hostParty($data)
    {
//        if ($data['url']) {
//            $url = $data['url'];
//        } else {
//            try {
//                $url = (new FileService())->uploadImage();
//            } catch (PartyException $e) {
//                return result('', $e->msg);
//            }
//        }
        if (
        PartyModel::create([
            'way' => $data['way'],
            'date' => $data['date'],
            'time' => $data['time'],
            'site' => $data['site'],

            'user_id' => 1,
            'people_no' => (int)$data['people_no'],
            'description' => $data['description'],
            'remaining_people_no' => (int)$data['people_no'] - 1,
        ])
        ) {
            //获取并删除缓存
            $check = Cache::pull('select');
            for ($i = 0; $i < sizeof($check); $i++) {
                $orderId = OrderId::find($check[$i]);
                $orderId['select'] = 1;
                $orderId->save();
            }
        } else {
            throw new PartyException([
                'code' => '607',
                'msg' => '举办失败,可能是服务器内部错误',
                'errorMsg' => '60007'
            ]);
        }
    }

    /**
     * @param $data
     * @return mixed
     * 得到聚会详情
     */
    public function getParty($data)
    {
        $party = PartyModel::with(['participants' => function ($query) {
            $query->with('user');
        }])
            ->with(['message' => function ($query) {
                $query->with('user');
            }])
            ->where('id', $data['id'])
            ->find();
        $party['way'] = config('jufeel_config.way')[$party['way']];
        $data = $this->getMessageIdentity($party);
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 获取评论人的身份
     */
    public function getMessageIdentity($data)
    {
        foreach ($data['message'] as $d_m) {
            foreach ($data['participants'] as $d_p) {
                if ($d_m['user_id'] == $data['user_id']) {
                    $d_m['identity'] = IdentityEnum::SPONSOR; //标记为发起者
                } else if ($d_m['user_id'] == $d_p['user_id']) {
                    $d_m['identity'] = IdentityEnum::PARTICIPANT; //标记为参与者
                } else {
                    $d_m['identity'] = IdentityEnum::PEDESTRIANS; //标记为路人
                }
            }
        }
        return $data;
    }

    /**
     * @return array|null|\PDOStatement|string|\think\Model
     * 获取派对已选择的商品
     */
    public function getPartyGoods()
    {
        $select = Cache::get('select');
        for ($i = 0; $i < sizeof($select); $i++) {
            $data[$i] = OrderId::with(['goods' => function ($query) {
                $query->field('id,name,pic_url');
            }])
                ->order('create_time desc')
                ->field('goods_id')
                ->find($select[$i]);
        }
        return $data;
    }
}