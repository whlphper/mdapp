<?php
namespace app\common\model;
use app\common\model\Base;
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 9:53
 * Comment: 商品分类模型
 */
class Category extends Base{

    public $proField = 'a.id,a.name,a.poster,b.savePath as posterPath';
    public $proJoin = [['File b','a.poster=b.id','left']];
    public $proOrder= 'a.sort desc';

    /**
     * 商城首页导航以及左侧商品分类
     * @return mixed
     */
    public function getCateNavTree()
    {
        // 分类数据 导航数据
        $cateList = $this->getDataList(['a.status'=>1], 'a.id,a.name,a.pid,a.top,a.level,a.poster,a.isNav,a.desc,b.savePath as posterPath', $this->proJoin, $this->proOrder);
        if($cateList['code'] == 0){
            return $cateList;
        }
        $cateList = $cateList['data'];
        // 分类tree start
        $cateTree = list_to_tree($cateList,'id','pid','subCategory');
        $result['cateTree'] =  $cateTree;
        // 分类tree end
        // 获取导航显示的分类 start
        $navBlock = [];
        foreach($cateList as $k=>$v){
            if($v['isNav'] == 1){
                $cur['id'] = $v['id'];
                $cur['name'] = $v['name'];
                $navBlock[] = $cur;
            }
        }
        // 获取导航显示的分类 end
        $result['navBlock'] =  $navBlock;
        return $result;
    }

    /**
     * 获取主分类
     * @param bool $relation  true 代表是否获取关联的商品
     * @return array
     */
    public function getCategoryProduct($relation=false)
    {
        $cateList = $this->getDataList(['a.level'=>1], $this->proField, $this->proJoin, $this->proOrder);
        if(!$relation){
            return $cateList;
        }
        $cateList = $cateList['data'];
        // 获取主分类下的商品 限制为4个
        foreach($cateList as $k=>$v){
            // 此主分类下的子节点
            $childId = $this->getColumn(['pid'=>$v['id']],'id');
            $pidArr = $childId['data'];
            // 查询商品
            $proList = model('Product')->getDataList(['a.categoryId'=>['in',$pidArr],'a.status'=>1], 'a.id,a.name,a.shopPrice,a.marketPrice,b.savePath as album', [['File b','a.album=b.id','left']],'a.sort desc',4);
            $cateList[$k]['products'] = $proList['data'];
        }
        return $cateList;
    }
}