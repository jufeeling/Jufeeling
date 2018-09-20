<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/7/6
 * Time: 20:27
 */

return [
    'app_id' => 'wx35c845c26c3c6c61',
    'app_secret' => 'df829ac15f691075636847ab02bc0ac3',
    'login_url'=> "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

];