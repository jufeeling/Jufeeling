<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 18:11
 */

namespace app\index\controller;

use app\index\service\File as FileService;

class File extends BaseController
{
    /**
     * @return \think\response\Json
     * 上传图片
     */
    public function uploadImage(){
        $url = (new FileService())->uploadImage();
        return $url;
    }
}