<?php
namespace app\user\controller;
use think\Request;
use think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 11:49
 */
class Login extends Controller {

    public function index(Request $request)
    {
        return view();
    }

    public function login(Request $request)
    {
        try{
            $data = $request->only(['username','password','rememberMe']);
            // 验证
            $loginVali = $this->validate($data,'User.login');
            if($loginVali !== true){
                throw new \Exception($loginVali);
            }
            // 账号是否合法
            $user = model("User")->checkUser($data['username']);
            if(!$user){
                throw new \Exception('用户不存在');
            }
            $user = $user->toArray();
            if($user['password'] != md5($data['password'])){
                throw new \Exception('登陆密码错误');
            }
            // 记住账号
            if(!empty($data['rememberMe'])){
                cookie('md_remember_username',$data['username'],60*60*24*30);
                cookie('md_remember_userpass',$data['password'],60*60*24*30);
            }else{
                cookie('md_remember_userpass',null);
                cookie('md_remember_username',null);
            }
            // session 跳转系统首页
            session("userId",$user['id']);
            session("franchiseeId",$user['franchisee_id']);
            session("user",$user);
            $indexUrl = url('/admin/Index/index');
            return ['code'=>1,'msg'=>'登陆成功','data'=>['url'=>$indexUrl]];
        }catch(\Exception $e){
            ecpLog($e);
            return ['code'=>0,'msg'=>$e->getMessage(),'data'=>[]];
        }
    }

    public function logout()
    {
        // session 跳转系统首页
        session("userId",null);
        session("franchiseeId",null);
        session("user",null);
        $indexUrl = url('/user/Login/index');
        $this->redirect($indexUrl);
    }
}