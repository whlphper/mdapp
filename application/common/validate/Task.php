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
        'belongto|跟随大牛'  => 'require|isSelf|isStrong',
        'account|账号'  => 'require|number|followAgain',
        'password|密码'  => 'require',
        'name|牛人名称'  => 'require|unique:Task',
        'multiple|跟单比例'  => 'require|between:-100,100|illege',//小于等于100
        'idcard|身份证号'  => 'require|idCard',
        'contact|手机号'  => 'require|mobile',
        'email|邮箱'  => 'require|email',
        'verifycode|验证码'  => 'require|captcha',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'follow'  =>  ['account','belongto','contact','multiple','password','idcard','email','verifycode'],
        'followStrong'  =>  ['account'=>'require|number|unique:Task|strongIn','contact','name','password','idcard','email','verifycode'],
    ];

    public function isSelf($value)
    {
        if($this->data['account'] == $value){
            return '不能跟随自己';
        }
        return true;
    }

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

    public function followAgain()
    {
        $account = $this->data['account'];
        $belongto = $this->data['belongto'];
        if($mamInfo = model('Mam')->where('account',$this->data['account'])->where('belongto',$belongto)->find()){
            return '账号 '.$account.' 已经跟随了 "'.$mamInfo['name'].'"';
        }
        return true;
    }


    /**
     * 跟单比例不能超过100
     * @param $value
     * @return bool|string
     */
    public function illege($value)
    {
        $followList = model('Mam')->where('account',$this->data['account'])->column('multiple');
        $testList = model('Task')->where('account',$this->data['account'])->column('multiple');
        $allFollow = array_merge($followList,$testList);
        $total = abs($value/100);
        foreach($allFollow as $k=>$v){
            $total += abs($v);
        }
        if($total > 1){
            return '账号总跟随比例不能超过100%';
        }
        return true;
    }
}