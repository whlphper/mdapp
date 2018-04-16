<?php
namespace app\common\controller;
use think\Controller;
/*
 * 基层控制层,所有公共操作函数都在此函数
 * */
class Base extends Controller
{
    public $roleId = null;
    public $menuIds= null;
    // 用户权限全局检查
    public function _initialize()
    {
        // 用户ID
        $userId = session('userId');
        // 加盟商ID
        $franchiseeId = session('franchiseeId');
        // 用户基本信息数组
        $userInfo = session('user');
        if(empty($userId) || empty($userInfo)){
            $loginUrl = url('/user/Login/index');
            $this->redirect($loginUrl);
            return;
        }else{
            $this->roleId = session("user.roles_id");
            $menus = model("Roles")::get(session("user.roles_id"))->toArray();
            $this->menuIds = $menus['menu_ids'];
        }
    }


    public function _empty($name)
    {
        return view($name);
    }
}
