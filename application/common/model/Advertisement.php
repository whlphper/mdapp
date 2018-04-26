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

    public function getPositionAttr($value)
    {
        //1首页2商品详情页3分类页
        $status = [1=>'首页',2=>'商品详情页',3=>'分类页'];
        return $status[$value];
    }

    public function getTypeAttr($value)
    {
        //1站内商品2站内分类3站内新闻4外部链接
        $status = [1=>'站内商品',2=>'站内分类',3=>'站内新闻',4=>'外部链接'];
        return $status[$value];
    }

    public function getIsBlankAttr($value)
    {
        //是否新标签打开-1是0否
        $status = [1=>'是',0=>'否'];
        return $status[$value];
    }


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