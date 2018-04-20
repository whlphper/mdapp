<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 16:44
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Index extends Base
{
    public function index()
    {
        // 首页广告轮播 start
        $adList = model('Advertisement')->getAdByPosition(1);
        // 首页广告轮播 end

        // 获取分类以及对应商品 start
        $cateList = model('Category')->getCategoryProduct(true);
        // 获取分类以及对应商品 end

        return view('',['carousel'=>$adList['data'],'cateGoods'=>$cateList]);
    }
}