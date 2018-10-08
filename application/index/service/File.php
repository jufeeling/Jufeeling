<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/5/24
 * Time: 11:17
 */

namespace app\index\service;

use app\lib\exception\PartyException;

class File
{

    private $url = 'jufeel/jufeeling/public/static/image/upload/';

    /**
     * 多图片上传
     */
    public function uploadImage()
    {
        $file = request()->file('image');
        if ($file) {
            //将文件移到upload路径下并得到文件名
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'image' . DS . 'upload');
            if ($info) {
                //组装url
                $url = $this->url . str_replace('\\', '/', $info->getSaveName());
                return $url;
            } else {
                // 上传失败获取错误信息
                return $file->getError();
            }
        } else {
            throw new PartyException([
                'msg' => '文件不存在'
            ]);
        }
    }


}