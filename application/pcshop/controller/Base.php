<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 16:38
 * Comment:
 */
namespace app\pcshop\controller;
use think\Controller;
use think\Request;
use think\Db;

class Base extends Controller
{
    // 商城前台用户控制流
    public function _initialize()
    {

    }

    public function _empty($name)
    {
        return view($name);
    }

    /**
     * 获取商城公共部分数据
     * 分类数据
     * 导航数据
     * SEO信息
     * 商城基本信息
     * 商城帮助数据
     */
    private function getCommonData()
    {
        // 分类数据 以及导航数据  因为现在导航都是显示的分类

    }
}