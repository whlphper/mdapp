<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 5:16
 * Desc:
 */
namespace app\weiubo\controller;
use app\common\controller\Base;

class Personal extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Accinfo';
        $this->theme = '个人中心';
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }


}