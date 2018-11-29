<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/5/24
 * Time: 11:17
 */

namespace app\index\service;

use app\lib\exception\PartyException;
use OSS\Core\OssException;
use OSS\OssClient;

require '../extend/oss/src/OSS/OssClient.php';
require '../extend/oss/autoload.php';

class File
{
    private $accessKeyId = 'LTAIjfhhjAEa69tU';
    private $accessKeySecret = 'z9jMoqELKVfFwzJUtJVsh304Cwq1LD';
    private $endpoint = 'http://oss-cn-hangzhou.aliyuncs.com';
    private $bucket = 'joofeel';
    private $url = 'https://oss.joofeel.com/upload/';


    /**
     * 图片上传
     */
    public function uploadImage()
    {
        $file = request()->file('image');
        if ($file) {
            //将文件移到upload路径下并得到文件名
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'image' . DS . 'upload');
            if ($info) {
                $url = $this->ossUpload($info);
                return $url;
            } else {
                // 上传失败获取错误信息
                return $file->getError();
            }
        } else {
            throw new PartyException(['msg' => '文件不存在']);
        }
    }

    /**
     * @param $info
     * @return string
     * 将图片上传到阿里云OSS
     */
    public function ossUpload($info){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        if( !$ossClient->doesBucketExist($this->bucket)){
            $ossClient->createBucket($this->bucket);
        }
        $object = 'upload/'. str_replace('\\', '/', $info->getSaveName());//想要保存文件的名称
        //找到文件在服务器上的根目录
        $file = ROOT_PATH . 'public' . DS . 'static' . DS . 'image' . DS . 'upload/' . str_replace('\\', '/', $info->getSaveName());
        try{
            //将文件上传到OSS
            $ossClient->uploadFile($this->bucket,$object,$file);
            //删除服务器上的文件
            //unlink($file);
        } catch(OssException $e) {
            printf($e->getMessage() . "\n");
        }
        return $this->url. str_replace('\\', '/', $info->getSaveName());
    }
}
