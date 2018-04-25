<?php
namespace app\wechat\controller;
use think\Controller;
use think\Request;
use app\wechat\controller\Wxbase;

class Index extends Wxbase
{
    // 测试获取用户信息
    public function index()
    {
        echo "假设只是首页";
    }
}
