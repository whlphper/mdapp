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

    protected $beforeActionList = [
        //'first',                                //在执行所有方法前都会执行first方法
        //'second' => ['except' => 'hello'],       //除hello方法外的方法执行前都要先执行second方法
        'getAccList' => ['only' => 'index,store'],
    ];

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

    public function getAccList()
    {
        if(!empty($id = $this->request->param('id'))){
            $data = model('History')->get($id)->toArray();
            $this->assign('data',$data);
            $this->assign('fromUri',url('backiubo/History/upHistory'));
        }else{
            $this->assign('fromUri',url('/backiubo/history/fullyStore'));
        }
        // 获取账号列表,用做搜索条件
        $list = model('History')->getAccountList();
        $this->assign('accList',$list);
    }

    public function upHistory()
    {
        $data = $this->request->post();
        return $this->model->updated($data,['ticket'=>$data['ticket']],$this->theme);
    }
}