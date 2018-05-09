<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 2:37
 * Desc:
 */
namespace app\weiubo\controller;
use think\Controller;
use wechat\auth\Auth as wAuth;
class User extends Controller {

    public function saveOpenId()
    {
        try{
            $auth = new wAuth('appid','appsecret');
            //return ['code'=>0,'msg'=>'success','openId'=>$accArr["openid"],'baseInfo'=>$userInfo];
            $respond = $auth->notify();
            if($respond['code'] == 0){
                die($respond['msg']);
            }
            // openid
            $openId = $respond['openId'];
            // baseInfo
            $baseInfo = $respond['baseInfo'];
            // 写入open表
            $res = model('Open')->addOpenId($openId);
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }
}