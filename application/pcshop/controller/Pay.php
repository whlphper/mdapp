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
use cbpay\Pay as cbPay;
use think\Log;

class Pay extends Base
{
    /**
     * 银联支付
     * @param $orderId
     * @return array|\think\response\Json
     */
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
            if($orderInfo['data']['status'] == 1){
                throw new \Exception('此订单已经交易成功了');
            }
            $unionPay = new Unionpay();
            $form = $unionPay->get_code($orderInfo['data']);
            Log::notice('银联支付表单信息'.$form);
            return ['code'=>1,'msg'=>'','data'=>$form];
        }catch(\Exception $e){
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 银联支付异步回调
     */
    public function unionpayNoyify()
    {
        try{
            $unionpay = new Unionpay();
            $notifyRes = $unionpay->respond();
            if($notifyRes['code'] == 0)
            {
                throw new \Exception($notifyRes['msg']);
            }
            file_put_contents("notifyRes.txt",json_encode($notifyRes));
            $tradeNumber = $notifyRes['data'];
            // 订单金额
            $dealFee = $notifyRes['total'];
            // 判断订单是否已经支付成功了
            $sucOrder = model('Order')->getRow(['tradeNumber'=>$tradeNumber],'a.id,a.tradeNumber,a.status,a.total');
            file_put_contents("orderInfo.txt",json_encode($sucOrder));
            if($sucOrder['code'] == 0){
                throw new \Exception('订单不存在');
            }
            if($sucOrder['data']['total'] != $dealFee){
                throw new \Exception('订单金额不一致');
            }
            if($sucOrder['data']['status'] == 1){
                echo 'notify_success';
                file_put_contents("unipayAlreadySuccess.txt",'已经修改了状态');
                exit;
            }
            // 修改订单状态
            $result = model('Order')->orderNoytify($tradeNumber,1);
            if($result['code'] == 0){
                throw new \Exception('订单状态修改失败'.$result['msg']);
            }
            echo 'notify_success';
            file_put_contents("unipaySuccess.txt",json_encode($result));
            exit;
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }

    public function cbPayForm()
    {
        return view();
    }

    /**
     * 国银网关支付
     * @return array
     */
    public function cbPay(Request $request)
    {
        try{
            /***************************  对方的文档 */
            $md5Key = '386125028475603';
            $data = $request->only(['pickupUrl','receiveUrl','signType','orderNo','orderAmount','orderCurrency','customerId','sign']);
            if(empty($data['orderNo'])){
                throw new \Exception('请输入订单编号');
            }
            if(empty($data['orderAmount'])){
                throw new \Exception('请输入订单金额');
            }
            $trueSign = md5($data['pickupUrl'].$data['receiveUrl'].$data['signType'].$data['orderNo'].$data['orderAmount'].$data['orderCurrency'].$data['customerId'].$md5Key);
            if($trueSign != $md5Key){
                // throw new \Exception('签名校验失败');
            }
            // 插入crm过来订单
            $oldOrder = model('Crmorder')->getRow(['orderNo'=>$data['orderNo']],'a.id,a.orderNo');
            if($oldOrder['code'] == 0){
                $orderRes = model('Crmorder')->saveData($data,'Crmorder','CRM订单','0424');
                if($orderRes['code'] == 0){
                    throw new \Exception('订单保存失败'.$orderRes['msg']);
                }
            }
            $order['tradeSn'] = $data['orderNo'];
            $order['orderAmount'] = $data['orderAmount']*100;
            $order['channelType'] = '1';
            $order['bankSegment'] = '1004';
            $order['receiveUrl'] = $data['receiveUrl'];
            $order['pickupUrl'] = $data['pickupUrl'];
            $cbPay = new cbPay();
            $result = $cbPay->applyPay($order);
            echo $result;
        }catch(\Exception $e){
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 国银网关支付回调
     */
    public function cbPayNotify()
    {
        $cbPay = new cbPay();
        $res = $cbPay->noticeResult();
    }

}