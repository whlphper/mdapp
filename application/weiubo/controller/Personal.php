<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 5:16
 * Desc:
 */
namespace app\weiubo\controller;
use app\common\controller\Base;

class Personal extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Accinfo';
        $this->theme = '个人中心';
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
        session('weiuboOpenId','111');
    }

    public function index()
    {
        // 获取当前微信用户的OPENID
        $openId = session('weiuboOpenId');
        // 去表里找此openid的记录
        $userInfo = model('Mam')->getUserInfo($openId);
        return view('',['data'=>$userInfo]);
    }
}