<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 13:06
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Product extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Product';
        $this->theme = '商品';
        $this->order = 'a.sort desc,a.created_at desc';
        $this->field = 'a.*,b.savePath as albumPath';
        $this->join = [['File b','a.album=b.id','left']];
        $this->model = model($this->modelName);
    }


}