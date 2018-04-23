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

    /*public function getStatusAttr($value)
    {
        $status = [0=>'代付款',1=>'付款成功'];
        return $status[$value];
    }*/

    public function getProgressAttr($value)
    {
        // 0未支付1代发货2已经发货3订单完成4取消订单
        $status = [0=>'未支付',1=>'代发货',2=>'已经发货',3=>'订单完成','4'=>'取消订单'];
        return $status[$value];
    }
}