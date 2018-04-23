<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 15:46
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Category extends Base
{

    /**
     * 获取某个分类下所有商品
     * @param int $id
     * @param bool $keyWord
     * @return \think\response\View
     */
    public function all($id=1,$keyWord=false)
    {
        $info = model('Category')->getCategoryProduct(true,$id,$keyWord);
        // 暂时先获取此分类下的商品
        return view('',['cateId'=>$id,'total'=>$info['data']['total'],'data'=>$info['data']['data'],'page'=>$info['page']]);
    }

}