<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 10:51
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Carts extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Carts';
        $this->theme = '购物车';
        $this->order = 'a.created_at desc';
        $this->field = 'a.*,b.name,c.savePath as albumPath';
        $this->join = [['Product b','a.productId=b.id','left'],['File c','b.album=c.id','left']];
        $this->model = model($this->modelName);
    }
}