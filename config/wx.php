<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/7/6
 * Time: 20:27
 */

return [
    'app_id' => 'wx1ed926cad0405452',
    'app_secret' => 'ffecd224c9b2bfbcc45cebb88cfdaebe',
    'login_url'=> "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

];