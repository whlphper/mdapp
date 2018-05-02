<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 9:54
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Spec extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Spec';
        $this->theme = '规格';
        $this->order = 'a.created_at desc';
        $this->field = 'a.id,a.shopId,a.name,a.created_at';
        $this->join = [];
        $this->model = model($this->modelName);
        $this->assign('theme','规格');
    }

    /**
     * 新增规格模板
     * @return array
     */
    public function insertSpec()
    {
        Db::startTrans();
        try{
            $data = input("post.",null);
            $spec['name'] = $data['name'];
            $spec['id'] = $data['id'];
            $specDetailId = explode(',',$data['specDetailId']);
            $specDetailVal = explode(',',$data['specValue']);
            if(!empty($data['id'])){
                $name = '修改规格';
                $code = 'editSpect';
            }else{
                $name = '新增规格';
                $code = 'insertSpect';
            }
            // 更新规格数据
            $sRes = $this->model->saveData($spec,'Spec',$name,$code);
            if($sRes['code'] == 0){
                //throw new \Exception($sRes['msg']);
            }
            $specDetailId = explode(',',$data['specDetailId']);
            $specDetailVal = explode(',',$data['specValue']);
            $spectArr = [];
            foreach($specDetailId as $k=>$v){
                $curDetail['value'] = $specDetailVal[$k];
                $curDetail['specId'] = $sRes['data'];
                $spectArr[] = $curDetail;
                // 更新规格值数据
                if(!empty($v)){
                    $curDetail['id'] = $v;
                    $name = '修改规格值';
                    $code = 'editSpectDetail';
                }else{
                    $name = '新增规格值';
                    $code = 'insertSpectDetail';
                }
                $dRes = model('SpecDetail')->saveData($curDetail,'SpecDetail',$name,$code);
                if($dRes['code'] == 0){
                    //throw new \Exception($dRes['msg']);
                }
            }
            Db::commit();
            return ['code'=>1,'msg'=>'规格模板设置成功'];
        }catch (\Exception $e){
            Db::rollback();
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    public function detail($id)
    {
        try{
            if(empty($id)){
                throw new \Exception('规格不存在');
            }
            return $this->model->getDetail($id);
        }catch (\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}