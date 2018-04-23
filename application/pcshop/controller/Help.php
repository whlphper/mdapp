<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 15:51
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Help extends Base
{
    public function index($id=55)
    {
        // 获取当前问题详情
        $detail = model('Catalog')->getColumn(['id'=>$id],'content');
        return view('',['curId'=>$id,'detail'=>$detail['data'][0]]);
    }
}