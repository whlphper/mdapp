<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 3:26
 * Desc:
 */
namespace app\common\validate;
use think\Validate;
class Task extends Validate
{
    protected $rule =   [
        'belongto|跟随大牛'  => 'require|isStrong',
        'account|账号'  => 'require|number|unique:Task',
        'password|密码'  => 'require',
        'name|牛人名称'  => 'require|unique:Task',
        'multiple|跟单比例'  => 'require|between:1,100',
        'idcard|身份证号'  => 'require|idCard|unique:Task',
        'contact|手机号'  => 'require|mobile|unique:Task',
        'email|邮箱'  => 'require|email|unique:Task',
        'verifycode|验证码'  => 'require|captcha',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'follow'  =>  ['account','belongto','contact','multiple','password','idcard','email','verifycode'],
        'followStrong'  =>  ['account'=>'require|number|unique:Task|strongIn','contact','name','password','idcard','email','verifycode'],
    ];

    public function isStrong($value)
    {
        if(!model('Accinfo')->where('account',$value)->find()){
            return '跟随的大牛账号不存在,请重试';
        }
        return true;
    }

    public function strongIn($value)
    {
        if(model('Accinfo')->where('account',$value)->find()){
            return '此大牛账号重复';
        }
        return true;
    }
}