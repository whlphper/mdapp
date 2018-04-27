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
use ipsPay\Ips;
use ipsPay\IpsPayNotify;

class Pay extends Base
{
    /**
     * 银联支付 - pcshop
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
            $domain = Request::instance()->domain();
            $unionPay = new Unionpay();
            $orderInfo['data']['tradeSn'] = $orderInfo['data']['tradeNumber'];
            $orderInfo['data']['orderAmount'] = $orderInfo['data']['total'];
            $orderInfo['data']['pickupUrl'] = $domain.url('pcshop/Order/orderSuccess');
            $orderInfo['data']['notifyUrl'] = $domain.url('pcshop/Pay/unionpayNoyify');
            $form = $unionPay->get_code($orderInfo['data']);
            Log::notice('银联支付表单信息'.$form);
            return ['code'=>1,'msg'=>'','data'=>$form];
        }catch(\Exception $e){
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 银联支付异步回调 - pcshop
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


    /**
     * 银联支付 - LW
     * @param Request $request
     * @return \think\response\Json
     */
    public function lwUnionPay(Request $request)
    {
        try{
            /***************************  对方的文档 */
            $md5Key = '098756421346975';
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
            $oldOrder = model('Crmorderunion')->getRow(['orderNo'=>$data['orderNo']],'a.id,a.orderNo');
            if($oldOrder['code'] == 0){
                $data['transactionId'] = $this->getTransId();
                $orderRes = model('Crmorderunion')->saveData($data,'Crmorderunion','CRM订单-inonpay','0425');
                if($orderRes['code'] == 0){
                    throw new \Exception('订单保存失败'.$orderRes['msg']);
                }
            }
            $domain = Request::instance()->domain();
            $order['tradeSn'] = $data['orderNo'];
            $order['orderAmount'] = $data['orderAmount'];
            $order['desc'] = '商品';
            $order['pickupUrl'] = 'http://cn.unionpay.com/';//$data['pickupUrl'];
            $order['notifyUrl'] = $domain.url('pcshop/Pay/lwUnionpayNoyify');
            $unionPay = new Unionpay();
            $form = $unionPay->get_code($order);
            Log::notice('银联支付表单信息'.$form);
            echo $form;
        }catch(\Exception $e){
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 由于联行支付没有返回第三方流水号,所以在这里自己生成
     * @return string
     */
    public function getTransId()
    {
        //订购日期
        $order_date = date('Y-m-d');
        //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
        $order_id_main = date('YmdHis') . rand(10000000,99999999);
        //订单号码主体长度
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for($i=0; $i<$order_id_len; $i++){
            $order_id_sum += (int)(substr($order_id_main,$i,1));
        }
        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
        $old = model('Crmorderunion')->getRow(['transactionId'=>$order_id],'a.id');
        if($old['code'] == 1){
            $this->getTransId();
        }
        return $order_id;
    }

    /**
     * 银联支付异步回调 - LW
     */
    public function lwUnionpayNoyify()
    {
        try{
            $unionpay = new Unionpay();
            $notifyRes = $unionpay->respond();
            if($notifyRes['code'] == 0)
            {
                throw new \Exception($notifyRes['msg']);
            }
            Log::notice('联行支付回调检查结果'.json_encode($notifyRes));
            $tradeNumber = $notifyRes['data'];
            // 订单金额
            $dealFee = $notifyRes['total'];
            // 判断订单是否已经支付成功了
            $sucOrder = model('Crmorderunion')->getRow(['orderNo'=>$tradeNumber],'a.*');
            Log::notice('LW订单信息'.json_encode($sucOrder));
            if($sucOrder['code'] == 0){
                throw new \Exception('订单不存在');
            }
            if($sucOrder['data']['orderAmount'] != $dealFee){
                throw new \Exception('订单金额不一致');
            }
            echo 'notify_success';
            Log::notice('联行支付回调成功');
            $orderInfo = $sucOrder['data'];
            // 判断LeanWork是否处理成功了该笔订单
            if($orderInfo['status'] != 'success'){
                // LW md5key
                $md5Key = '098756421346975';
                $notifyUrl = $orderInfo['receiveUrl'];
                $trueSign = md5($orderInfo['signType'].$orderInfo['orderNo'].$orderInfo['orderAmount'].$orderInfo['orderCurrency'].$orderInfo['transactionId'].'success'.$md5Key);
                $param = 'signType=MD5&orderNo='.$orderInfo['orderNo'].'&orderAmount='.$orderInfo['orderAmount'].'&orderCurrency='.$orderInfo['orderCurrency'].'&transactionId='.$orderInfo['transactionId'].'&status=success&sign='.$trueSign;
                // 回调我们的时候需要再次回调给Leanwork
                /**********************************/
                $regularUrl = $notifyUrl.'?'.$param;
                // 用curl代替file_get_contents
                $ch = curl_init();
                $timeout = 5;
                curl_setopt ($ch, CURLOPT_URL, $regularUrl);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $file_contents = curl_exec($ch);
                if($file_contents){
                    file_put_contents('lwUnionpayNoyify.txt',$file_contents);
                    Log::notice('联行支付回调LW后的返回信息为'.$file_contents);
                    // 写入订单状态
                    model('Crmorderunion')->save(['status'=>$file_contents],['id'=>$orderInfo['id']]);
                }
                curl_close($ch);
            }
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



    /**
     * LEANWORD 环迅支付
     */
    public function lwIpsPay()
    {
        try{
            /***************************  对方的文档 */
            $md5Key = '098756421346975';
            $data = Request::instance()->only(['pickupUrl','receiveUrl','signType','orderNo','orderAmount','orderCurrency','customerId','sign']);
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
            $oldOrder = model('Crmorderips')->getRow(['orderNo'=>$data['orderNo']],'a.id,a.orderNo');
            if($oldOrder['code'] == 0){
                $data['transactionId'] = $this->getTransId();
                $orderRes = model('Crmorderips')->saveData($data,'Crmorderips','CRM订单-ipspay','0425');
                if($orderRes['code'] == 0){
                    throw new \Exception('订单保存失败'.$orderRes['msg']);
                }
            }
            $domain = Request::instance()->domain();
            $pickUrl = $data['pickupUrl'];
            $notify = $domain.url('pcshop/Pay/lwIpspayNoyify');
            $order['orderNo'] = $data['orderNo'];
            $order['orderAmount'] = $data['orderAmount'];
            $order['InFailUrl'] = $domain.url('pcshop/Order/orderFail');
            $ips = new Ips($notify,$pickUrl);
            $form = $ips->getPayParams($order);
            Log::notice('环迅支付表单信息'.$form);
            echo $form;
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }

    /**
     * 环迅支付异步回调 - LW
     */
    public function lwIpspayNoyify()
    {
        try{
            $domain = Request::instance()->domain();
            $pickUrl = $domain;
            $notify = $domain.url('pcshop/Pay/lwIpspayNoyify');
            $ips = new Ips($notify,$pickUrl);
            $res = $ips->respond();
            switch ($res['code']){
                case 0:
                    throw new \Exception($res['msg']);
                    break;
                case 1:
                    // 订单金额
                    $dealFee = $res['amount'];
                    $tradeNumber = $res['merBillNo'];
                    // 判断订单是否已经支付成功了
                    $sucOrder = model('Crmorderips')->getRow(['orderNo'=>$tradeNumber],'a.*');
                    Log::notice('LW-环迅ips订单信息'.json_encode($sucOrder));
                    if($sucOrder['code'] == 0){
                        throw new \Exception('订单不存在');
                    }
                    if($sucOrder['data']['orderAmount'] != $dealFee){
                        throw new \Exception('订单金额不一致');
                    }
                    $orderInfo = $sucOrder['data'];
                    // 判断LeanWork是否处理成功了该笔订单
                    if($orderInfo['status'] != 'success'){
                        // LW md5key
                        $md5Key = '098756421346975';
                        $notifyUrl = $orderInfo['receiveUrl'];
                        $trueSign = md5($orderInfo['signType'].$orderInfo['orderNo'].$orderInfo['orderAmount'].$orderInfo['orderCurrency'].$orderInfo['transactionId'].'success'.$md5Key);
                        $param = 'signType=MD5&orderNo='.$orderInfo['orderNo'].'&orderAmount='.$orderInfo['orderAmount'].'&orderCurrency='.$orderInfo['orderCurrency'].'&transactionId='.$orderInfo['transactionId'].'&status=success&sign='.$trueSign;
                        // 回调我们的时候需要再次回调给Leanwork
                        /**********************************/
                        $regularUrl = $notifyUrl.'?'.$param;
                        // 用curl代替file_get_contents
                        $ch = curl_init();
                        $timeout = 5;
                        curl_setopt ($ch, CURLOPT_URL, $regularUrl);
                        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                        $file_contents = curl_exec($ch);
                        if($file_contents){
                            file_put_contents('lwUnionpayNoyify.txt',$file_contents);
                            Log::notice('环迅支付回调LW后的返回信息为'.$file_contents);
                            // 写入订单状态
                            model('Crmorderips')->save(['status'=>$file_contents],['id'=>$orderInfo['id']]);
                        }
                        curl_close($ch);
                    }
                    break;
            }
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }


    /**
     * 环迅支付 - pcshop
     * @param $orderId
     * @return array|\think\response\Json
     */
    public function ipsPay($orderId=4)
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
            $domain = Request::instance()->domain();
            $pickUrl = $domain.url('pcshop/Order/orderSuccess',['type'=>'ips']);
            $notify  = $domain.url('pcshop/Order/ipspayNoyify');
            $failurl = $domain.url('pcshop/Order/orderFial');
            $ips = new Ips($notify,$pickUrl);
            $orderInfo['data']['orderNo'] = $orderInfo['data']['tradeNumber'];
            $orderInfo['data']['orderAmount'] = $orderInfo['data']['total'];
            $orderInfo['data']['InFailUrl'] = $failurl;
            $orderInfo['data']['InAttach'] = $orderInfo['data']['product'];
            $orderInfo['data']['InGoodsName'] = $orderInfo['data']['desc'];
            $form = $ips->getPayParams($orderInfo['data']);
            Log::notice('银联支付表单信息'.$form);
            return ['code'=>1,'msg'=>'','data'=>$form];
        }catch(\Exception $e){
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 环迅支付异步回调 - pcshop
     */
    public function ipspayNoyify()
    {
        try{
            $domain = Request::instance()->domain();
            $pickUrl = $domain.url('pcshop/Order/orderSuccess',['type'=>'ips']);
            $notify = $domain.url('pcshop/Pay/lwIpspayNoyify');
            $ips = new Ips($notify,$pickUrl);
            $res = $ips->respond();
            switch ($res['code']){
                case 0:
                    throw new \Exception($res['msg']);
                    break;
                case 1:
                    // 订单金额
                    $dealFee = $res['amount'];
                    $tradeNumber = $res['merBillNo'];
                    // 判断订单是否已经支付成功了
                    $sucOrder = model('Order')->getRow(['tradeNumber'=>$tradeNumber],'a.id,a.tradeNumber,a.status,a.total');
                    if($sucOrder['code'] == 0){
                        throw new \Exception('订单不存在');
                    }
                    if($sucOrder['data']['total'] != $dealFee){
                        throw new \Exception('订单金额不一致');
                    }
                    if($sucOrder['data']['status'] != 1){
                        // 修改订单状态
                        $result = model('Order')->orderNoytify($tradeNumber,1);
                        if($result['code'] == 0){
                            throw new \Exception('订单状态修改失败'.$result['msg']);
                        }
                        echo 'notify_success';
                        file_put_contents("ipspaySuccess.txt",json_encode($result));
                        exit;
                    }
                    break;
            }
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }
}