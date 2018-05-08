<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 10:56
 * Desc:
 */
namespace app\backiubo\controller;
use app\common\controller\Base;

class Orderrecord extends Base{

    protected $beforeActionList = [
        //'first',                                //在执行所有方法前都会执行first方法
        //'second' => ['except' => 'hello'],       //除hello方法外的方法执行前都要先执行second方法
        'getSearchList' => ['only' => 'index,store'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Record';
        $this->theme = '订单匹配记录';
        $this->order = '';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }



    public function getSearchList()
    {
        if(!empty($id = $this->request->param('id'))){
            $data = model('Record')->get($id)->toArray();
            $this->assign('data',$data);
            $this->assign('fromUri',url('/backiubo/Orderrecord/upRecord'));
        }else{
            $this->assign('fromUri',url('/backiubo/Orderrecord/fullyStore'));
        }
        $list = model('Record')->getAccountList();
        $this->assign('accList',$list);
        $list = model('Record')->getOrderList();
        $this->assign('orderList',$list);
        $list = model('Record')->getSymbolList();
        $this->assign('symbolList',$list);
    }

    public function upRecord()
    {
        $data = $this->request->post();
        return $this->model->updated($data,['ticket'=>$data['ticket']],$this->theme);
    }
}