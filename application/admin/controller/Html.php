<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Cache;
class Html extends Controller
{
    public function changeLevel($table,$condition=[],$field,$order='')
    {
        $list = model($table)->getCommonCollection($condition,$field,$order);
        return $list;
    }
}
