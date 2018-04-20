<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 11:23
 * Comment: 商城模块-品牌
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Brand extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Brand';
        $this->theme = '品牌';
        $this->order = 'a.sort desc';
        $this->field = 'a.id,a.poster,a.name,a.desc,a.poster,b.savePath as posterPath';
        $this->join = [['File b','a.poster=b.id','left']];
        $this->model = model($this->modelName);
    }


}