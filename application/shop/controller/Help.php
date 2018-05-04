<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 11:15
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Help extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Catalog';
        $this->theme = '帮助信息';
        $this->order = 'a.sort_order desc';
        $this->field = 'a.id,a.parent_id,a.title,a.content,a.sort_order,a.created_at';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }
}