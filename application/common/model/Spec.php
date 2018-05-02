<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 9:55
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Spec extends Base{

    public function getDetail($id)
    {
        $info = $this->getRow(['id'=>$id],'a.id,a.shopId,a.name,a.created_at');
        if($info['code'] == 0){
            return $info;
        }
        $valInfo = model('SpecDetail')->getDataList(['specId'=>$id],'a.specId,a.value,a.id');
        if($valInfo['code'] == 0){
            return $valInfo;
        }
        $info['data']['detail'] = $valInfo['data'];
        return $info;
    }

    public function getAllDetail()
    {
        $info = $this->getDataList([],'a.id,a.shopId,a.name,a.created_at');
        if($info['code'] == 0){
            return $info;
        }
        foreach($info['data'] as $k=>$v){
            $valInfo = model('SpecDetail')->getDataList(['specId'=>$v['id']],'a.specId,a.value,a.id');
            if($valInfo['code'] == 0){
                return $valInfo;
            }
            $info['data'][$k]['detail'] = $valInfo['data'];
        }
        return $info;
    }

    /**
     * 获取商品对应的SKU信息
     * @param $proId
     * @return mixed
     */
    public function getProSku($proId)
    {
        $list = model('ItemAttrVal')->getDataList(['item_id'=>$proId],'a.id,a.attr_key_id,a.attr_value,b.name as attr_key_name,c.value as attr_key_value',[['Spec b','a.attr_key_id=b.id','left'],['SpecDetail c','a.attr_value=c.id','left']]);
        return $list;
    }
}