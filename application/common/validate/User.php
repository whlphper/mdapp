<?php
namespace app\common\validate;
use think\Validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 15:03
 */
class User extends Validate
{
    protected $rule =   [
        'username|账号或者手机号'  => 'require|max:25',
        'password|登陆密码'   => 'require|length:3,25',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
    ];

    protected $scene = [
        'login'  =>  ['username','password'],
    ];
}