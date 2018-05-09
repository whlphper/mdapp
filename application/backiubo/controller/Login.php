<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 2:06
 * Desc:
 */
namespace app\backiubo\controller;
use think\Controller;
class Login extends Controller{

    public function index()
    {
        return view();
    }

    public function go()
    {
        try{
            $data = $this->request->post();
            $user = model('User')->where('account_number',$data['name'])->find();
            if(!$user){
                throw new \Exception('账号不存在');
            }
            if($data['pass'] !== $user['password']){
                throw new \Exception('密码错误');
            }
            session('backiuboUserId',$user['id']);
            return ['code'=>1,'msg'=>'success'];
        }catch(\Exception $e){
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    public function logout()
    {
        session('backiuboUserId',null);
        $this->redirect(url('/backiubo/Login'));
    }
}