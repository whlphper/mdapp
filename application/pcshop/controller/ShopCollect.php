<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 10:48
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class ShopCollect extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'ShopCollect';
        $this->theme = '用户收藏';
        $this->order = 'a.created_at desc';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
    }
}