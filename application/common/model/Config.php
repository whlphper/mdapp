<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 9:06
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Config extends Base{

    /**
     * 获取对应的配置参数
     * 配置类型-1pc端商城基本信息2支付宝支付3微信支付
     * @param int $id
     * @return array
     */
    public function getConfigById($id=1,$isRegular=true)
    {
        // 分类数据 导航数据
        $confList = $this->getDataList(['configId'=>$id], 'a.name,a.value,a.desc');
        if($confList['code'] == 0){
            return $confList;
        }

        $regular = [];
        foreach($confList['data'] as $k=>$v){
            if($v['name'] == 'siteLogo'){
                $logoPath = model('File')->where('id',$v['value'])->column('savePath');
                $regular[$v['name']] = $logoPath[0];
            }else{
                $regular[$v['name']] = $v['value'];
            }
        }
        if(!$isRegular){
            return $confList;
        }
        return $regular;
    }

    public function saveConf($data)
    {
        $this->startTrans();
        try{
            if(empty($data)){
                throw new \Exception('没有可以更新的数据');
            }
            foreach ($data as $k=>$v){
                $upData['value'] = $v;
                $con['name'] = $k;
                $res = $this->saveByCondition($upData,$con);
            }
            $this->commit();
            return ['code'=>1,'msg'=>'配置更新成功'];
        }catch (\Exception $e){
            $this->rollback();
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}