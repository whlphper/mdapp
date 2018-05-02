<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 13:06
 * Comment:
 */
namespace app\shop\controller;
use app\common\controller\Base;
use think\Db;
use think\Request;
class Product extends Base{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Product';
        $this->theme = '商品';
        $this->order = 'a.sort desc,a.created_at desc';
        $this->field = 'a.*,b.savePath as albumPath';
        $this->rowField = 'a.*';
        $this->join = [['File b','a.album=b.id','left']];
        $this->rowJoin = [];
        $this->model = model($this->modelName);
    }

    public function storeProduct($id=0)
    {
        $specList = model('Spec')->getAllDetail();
        if($id){
            $skuInfo = $this->model->where('id',$id)->value('skuId');
            $sku = explode(",",$skuInfo);
        }
        return view('',['specList'=>$specList['data'],'skuList'=>isset($sku) ? $sku : []]);
    }

    public function saveToTable2()
    {
        try{
            $data = input("post.");
            // SKU数据信息
            $skuInfo = isset($data['extraData']) ? $data['extraData'] : [];
            // Spec表中的ID集
            $attrId = [];
            // 对应Spec的规格值表spec_detail  ID
            $attrVal = [];
            foreach ($skuInfo as $k=>$v){
                $attrId[] = $v['id'];
                $attrVal[] = $v['val'];
            }
            $skuId = [];
            foreach($attrVal as $k=>$v){
                $skuId = array_merge($v,$skuId);
            }
            $data['skuId'] = implode(',',$skuId);
            if(empty($data['id'])){
                // 做校验
                $valiMsg = $this->validate($data, 'Product.insert');
                if($valiMsg !== true){
                    throw new \Exception($valiMsg);
                }
            }
            if(isset($data['extraData'])){
                unset($data['extraData']);
                unset($data['shopPrice']);
                unset($data['marketPrice']);
                unset($data['stock']);
            }
            return $this->model->addProduct($data,$attrId,$attrVal);
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }


}