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

    public $proField = 'a.id,a.name,a.poster,a.level,b.savePath as posterPath';
    public $proJoin = [['File b','a.poster=b.id','left']];
    public $proOrder= 'a.sort desc';

    public function getStatusAttr($value)
    {
        $status = [0=>'关闭',1=>'开启'];
        return $status[$value];
    }

    public function getIsNavAttr($value)
    {
        $status = [0=>'非导航',1=>'导航显示'];
        return $status[$value];
    }


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
     * @param bool $relation 代表是否获取关联的商品
     * @param bool $cateId   代表是查询某个主分类下的商品
     * @return array
     */
    public function getCategoryProduct($relation=false,$cateId=false,$keyWord=false)
    {
        // 如果catedId为真,就代表是查询某个主分类下的商品  并且需要获取分页信息
        if($cateId && $relation){
            if($keyWord){
                $object  = model('Product')->alias('a')->where(['a.name'=>['like',"%".$keyWord."%"]])->field('a.id,a.name,a.shopPrice,a.marketPrice,b.savePath as album,a.categoryId')->order('a.sort desc')->join([['File b','a.album=b.id','left']])->paginate(16,false,[
                    'type'     => 'Pcshoppage',
                    'var_page' => 'p',
                ]);
            }else{
                $row = $this->getRow(['id'=>$cateId],'a.id,a.level,a.name');
                $row = $row['data'];
                if($row['level'] == 1){
                    // 此主分类下的子节点
                    $childId = $this->getColumn(['pid'=>$row['id']],'id');
                    $pidArr = $childId['data'];
                    // 查询商品
                    $object  = model('Product')->alias('a')->where(['a.categoryId'=>['in',$pidArr]])->field('a.id,a.name,a.shopPrice,a.marketPrice,b.savePath as album,a.categoryId')->order('a.sort desc')->join([['File b','a.album=b.id','left']])->paginate(16,false,[
                        'type'     => 'Pcshoppage',
                        'var_page' => 'p',
                    ]);
                }else{
                    $object  = model('Product')->alias('a')->where(['a.categoryId'=>$row['id']])->field('a.id,a.name,a.shopPrice,a.marketPrice,b.savePath as album,a.categoryId')->order('a.sort desc')->join([['File b','a.album=b.id','left']])->paginate(16,false,[
                        'type'     => 'Pcshoppage',
                        'var_page' => 'p',
                    ]);
                }
            }
            $return['data'] = $object->toArray();
            $return['page'] = $object->render();
            return $return;
        }
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