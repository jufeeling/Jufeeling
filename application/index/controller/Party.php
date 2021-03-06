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

    public function __construct(App $app = null, PartyService $party)
    {
        $this->party = $party;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 举办聚会
     */
    public function hostParty()
    {
        (new PartyValidate())->scene('host')->goCheck(Request::param());
        try {
            $data = $this->party->hostParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data, '举办成功');
    }

    /**
     * @return \think\response\Json
     * 绑定来点feel的物品到聚会
     */
    public function bindGoodsToParty(){
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        $this->party->bindGoodsToParty(Request::param('id'));
        return result();
    }

    /**
     * @return \think\response\Json
     * 参加聚会
     */
    public function joinParty()
    {
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->party->joinParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result('', '参加成功');
    }

    /**
     * @return \think\response\Json
     * 关闭聚会
     */
    public function closeParty(){
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->party->closeParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result('', '关闭成功');
    }

    /**
     * @return \think\response\Json
     * 提前成行
     */
    public function doneParty(){
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->party->doneParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result('', '操作成功');
    }

    /**
     * @return \think\response\Json
     * 评论派对
     */
    public function commentParty()
    {
        (new PartyValidate())->scene('comment')->goCheck(Request::param());
        try {
            $data = $this->party->commentParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data, '评论成功');
    }

    /**
     * @return \think\response\Json
     * 查看派对详情
     */
    public function getParty()
    {
        (new PartyValidate())->scene('id')->goCheck(Request::param());
        try {
            $data = $this->party->getParty(Request::param());
        } catch (PartyException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data, '查看成功');
    }
}