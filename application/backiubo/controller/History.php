<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 15:22
 * Comment:
 */
namespace app\backiubo\controller;
use app\common\controller\Base;

class History extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'History';
        $this->theme = '历史交易记录';
        $this->order = 'a.timeopen asc';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }

    public function getHistory()
    {
        $data = $this->model->getJQPage([],'a.*',[],'');
        return $data;
    }
}