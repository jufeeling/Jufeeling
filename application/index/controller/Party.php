<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/11
 * Time: 16:48
 */

namespace app\index\controller;


use app\index\validate\PartyValidate;
use app\lib\exception\PartyException;
use think\App;
use think\Controller;
use think\facade\Request;
use app\index\service\Party as PartyService;

class Party extends Controller
{
    private $party;

    public function __construct(App $app = null,PartyService $party)
    {
        $this->party = $party;
        parent::__construct($app);
    }

    public function hostParty(){

    }

    /**
     * @return \think\response\Json
     * 参加聚会
     */
    public function joinParty(){
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->party->joinParty(Request::param());
        }catch (PartyException $e){
            return result('',$e->msg,$e->code);
        }
        return result('','参加成功');
    }

    public function commentParty(){
        (new PartyValidate())->scene('comment')->goCheck(Request::param());
        try{
            $this->party->commentParty(Request::param());
        }catch (PartyException $e){
            return result('',$e->msg,$e->code);
        }
        return result('','评论成功');
    }
}