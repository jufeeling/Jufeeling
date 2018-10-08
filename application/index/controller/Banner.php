<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/30
 * Time: 12:27
 */

namespace app\index\controller;

use app\index\model\Banner as BannerModel;

class Banner extends BaseController
{
    /**
     * @return \think\response\Json
     * 获取banner图
     */
    public function getBanner(){
        $data = BannerModel::where('status',0)
            ->select();
        return result($data);
    }
}