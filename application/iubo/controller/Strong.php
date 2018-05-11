<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 10:43
 * Desc:
 */
namespace app\iubo\controller;
use app\common\controller\Base;

class Strong extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Accinfo';
        $this->theme = '牛人账户';
        $this->order = '';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }

}