<?php
namespace app\common\controller;
use think\Controller;
/*
 * 基层控制层,所有公共操作函数都在此函数
 * */
class Base extends Controller
{
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
            $list = $this->initMenu($userInfo['roles_id']);
            //dump($list);exit;
            $this->assign('menuList',$list);
        }
    }

    /*
     * 初始化菜单
     * @param  $rolesId 用户角色ID
     * @return array
     * */
    private function initMenu($rolesId)
    {
        try{
            if(empty($rolesId)){
                throw new \think\Exception('用户角色ID不能为空');
            }
            // 获取用户下menu集
            $menuInfo = model('Roles')->getMenuStr($rolesId);
            if(isset($menuInfo['code']) && $menuInfo['code'] == 0){
                throw new \think\Exception($menuInfo['msg']);
            }
            if(empty($menuInfo['menu_ids'])){
                throw new \think\Exception($menuInfo['name'].'下尚未配置菜单');
            }
            // 获取真正的菜单数组
            $menuList = model('Menus')->getList($menuInfo['menu_ids']);
            if(empty($menuList)){
                throw new \think\Exception('菜单不存在或者已经删除');
            }
            return list_to_tree($menuList,'id','pid','subMenus',0);
        }catch(\Exception $e){
          ecpLog($e);
          return ['code'=>0,'msg'=>$e->getMessage(),'data'=>[]];
        }
    }

    public function _empty($name)
    {
        return view($name);
    }
}
