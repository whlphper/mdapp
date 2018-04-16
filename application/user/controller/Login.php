<?php
namespace app\user\controller;
use app\common\controller\Base;
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
        // 判断是否存在cookie
        if(cookie('md_remember_username') && cookie('md_remember_userpass')){
            $rememberInfo = ['username'=>cookie('md_remember_username'),'password'=>cookie('md_remember_userpass')];
        }
        return view('',isset($rememberInfo) ? ['rememberInfo'=>$rememberInfo] : []);
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
            mdLog($e);
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

    /*
      * 初始化菜单
      * @param  $rolesId 用户角色ID
      * @return array
      * */
    public function initMenu($rolesId='')
    {
        $rolesId = session('user.roles_id');
        try{
            if(empty($rolesId)){
                throw new \think\Exception('用户角色ID不能为空');
            }
            // 获取用户下menu集
            $menuInfo = model('Roles')->getMenuStr($rolesId);
            if(isset($menuInfo['code']) && $menuInfo['code'] == 0){
                throw new \think\Exception($menuInfo['msg']);
            }
            if(empty($menuInfo['menu_ids']) && session("user.type") != '1001001'){
                throw new \think\Exception($menuInfo['name'].'下尚未配置菜单');
            }
            // 获取真正的菜单数组
            $menuList = model('Menus')->getList($menuInfo['menu_ids']);
            if(empty($menuList)){
                throw new \think\Exception('菜单不存在或者已经删除');
            }
            return list_to_tree($menuList,'id','pid','subMenus',0);
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage(),'data'=>[]];
        }
    }

    public function getAllMenu()
    {
        $menus = model("Roles")::get(session("user.roles_id"))->toArray();
        $menuIds = $menus['menu_ids'];
        $list = model('Menus')->getList($menuIds);
        if($menuId = $this->request->param('id')){
            $idStr = model('Roles')->getField(['id'=>$menuId],'menu_ids');
            if($idStr['code'] == 0){
                return ['code'=>0,'msg'=>'角色权限获取失败'];
            }
            $idStr = explode(',',$idStr['data']);
            foreach ($list as $k => $v) {
                \think\Log::notice($v['id']);
                \think\Log::notice($idStr);
                if (in_array($v['id'], $idStr)) {
                    $list[$k]['checked'] = true;
                }
            }
        }
        return ['code'=>1,'msg'=>'','data'=>$this->getMenuSubs($list,0,1,'pid')];
    }

    public function getMenuSubs($categorys, $catId = 0, $level = 1, $filed)
    {
        $subs = array();
        foreach ($categorys as $item) {
            if ($item[$filed] == $catId) {
                $item['level'] = $level;
                $item['pId'] = $item['pid'];
                $item['open'] = false;
                unset($item['icon']);
                unset($item['url']);
                $subs[] = $item;
                $subs = array_merge($subs, $this->getMenuSubs($categorys, $item['id'], $level + 1, $filed));
            }
        }
        return $subs;
    }



}