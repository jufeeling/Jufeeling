<?php

namespace app\index\service;

use think\Exception;


/**
 * Class Message
 * @package app\index\service
 * 已弃用
 */
class Message
{
    private $sendUrl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";

    function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->getAccessToken();
        $this->sendUrl = sprintf($this->sendUrl, $token);
    }

    public function sendMessage($user,$content)
    {
        //   $openid = Token::getCurrentTokenVar('openid');
        $post_data = array(
            'touser'    => $user,
            'msgtype'   => 'text',
            'text'      => array(
                'content'   => $content
            )
        );
        $result = curl_message_post($this->sendUrl, json_encode($post_data, JSON_UNESCAPED_UNICODE));
        $result = json_decode($result, true);
        if ($result['errcode'] == 0) {
            return true;
        } else {
            throw new Exception('客服消息发送失败,  ' . $result['errmsg']);
        }
    }
}