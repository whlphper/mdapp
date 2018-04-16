<?php

namespace app\admin\controller;

use app\common\controller\Base;
use think\Request;
use think\Db;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 17:36
 */
class Core extends Base
{
    // 获取日志
    public function text()
    {
        echo "<pre>";
        $log = model('log')->getLogRecord();
        print_r($log);
    }
    // 菜单列表
    public function getMenus(request $request)
    {
        $list = model('Menus')->getBootstrapeTable([], 'a.id,a.url,a.name,a.flag,a.pid,a.sort', [], 'a.sort desc');
        return $list;
    }

    public function getMenu(request $request)
    {
        $id = $request->only('id');
        if (!empty($id)) {
            $result = model('Menus')::get($id);
            return ['code'=>1,'msg'=>'','data'=>$result];
        }
        return ['code'=>0,'msg'=>'菜单数据不存在'];
    }

    public function saveMenu(request $request)
    {
        if (strtolower($request->method()) == 'post') {
            try {
                $data = $request->param();
                // 校验
                if (empty($data['id'])) {
                    $name = '新增菜单';
                    $code = '1002002';
                    $valiMsg = $this->validate($data, 'Menus.insert');
                } else {
                    $name = '编辑菜单';
                    $code = '1002003';
                    $valiMsg = $this->validate($data, 'Menus.update');
                }
                if ($valiMsg !== true) {
                    throw new \Exception($valiMsg);
                }
                // 控制下level
                // 获取父级level
                if($data['pid'] > 0){
                    $parentLevel = model("Menus")::where(['id'=>$data['pid'],'deleted_at'=>null])->column('level');
                    $data['level'] = ++$parentLevel[0];
                }
                $result = model('Menus')->saveData($data,'Menus',$name, $code);
                if ($result['code'] == 0) {
                    throw new \Exception($result['msg']);
                }
                return $result;
            } catch (\Exception $e) {
                mdLog($e);
                return ['code' => 0, 'msg' => $e->getMessage()];
            }
        } else {
            return ['code' => 0, 'msg' => '非法请求' . $request->method()];
        }
    }

    public function deleteMenu(request $request)
    {
        try {
            $data = $request->only('id');
            if (empty($data['id'])) {
                throw new \think\Exception('数据不存在');
            }
            $list = model('Menus')->getCommonCollection(['pid'=>$data['id']],'id');
            if($list['code'] == 0){
                throw new \think\Exception('父级菜单出错');
            }
            if(!empty($list['data'])){
                throw new \think\Exception('存在子菜单,不可删除');
            }
            $result = model("Menus")->deleteData($data['id'], 'Menus','删除菜单', '1002001');
            if ($result['code'] == 0) {
                throw new \think\Exception('删除失败' . $result['msg']);
            }
            return ['code' => 1, 'msg' => '菜单删除成功', 'data' => $data['id']];
        } catch (\Exception $e) {
            mdLog($e);
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }

    public function getRoles()
    {
        $condition = [];
        // 加盟商超管,只获取其他所有及角色
        if($this->roleId != 1){
            $condition['a.top'] = $this->roleId;
        }
        $list = model('Roles')->getBootstrapeTable($condition, 'a.id,a.pid,a.top,a.level,a.name,a.sort,a.menu_ids,a.created_at,b.nick_name as userName', [['user b','a.created_user=b.id and b.deleted_at is null','left']], 'a.sort desc');
        return $list;
    }

    public function getRoleRow(request $request)
    {
        $id = $request->only('id');
        if (!empty($id)) {
            $result = model('Roles')::get($id);
            return ['code'=>1,'msg'=>'','data'=>$result];
        }
        return ['code'=>0,'msg'=>'用户组数据不存在'];
    }

    public function saveRole()
    {
        try{
            $data = $this->request->post();
            // 校验
            if (empty($data['id'])) {
                $name = '新增角色';
                $code = '1002004';
                $valiMsg = $this->validate($data, 'Roles.insert');
            } else {
                $name = '编辑角色';
                $code = '1002005';
                $valiMsg = $this->validate($data, 'Roles.update');
            }
            if ($valiMsg !== true) {
                throw new \Exception($valiMsg);
            }
            // 控制下level 以及获取pid  top
            if(!empty($data['morelevel'])){
                $level = explode(',',$data['morelevel']);
                $levelLength = count($level);
                $data['franchisee'] = $level[0];
                if($levelLength > 1){
                    $data['top'] = $level[$levelLength-2];
                }else{
                    $data['top'] = $level[0];
                }
                $data['pid'] = end($level);
                $data['level'] = $levelLength;
                unset($data['morelevel']);
            }
            $result = model('Roles')->saveData($data,'Roles',$name, $code);
            if ($result['code'] == 0) {
                throw new \Exception($result['msg']);
            }
            return $result;
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    public function deleteRole(request $request)
    {
        try {
            $data = $request->only('id');
            if (empty($data['id'])) {
                throw new \think\Exception('数据不存在');
            }
            $list = model('Roles')->getCommonCollection(['pid'=>$data['id']],'id');
            if($list['code'] == 0){
                throw new \think\Exception('父级菜单出错');
            }
            if(!empty($list['data'])){
                throw new \think\Exception('存在子角色,不可删除');
            }
            $result = model("Roles")->deleteData($data['id'], 'Roles','删除角色', '1002004');
            if ($result['code'] == 0) {
                throw new \think\Exception('删除失败' . $result['msg']);
            }
            return ['code' => 1, 'msg' => '角色删除成功', 'data' => $data['id']];
        } catch (\Exception $e) {
            mdLog($e);
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }
}