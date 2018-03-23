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
    // 菜单列表
    public function getMenus(request $request)
    {
        $list = model('Menus')->getBootstrapeTable([], 'a.id,a.url,a.name,a.flag,a.pid,a.sort', [], 'a.sort desc');
        return $list;
    }

    public function storeMenu(request $request)
    {
        $id = $request->only('id');
        if (!empty($id)) {
            $current = model('Menus')::get($id);
        }
        return view('', ['current' => isset($current) ? $current : []]);
    }

    public function saveMenu(request $request)
    {
        if (strtolower($request->method()) == 'post') {
            try {
                $data = $request->param();
                if (isset($data['old']) && isset($data['new'])) {
                    $old = $data['old'];
                    $new = $data['new'];
                    // 要unset掉
                    unset($data['old']);
                    unset($data['new']);
                } else {
                    $old = [];
                    $new = [];
                }
                // 校验
                if (empty($data['id'])) {
                    $name = '新增菜单';
                    $code = '1002002';
                    $valiMsg = $this->validate($data, 'Menus.insert');
                    if ($valiMsg !== true) {
                        throw new \Exception($valiMsg);
                    }
                } else {
                    $name = '编辑菜单';
                    $code = '1002003';
                    $valiMsg = $this->validate($data, 'Menus.update');
                    if ($valiMsg !== true) {
                        throw new \Exception($valiMsg);
                    }
                }
                // 控制下level
                // 获取父级level
                $parentLevel = model("Menus")::where('id', $data['pid'])->column('level');
                $data['level'] = ++$parentLevel[0];
                $result = model('Menus')->saveData($data, $name, $code, $old, $new);
                if ($result['code'] == 0) {
                    throw new \Exception($result['msg']);
                }
                return $result;
            } catch (\Exception $e) {
                return ['code' => 0, 'msg' => $e->getMessage()];
            };
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
            $result = model("Menus")->deleteData($data['id'], '删除菜单', '1002001');
            if ($result['code'] == 0) {
                throw new \think\Exception('删除失败' . $result['msg']);
            }
            return ['code' => 1, 'msg' => '菜单删除成功', 'data' => $data['id']];
        } catch (\Exception $e) {
            mdLog($e);
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => []];
        }
    }
}