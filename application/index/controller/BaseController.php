<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/19
 * Time: 13:45
 */

namespace app\index\controller;

use app\index\service\Token as TokenService;
use think\App;
use think\Controller;

class BaseController extends Controller
{
    public function __construct(App $app = null)
    {
        TokenService::checkExistToken();
        parent::__construct($app);
    }
}