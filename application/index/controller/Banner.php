<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/30
 * Time: 12:27
 */

namespace app\index\controller;

use app\index\model\Banner as BannerModel;
use app\lib\exception\base\BaseException;
use think\Controller;
use think\facade\Cache;

class Banner extends Controller
{
    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function getBanner()
    {
//        $data = Cache::get('banner');
//        if($data){
//            return result($data);
//        }
//        else{
            $data = BannerModel::where('status',0)
                ->order('isPrize desc')
                ->select();
            return result($data);
//            if(count($data) > 0){
//                Cache::set('banner',$data);
//                return result($data);
//            }
//            throw new BaseException([
//                'msg'  => 'Banner不存在',
//                'code' => 911
//            ]);
      //  }
    }
}