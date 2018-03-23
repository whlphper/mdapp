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
function mdLog($e)
{
    $msg = '';
    if (!empty($e)) {
        $msg = "\r\n时间：" . date("Y-m-d H:i:s", time()) . "\r\n文件：" . $e->getFile() . "\r\n行-" . $e->getLine() . ":" . $e->getMessage();
    }
    if (!empty($msg) && preg_match('/[\x{4e00}-\x{9fa5}]/u', $msg) > 0) {
        trace($msg, 'notice');
    } else {
        trace($msg, 'error');
    }
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

/**
 * 递归实现迪卡尔乘积
 * @param $arr 操作的数组
 * @param array $tmp
 * @return array
 */
function descarte($arr, $tmp = array())
{
    $n_arr = array();
    foreach (array_shift($arr) as $v) {
        $tmp[] = $v;
        if ($arr) {
            descarte($arr, $tmp);
        } else {
            $n_arr[] = $tmp;
        }
        array_pop($tmp);
    }
    return $n_arr;
}

function upload($folder = "", $extType = "image", $defaultSize = 15)
{
    $folder = empty($folder) ? 'default' : $folder;
    $accept = array();
    switch ($extType) {
        case 'file':
            $accept = array("xlsx", "docx", "rar", "zip", "doc", "xls", "txt", "ppt", "pptx",
                "pdf");
            break;
        default:
            $accept = array('jpg', 'jpeg', 'png', 'gif');
    }
    $result = array();
    $files = request()->file();
    $filesNumber = count($files);
    if ($filesNumber > 0) {
        foreach ($files as $key => $file) {
            $sysFileData = array();
            $fileInfo = $file->getInfo();
            $fileName = $fileInfo['name'];
            $fileSize = $fileInfo['size'];
            if ($fileSize > $defaultSize * 1024 * 1024) {
                return array('code' => 0, 'msg' => '文件大小超过' . $defaultSize . 'M');
            }
            if (strpos($fileName, '.') !== false) {
                $ext = substr($fileName, strrpos($fileName, '.') + 1);
            } else {
                $ext = '';
            }
            $fileSize = $fileSize / (1024 * 1024);
            if (!in_array(strtolower($ext), $accept)) {
                return array('code' => 0, 'msg' => '文件后缀只允许' . implode(',', $accept) . '格式');
            }
            $savePath = $folder;
            $saveName = date('YmdHis', time()) . rand(1, 9999999) . "." . $ext;
            $moveInfo = $file->move(ROOT_PATH . 'public/uploads/' . $savePath, $saveName);
            $fullPath = $moveInfo->getPathName();
            if ($moveInfo !== false) {
                $sysFileData['fileName'] = $fileName;
                $sysFileData['savePath'] = '/uploads/' . $savePath . '/' . $saveName;
                $sysFileData['saveName'] = $saveName;
                $sysFileData['exts'] = $ext;
                $sysFileData['size'] = $fileSize;
                $sysFileData['adduserId'] = session('userId') ? session('userId') : 0;
                $sysFileData['addtime'] = date('Y-m-d H:i:s', time());
                $last = saveData('SysFile', $sysFileData);
                if ($last['data']) {
                    $result[] = $last['data'];
                }
            } else {
                return array('code' => 0, 'msg' => $file->getError());
            }
        }
    } else {
        return array('code' => 0, 'msg' => '没有文件可上传');
    }
    return array('code' => 1, 'msg' => '文件上传成功', 'data' => implode(',', $result));
}

//获取某个分类的所有子分类
function getSubs($categorys,$catId=0,$level=1,$filed){
    $subs=array();
    foreach($categorys as $item){
        if($item[$filed]==$catId){
            $item['name'] = '|'.str_repeat('——',$item['level']).$item['name'];
            $item['level']=$level;
            $subs[]=$item;
            $subs=array_merge($subs,getSubs($categorys,$item['id'],$level+1,$filed));
        }
    }
    return $subs;
}

