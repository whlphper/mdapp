<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 10:10
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class SpecDetail extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'SpecDetail';
        $this->theme = '规格值';
        $this->order = 'a.created_at desc';
        $this->field = 'a.id,a.specId,a.value,a.created_at';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme','规格值');
    }

    /**
     * 获取规格值
     * @param $id
     * @return array
     */
    public function getSpecDetail($id)
    {
        try{
            if(empty($id)){
                throw new \Exception('规格不存在');
            }
            return $this->model->getDetailView($id);
        }catch (\Exception $e)
        {
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }

    }
}