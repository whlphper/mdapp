<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 15:50
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Product extends Base
{


    public function detail($id=0)
    {
        $result = model('Product')->getProductDetail($id);
        return view('',['pageInfo'=>$result]);
    }
}