<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 17:32
 */

namespace app\index\service;

use app\index\model\Message;
use app\index\model\Party as PartyModel;
use app\index\model\PartyGoods;
use app\index\model\PartyOrder;
use app\index\service\Token as TokenService;
use app\lib\enum\IdentityEnum;
use app\lib\enum\PartyEnum;
use app\lib\exception\PartyException;

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
        $party = PartyModel::field('user_id,state,people_no,remaining_people_no,start_time,id')
            ->where('is_delete', PartyEnum::OPEN)
            ->find($data['id']);
        //判断是否存在
        //判断请求是否来与发起者
        //判断聚会是否已关闭状态
        //判断开始时间是否已过
        //判断是否还有名额
        if ($party && $party['user_id'] != TokenService::getCurrentUid() && $party['state'] == PartyEnum::OPEN) {
            if ($party['start_time'] < time()) {
                throw new PartyException(['msg' => '已经过了派对的开始时间了哦!']);
            }
            if ($party['remaining_people_no'] <= 0) {
                throw new PartyException(['msg' => '抱歉,报名人数已满!']);
            }
            //一起没问题
            //判断是否重复参加
            $party_order = PartyOrder::where('party_id', $data['id'])
                ->where('user_id', TokenService::getCurrentUid())
                ->find();
            if ($party_order) {
                throw new PartyException(['msg' => '您已参加了该派对']);
            }
            //判断参加的聚会是否有人数限制,如果没有的话remaining_people_no不做变化(小于11即有人数限制)
            PartyOrder::create([
                'party_id' => $party['id'],
                'user_id' => TokenService::getCurrentUid(),
                'status' => 0
            ]);
            if ($party['people_no'] < 11) {
                $party['remaining_people_no'] -= 1;
                $party->save();
            }
        }
        else
        {
            throw new PartyException(['msg' => '请刷新界面']);
        }
    }

    /**
     * @param $data
     * @throws PartyException
     * 关闭聚会
     */
    public function closeParty($data)
    {
        $party = PartyModel::find($data['id']);
        //判断聚会是否存在以及判断用户是否有权限关闭
        if ($party && $party['user_id'] == TokenService::getCurrentUid()) {
            $party['state'] = PartyEnum::CLOSE;
            $party->save();
        } else {
            throw new PartyException(['msg' => '你没有权利执行此操作']);
        }
    }

    /**
     * @param $data
     * @throws PartyException
     * 提前成行
     */
    public function doneParty($data)
    {
        $party = PartyModel::find($data['id']);
        //判断聚会是否存在以及判断用户是否有权限关闭
        if ($party && $party['user_id'] == TokenService::getCurrentUid())
        {
            $party['state'] = PartyEnum::DONE;
            $party->save();
        }
        else
        {
            throw new PartyException(['msg' => '你没有权利执行此操作']);
        }
    }

    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * @throws PartyException
     * 留言
     */
    public function commentParty($data)
    {
        $party = PartyModel::where('is_delete', PartyEnum::OPEN)
            ->find($data['id']);
        //如果派对存在并且状态不为关闭
        if ($party &&
            ($party['state'] != PartyEnum::CLOSE ||
                $party['start_time'] > time())
        ) {
            Message::create([
                'user_id' => TokenService::getCurrentUid(),
                'party_id' => $data['id'],
                'content' => base64_encode($data['content'])
            ]);
        } else {
            throw new PartyException(['msg' => '该聚会已经不能评论了哦']);
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

        /**
         * 注意 此时聚会状态为Close,点击确定按钮以后修改状态
         */
        $start_time = $data['date'] . $data['time'];
        $value = [
            'way' => $data['way'],
            'date' => $data['date'],
            'time' => $data['time'],
            'site' => base64_encode($data['site']),
            'image' => $data['image'],
            'user_id' => TokenService::getCurrentUid(),
            'people_no' => (int)$data['people_no'],
            'start_time' => strtotime($start_time),
            'description' => base64_encode($data['description']),
            'remaining_people_no' => (int)$data['people_no'] - 1,
            'status' => PartyEnum::CLOSE,
            'longitude' => $data['longitude'],
            'latitude' => $data['latitude'],
            'create_time' => time(),
            'update_time' => time()
        ];
        $id = PartyModel::insertGetId($value);
        //拿到id
        //先判断是否有选择来点feel商品举办派对
        //如果有则绑定id
        if ($data['orders'][0]['order_id'] != 0) {
            foreach ($data['orders'] as $item) {
                PartyGoods::create([
                    'party_id' => $id,
                    'goods_id' => $item['order_id'], // 其实代表的是goods_id 前端未修改名字
                ]);
            }
        }
        return $id;
    }

    /**
     * @param $id
     * 绑定来点feel物品到聚会
     */
    public function bindGoodsToParty($id)
    {
        //将聚会的状态改为Open
        PartyModel::where('id', $id)->setField(['status' => PartyEnum::OPEN]);
    }

    /**
     * @param $data
     * @return mixed
     * @throws PartyException
     */
    public function getParty($data)
    {

        //先获取token
        //获取参与者的信息
        //获取此派对的来点feel(关联orderId并通过orderId关联Goods)
        //获取聚会评论
        //获取发起者信息
        //得到评论此聚会人的身份

        $party = PartyModel::with(['participants' => function ($query) {
            $query->with(['user' => function ($query) {
                $query->field('avatar,id,nickname');
            }])
                ->order('create_time desc');
        }])
            ->with(['goods' => function ($query) {
                $query->with(['goods' => function ($query) {
                        $query->field('id,name,price,sale_price,thu_url')
                            ->with('label');
                    }]);
            }])
            ->with(['message' => function ($query) {
                $query->with('user')->order('create_time desc');
            }])
            ->with(['user' => function ($query) {
                $query->field('id,avatar,nickname');
            }])
            ->where('is_delete', PartyEnum::OPEN)
            ->find($data['id']);
        if ($party) {
            $party['site'] =html_entity_decode(base64_decode($party['site']));
            $party['description'] = html_entity_decode(base64_decode($party['description']));
            //前端需要参与者的个数(加上发起者)
            $party['participants_count'] = count($party['participants']) + 1;
            //得到参与者的头像(虚拟)
            $party['avatar'] = $this->getPartyUserAvatar($party['participants']);
            //反转义字符
            $party['message'] = $this->html_entity_decode($party['message']);
            //将participants从数组中删除
            unset($party['participants']);
            $party = $this->getPartyStatus($party);
            return $party;
        }
        throw new PartyException(['msg' => '聚会不存在']);
    }

    /**
     * @param $data
     * @return array
     * 重新组装聚会详情页参与者的头像
     */
    public function getPartyUserAvatar($data)
    {
        $array = array();
        if (count($data) < 6) {
            for ($i = 0; $i < count($data); $i++) {
                $array[$i]['avatar'] = $data[$i]['user']['avatar'];
                $array[$i]['nickname'] = $data[$i]['user']['nickname'];
            }
            for ($i = count($data); $i < 6; $i++) {
                $array[$i]['avatar'] = 'https://jufeel.jufeeling.com/static/image/icon/837368762097679221.png';
                $array[$i]['nickname'] = '';
            }
        } else {
            for ($i = 0; $i < count($data); $i++) {
                $array[$i]['avatar'] = $data[$i]['user']['avatar'];
                $array[$i]['nickname'] = $data[$i]['user']['nickname'];
            }
        }
        return $array;
    }

    /**
     * @param $data
     * @return mixed
     * 反转义字符
     */
    private function html_entity_decode($data)
    {
        foreach ($data as $item) {
            $item['content'] = base64_decode($item['content']);
            $item['content'] = html_entity_decode($item['content']);
        }
        return $data;
    }

    /**
     * @param $party
     * @return mixed
     * 获取查看聚会的用户的身份以及聚会此时的状态
     */
    private function getPartyStatus($party)
    {
        //先判定状态
        //除了进行中,其余三中状态对于每种人来说都是一样的视图
        //进行中->判定身份
        //如果是路人还要判定聚会人数是否已满
        //详情参考OSS、聚会状态.xlsx
        $uid = TokenService::getCurrentUid();
        if ($party['state'] == PartyEnum::CLOSE) {
            $pStatus = 6;
        } else if ($party['state'] == PartyEnum::DONE) {
            $pStatus = 5;
        } else if ($party['state'] == PartyEnum::OPEN && $party['start_time'] < time()) {
            $pStatus = 4;
        } else {
            if ($party['user_id'] == $uid) {
                $pStatus = 1;
            } else {
                $record = PartyOrder::where('party_id', $party['id'])
                    ->where('user_id', $uid)
                    ->find();
                if ($record) {
                    $pStatus = 2;
                } else {
                    if ($party['remaining_people_no'] > 0) {
                        $pStatus = 3;
                    } else {
                        $pStatus = 2;
                    }
                }
            }
        }
        $party['pStatus'] = $pStatus;
        return $this->getMessageIdentity($party);
    }

    /**
     * @param $data
     * @return mixed
     * 获取评论人的身份
     */
    public function getMessageIdentity($data)
    {
        //遍历所有消息
        //遍历所有参与者
        //判断发消息的人user_id是否属于聚会发起者
        //判断user_id是否属于参与者
        foreach ($data['message'] as $d_m) {
            if ($d_m['user_id'] == $data['user_id']) {
                $d_m['identity'] = IdentityEnum::SPONSOR;
                //标记为发起者
            } else {
                $pStatus = false;
                foreach ($data['participants'] as $d_p) {
                    if ($d_m['user_id'] == $d_p['user_id']) {
                        $pStatus = true;
                        $d_m['identity'] = IdentityEnum::PARTICIPANT;
                        //标记为参与者
                    }
                }
                if ($pStatus == false) {
                    $d_m['identity'] = IdentityEnum::PEDESTRIANS;
                }
            }
        }
        return $data;
    }
}