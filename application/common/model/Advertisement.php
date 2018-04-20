<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 10:36
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Advertisement extends Base{

    public $adOrder = 'a.sort desc';
    public $adField = 'a.id,a.poster,a.name,a.url,a.type,a.position,a.sort,a.isBlank,b.savePath as posterPath,a.created_at';
    public $adJoin = [['File b','a.poster=b.id','left']];

    /**
     * 获取位置对应广告位数据
     * 1首页2商品详情页3分类页
     * @param int $position
     * @return array
     */
    public function getAdByPosition($position=1)
    {
        return $this->getDataList(['a.position'=>$position], $this->adField, $this->adJoin, $this->adOrder);
    }

}