<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 13:35
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Address extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Address';
        $this->theme = '收货地址';
        $this->order = 'a.isDefault desc,a.created_at desc';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
    }
}