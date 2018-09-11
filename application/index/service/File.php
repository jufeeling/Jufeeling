<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/5/24
 * Time: 11:17
 */

namespace app\index\service;
use app\index\model\Image as ImageModel;

class File
{

    /**
     * 多图片上传
     */
    public function uploadImage($id){
        $files = request()->file('image');
        if($files){
            foreach ($files as $file){
                if($file) {
                    //将文件移到upload路径下并得到文件名
                    $info = $files->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'image' . DS . 'upload');
                    if ($info) {
                        //组装url
                        $url = 'http://x2018062501.aweyu.cn/static/image/upload/'.str_replace('\\', '/', $info->getSaveName());
                        ImageModel::create([
                            'post_id' => $id,
                            'url'     => $url
                        ]);
                        return 1;
                    } else {
                        // 上传失败获取错误信息
                        echo $file->getError();
                    }
                }
                return 2;
            }
        }
        return 3;
    }


}