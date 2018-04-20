<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 14:35
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Region extends Base{

    public function getRegion($condition=[])
    {
        $condition = empty($condition) ? ['parent_id'=>1] : $condition;
        return $this->getDataList($condition,'a.*',[],'a.id asc');
    }
}