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
use app\index\model\PartyOrder;
use app\index\service\Token as TokenService;
use app\lib\exception\PartyException;

class Party
{
    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws PartyException
     * 参加聚会
     */
    public function joinParty($data){
        $party = PartyModel::find($data['id']);
        if($party){
            $party_order = PartyOrder::where('party_id',$data['id'])
                ->where('user_id',TokenService::getCurrentUid())
                ->find();
            if($party_order){
                throw new PartyException([
                    'code'     => '606',
                    'msg'      => '您已参加了该派对',
                    'errorMsg' => '60006'
                ]);
            }
            else{
                $start_time = $party['date'].' '.$party['time'];
                if($party['state'] == 1){
                    throw new PartyException([
                        'code'     => '602',
                        'msg'      => '该派对暂时不能参加',
                        'errorMsg' => '60002'
                    ]);
                }
                else if(strtotime($start_time) < time()){
                    throw new PartyException([
                        'code'     => '603',
                        'msg'      => '抱歉,已经过了报名时间',
                        'errorMsg' => '60003'
                    ]);
                }
                else if($party['remaining_people_no'] == 0){
                    throw new PartyException([
                        'code'     => '604',
                        'msg'      => '抱歉,报名人数已满',
                        'errorMsg' => '60004'
                    ]);
                }
                else if(
                PartyOrder::create([
                    'party_id' => $party['id'],
                    'user_id'  => TokenService::getCurrentUid(),
                    'status'   => 0
                ])
                ){
                    $party['remaining_people_no'] -= 1;
                    $party->save();
                }
                else{
                    throw new PartyException([
                        'code'     => '605',
                        'msg'      => '服务器内部错误',
                        'errorMsg' => '60005'
                    ]);
                }
            }
        }
        else{
            throw new PartyException();
        }
    }

    /**
     * @param $data
     * @throws PartyException
     * 评论派对
     */
    public function commentParty($data){
        $party = PartyModel::find($data['id']);
        if($party){
            if(
                Message::create([
                    'user_id'  => TokenService::getCurrentUid(),
                    'party_id' => $data['id'],
                    'content'  => $data['content']
                 ])
            );
            else{
                throw new PartyException([
                    'code'     => '605',
                    'msg'      => '服务器内部错误',
                    'errorMsg' => '60005'
                ]);
            }
        }
        else{
            throw new PartyException();
        }
    }
}