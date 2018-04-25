<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 13:33
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Product extends Base{

    public $proField = 'a.id,a.name,a.album,a.shopPrice,a.marketPrice,a.status,a.type,a.desc,a.stock,a.detail,a.brandId,b.savePath as albumPath';
    public $proJoin = [['File b','a.album=b.id','left']];
    public $proOrder= 'a.sort desc';

    public function getStatusAttr($value)
    {
        $status = [1=>'正常',2=>'下架'];
        return $status[$value];
    }

    public function getTypeAttr($value)
    {
        $status = [1=>'新品',2=>'热销',3=>'赠品',4=>'促销',5=>'团购'];
        return $status[$value];
    }

    public function getProductDetail($id=0)
    {
        // 获取基本信息
        $proInfo = $this->getRow(['a.id'=>$id],$this->proField,$this->proJoin);
        if($proInfo['code'] == 0){
            return ['code'=>0,'msg'=>$proInfo['msg']];
        }
        $proInfo = $proInfo['data'];
        $result['proInfo'] = $proInfo;
        // 获取此商品所属品牌
        $brandInfo = model('Brand')->getRow(['a.id'=>$proInfo['brandId']],'a.name,a.poster,a.desc,b.savePath as posterPath',[['File b','a.poster=b.id','left']],'a.sort desc');
        if($brandInfo['code'] == 0){
            return ['code'=>0,'msg'=>$brandInfo['msg']];
        }
        $result['brandInfo'] = $brandInfo['data'];
        // 获取对应品牌下的商品
        $brandProList = $this->getDataList(['brandId'=>$proInfo['brandId'],'a.id'=>['<>',$proInfo['id']]],'a.id,a.name,a.shopPrice,a.album,b.savePath as albumPath',$this->proJoin,$this->proOrder,4);
        if($brandProList['code'] == 0){
            return ['code'=>0,'msg'=>$brandProList['msg']];
        }
        $result['brandProList'] = $brandProList['data'];

        // 获取商城支付服务
        // 获取商品详情页面广告位信息
        $adList = model('Advertisement')->getAdByPosition(2);
        if($adList['code'] == 0){
            return ['code'=>0,'msg'=>$adList['msg']];
        }
        $result['adList'] = $adList['data'];
        // 获取商品全部分类 这个在base已经aiisgn过了
        // 获取商品排序后信息 --  1热卖（销量做多的） 2热评（评论最多的）
        // 获取此商品的评价信息
        $commentList  = model('Comment')->getDataList(['productId'=>$proInfo['id']],'a.*,b.nick_name,c.savePath as avatarPath',[['User b','a.created_user=b.id','left'],['File c','b.avatar=c.id','left']],'a.created_at desc',4);
        $result['commentList'] = $commentList['data'];
        // 获取此商品的咨询信息
        $consulation = model('Consulation')->getDataList(['productId'=>$proInfo['id']],'a.*',[],'a.created_at desc',4);
        $result['consulation'] = $consulation['data'];
        return $result;
    }

    /**
     * 获取销量最多
     * @param array $condition
     */
    public function getSaleList($condition=[])
    {

    }

    /**
     * 获取评论最多
     * @param array $condition
     */
    public function getCommentMaxList($condition=[])
    {

    }
}