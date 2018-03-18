<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Request;
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
        $list = model('Menus')->getBootstrapeTable([],'a.id,a.url,a.name,a.flag,a.pid,a.sort',[],'a.sort desc');
        return $list;
    }
}