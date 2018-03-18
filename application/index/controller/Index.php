<?php
namespace app\index\controller;
class Index
{
    public function index()
    {
        $menuInfo = model('Roles')->getMenuStr(1);
        dump($menuInfo);
        if(empty($menuInfo['menu_ids'])){
            echo 0;
        }
        dump(model('Menus')->getList('1,2,3'));
    }
}
