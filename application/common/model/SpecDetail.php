<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 10:13
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class SpecDetail extends Base{

    /**
     * 获取规格对应的值
     * @param $specId
     * @return array
     */
    public function getDetailView($specId)
    {
        $con['specId'] = $specId;
        return $this->getDataList($con,'a.specId,a.id,a.value');
    }
}