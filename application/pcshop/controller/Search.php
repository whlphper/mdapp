<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 15:45
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Search extends Base
{
    public function byKeyword($keyword='')
    {
        // 记录用户搜索记录
        $data['userId'] = session('pcshopUserId');
        $data['searchName'] = $keyword;
        $data['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
        if(!empty($keyword)){
            model('SearchHistory')->insert($data);
        }
        $this->redirect(url('/pcshop/Category/all',['id'=>false,'keyWord'=>$keyword]));
    }
}
