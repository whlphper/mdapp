<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 12:47
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Order extends Base{

    public function getStatusAttr($value)
    {
        $status = [0=>'待付款',1=>'付款成功'];
        return $status[$value];
    }

    public function getProgressAttr($value)
    {
        // 0未支付1代发货2已经发货3订单完成4取消订单
        $status = [0=>'未支付',1=>'待发货',2=>'已经发货',3=>'订单完成','4'=>'取消订单'];
        return $status[$value];
    }

    public function getPayTypeAttr($value)
    {
        $status = [1=>'支付宝',2=>'微信',3=>'银联支付',4=>'环迅支付'];
        return $status[$value];
    }


    /**
     * 支付成功后的回调       修改订单支付状态以及物流状态- 以及商品库存的减少以及商品销量的增加
     * @param $tradeNumber  订单号
     * @param int $progress 订单发货状态
     * @return array|null|static
     * @throws \think\exception\PDOException
     */
    public function orderNoytify($tradeNumber,$progress=1)
    {
        $this->startTrans();
        try{
            $curOrder = $this->getRow(['tradeNumber'=>$tradeNumber],'a.id,a.product,a.desc,a.total,a.created_at,a.tradeNumber');
            if($curOrder['code'] == 0){
                throw new \Exception('订单号不存在'.$curOrder['msg']);
            }
            $data['id'] = $curOrder['data']['id'];
            $data['status'] = 1;
            $data['progress'] = $progress;
            $orderRes = $this->saveData($data,'Order','订单支付成功','666');
            if($orderRes['code'] == 0){
                throw new \Exception('订单状态修改失败'.$orderRes['msg']);
            }
            // 获取买到的商品信息
            $proList = explode('|',$curOrder['data']['product']);
            foreach($proList as $k=>$v){
                $idNumber = explode(':',$v);
                // 商品ID
                $proId = $idNumber[0];
                // 商品数量
                $proNum= $idNumber[1];
                // 减少库存  增加销量
                $incSales = model('Product')->where('id',$proId)->setInc('sales',$proNum);
                if(!$incSales){
                    throw new \Exception('商品销量+失败');
                }
                $decStock = model('Product')->where('id',$proId)->setDec('stock',$proNum);
                if(!$decStock){
                    throw new \Exception('商品库存-失败');
                }
            }
            $this->commit();
            return $curOrder;
        }catch(\Exception $e){
            $this->rollback();
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}