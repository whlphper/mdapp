<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 10:56
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Carts extends Base{

    public function getPcShopCarts($con)
    {
        $field = 'a.id,a.userId,a.productId,a.number,b.id as proId,b.name,b.keyword,b.album,b.shopPrice,b.marketPrice,c.savePath as albumPath';
        $join = [['Product b','a.productId=b.id','left'],['File c','b.album=c.id','left']];
        $order = 'a.created_at desc';
        $data = $this->getDataList($con,$field,$join,$order);
        return $data;
    }
}