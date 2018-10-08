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
    public function getBanner(){
        $data = BannerModel::where('status',0)
            ->select();
        return result($data);
    }
}