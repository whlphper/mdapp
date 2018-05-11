<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 11:18
 * Desc:
 */
namespace app\iubo\controller;
use app\common\controller\Base;

class Accountrelationship extends Base{

    protected $beforeActionList = [
        //'first',                                //在执行所有方法前都会执行first方法
        //'second' => ['except' => 'hello'],       //除hello方法外的方法执行前都要先执行second方法
        'getBelongList' => ['only' => 'index,store'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Mam';
        $this->theme = '账户关系表';
        $this->order = '';
        $this->field = 'a.*';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }



    public function getBelongList()
    {
        if(!empty($id = $this->request->param('id'))){
            $data = model('Mam')->get($id)->toArray();
            $this->assign('data',$data);
            $this->assign('fromUri',url('iubo/Accountrelationship/upAccountrelationship'));
        }else{
            $this->assign('fromUri',url('/iubo/Accountrelationship/fullyStore'));
        }
        // 获取账号列表,用做搜索条件
        $list = model('Mam')->getBelongList();
        //$accList = model('Mam')->getAccList();
        $this->assign('belongList',$list);
        //$this->assign('accList',$accList);
    }

    public function upAccountrelationship()
    {
        $data = $this->request->post();
        return $this->model->updated($data,['autoid'=>$data['autoid']],$this->theme);
    }


}