<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 2:14
 * Desc:
 */
namespace app\weiubo\controller;
use app\common\controller\Base;

class Index extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Accinfo';
        $this->theme = '牛人榜';
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }

    public function index()
    {
        $strong = $this->model->listData();
        foreach($strong['data'] as $k=>$v){
            $headimg = model('Mam')->alias('a')->where('account',$v['account'])->field('b.headimgurl')->join([['Openid b','a.openid=b.openid','left']])->find();
            $strong['data'][$k]['headimgurl'] = $headimg['headimgurl'];
        }
        return view('',['strong'=>$strong['data']]);
    }
}