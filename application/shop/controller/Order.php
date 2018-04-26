<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/26 0026
 * Time: 9:42
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Order extends Base{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Order';
        $this->theme = '订单';
        $this->order = 'a.created_at desc';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
    }
}