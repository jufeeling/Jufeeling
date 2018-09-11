<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:48
 */

namespace app\index\controller;


use think\App;
use app\index\service\User as UserService;
use think\Controller;

class User extends Controller
{
    private $user;
    public function __construct(App $app = null,UserService $user)
    {
        $this->user = $user;
        parent::__construct($app);
    }

    public function getUserLaunchParty(){
        $data = $this->user->getUserLaunchParty();
        return result($data);
    }
}