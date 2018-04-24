<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/24 0024
 * Time: 9:19
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;
use unionpay\Unionpay;
use think\Log;

class Pay extends Base
{
    public function unionPay($orderId)
    {
        try{
            if(empty($orderId)){
                throw new \Exception('订单数据不存在');
            }
            $orderInfo = model('Order')->getRow(['id'=>$orderId],'a.id,a.tradeNumber,a.payType,a.userId,a.product,a.desc,a.total,a.status,a.progress');
            if($orderInfo['code'] == 0){
                throw new \Exception('订单获取失败'.$orderInfo['msg']);
            }
            $unionPay = new Unionpay();
            $form = $unionPay->get_code($orderInfo['data']);
            Log::notice('银联支付表单信息'.$form);
            return ['code'=>1,'msg'=>'','data'=>$form];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}