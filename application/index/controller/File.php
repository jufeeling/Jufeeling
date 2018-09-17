<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 18:11
 */

namespace app\index\controller;

use app\index\service\File as FileService;
use think\Controller;

class File extends Controller
{
    /**
     * @return \think\response\Json
     * 上传图片
     */
    public function uploadImage(){
        $url = (new FileService())->uploadImage();
        return result($url);
    }
}