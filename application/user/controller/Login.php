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
        if(cookie('adminpassword') && cookie('adminusername')){
            $rememberInfo = ['username'=>cookie('adminusername'),'password'=>passport_decrypt(cookie('adminpassword'))];
        }
        return view('',isset($rememberInfo) ? ['rememberInfo'=>$rememberInfo] : []);
    }

    public function login(Request $request)
    {
        try{
            $order = 'a.created_at desc';
            $field = 'a.id,a.account_number,a.roles_id,a.password,a.franchisee_id,a.avatar,a.nick_name,a.mobile,a.type,a.real_name,b.savePath as avatarPath';
            $join = [['File b','a.avatar=b.id','left']];
            $data = $request->only(['username','password','rememberMe']);
            // 账号是否合法
            $user = model("User")->getRow(['account_number|mobile'=>$data['username']],$field,$join,$order);
            if($user['code'] == 0){
                throw new \Exception('用户'.$user['msg']);
            }
            $user = $user['data'];
            if($user['password'] != md5($data['password'])){
                throw new \Exception('登陆密码错误');
            }
            // 记住账号
            if(!empty($data['rememberMe'])){
                // 加密
                $cookieUserPwd = passport_encrypt($data['password']);
                // 设置
                cookie('adminpassword', $cookieUserPwd, 7200);
                cookie('adminusername', $data['username'], 7200);
            }else{
                cookie('adminpassword',null);
                cookie('adminusername',null);
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
        $menus = model("Roles")->get(session("user.roles_id"))->toArray();
        $menuIds = $menus['menu_ids'];
        $list = model('Menus')->getList($menuIds);
        if($menuId = $this->request->param('id')){
            $idStr = model('Roles')->getField(['id'=>$menuId],'menu_ids');
            if($idStr['code'] == 0){
                return ['code'=>0,'msg'=>'角色权限获取失败'];
            }
            $idStr = explode(',',$idStr['data']);
            if($menus['pid'] == '0'){
                foreach ($list as $k => $v) {
                    $list[$k]['checked'] = true;
                }
                return ['code'=>1,'msg'=>'','data'=>$this->getMenuSubs($list,0,1,'pid')];
            }else{
                foreach ($list as $k => $v) {
                    if (in_array($v['id'], $idStr)) {
                        $list[$k]['checked'] = true;
                    }
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