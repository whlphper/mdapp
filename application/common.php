<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 异常记录log
function ecpLog($e)
{
    $err = [
        'code' => $e->getCode(),
        'msg'  => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'   => $e->getLine()
    ];
    \think\Log::notice($err);
}

//找子孙树
//用静态变量
function subtree($arr,$pField,$id,$lev=1){
    static $subs=array();  //static 只初始化一次
    foreach($arr as $v){
        if($v[$pField]==$id){
            $v['lev']=$lev;
            $subs[]=$v;
            subtree($arr,$pField,$v['id'],$lev+1);
            //不用静态变量
            //$subs=array_merge($subs,subtree($arr,$v['id'],$lev+1));
        }
    }
    return $subs;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root =
0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}


