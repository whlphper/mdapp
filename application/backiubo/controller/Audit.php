<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 9:01
 * Desc:
 */
namespace app\backiubo\controller;
use app\common\controller\Base;

class Audit extends Base{

    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Task';
        $this->theme = '审核';
        $this->order = 'a.autoid desc';
        $this->field = 'a.account,a.autoid,a.belongto,a.multiple,a.password,a.contact,a.email,a.idcard,a.allow,a.reason';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }


    public function goAudit($id,$status,$reason='')
    {
        try{
            if(empty($id)){
                throw new \Exception('数据不存在');
            }
            if($status != 1){
                $status = null;
            }
            $this->model->upAllowStatus($id,$status,$reason);
            return ['code'=>1,'msg'=>'操作成功'];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

    }
}