<?php
namespace app\common\model;
use think\Model;
use app\common\model\Base;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 15:12
 */
class User extends Base
{
    /*
     * @ 检查用户账号合法性
     * @ param $username  账号或者手机号
     * @ return mixed
     * */
    public function checkUser($username)
    {
        $user = User::get(['account_number' => $username]);
        return $user;
    }
}