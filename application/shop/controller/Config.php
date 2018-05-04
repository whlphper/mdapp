<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 10:12
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Config extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Config';
        $this->theme = '商城配置';
        $this->order = 'a.configId asc';
        $this->field = 'a.id,a.configId,a.name,a.value';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme','商城配置');
    }

    public function shop($configId=1)
    {
        $conf = $this->model->getConfigById($configId,false);
        return view('',['conf'=>$conf['data']]);
    }

    public function saveShopConf()
    {
        $data = $this->request->post();
        return $this->model->saveConf($data);
    }
}