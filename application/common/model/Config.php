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

    public function getConfigById($id=1)
    {
        // 分类数据 导航数据
        $confList = $this->getDataList(['configId'=>$id], 'a.name,a.value');
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
        return $regular;
    }
}