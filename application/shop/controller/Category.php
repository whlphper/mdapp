<?php
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 9:44
 * Comment:  商城模块-商品分类
 */
class Category extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Category';
        $this->theme = '分类';
        $this->order = 'a.level asc,a.sort desc';
        $this->field = 'a.id,a.poster,a.name,a.desc,a.pid,a.top,a.poster,a.sort,a.isNav,a.status,a.created_at,a.created_user,b.name as pName,c.savePath as posterPath';
        $this->join = [['Category b','a.pid=b.id','left'],['File c','a.poster=c.id','left']];
        $this->model = model($this->modelName);
    }
}