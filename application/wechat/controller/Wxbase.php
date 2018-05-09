<?php
namespace app\wechat\controller;

use think\Controller;
use think\Request;

class Wxbase extends Controller
{
   public function _initialize()
   {
       parent::_initialize(); // TODO: Change the autogenerated stub
       cookie('openId', null);
       // 检查openId是否有效
       if(!cookie("openId")){
           $this->redirect(url('wechat/auth/oauth2_access'));
       }
   }
}
