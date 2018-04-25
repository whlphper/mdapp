<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 9:47
 * Comment:
 */
namespace cbpay;
use think\Db;
use think\Exception;
use think\Request;

/**
 * 国银支付
 * Class Pay
 * @package cbpay
 */
class Pay{

    public $mchId = 'gypay0180050';
    public $key = '9423cca112324053a6c579a5aa6056e3';
    public $applyUrl = 'http://39.108.150.3:8080/gyprovider/netpay/applyPay.do';
    public $queryUrl = "http://112.74.25.79:9999/gyprovider/netpay/queryPay.do";
    public $debugInfo;


    /**
     *      请求支付
     *      @params         string              $order              商户订单信息
     *      @params         int                 $money              订单金额,单位为分,只允许数字
     *      @params         string              $bankSegment        银行代号
     *
     */
    public function applyPay($order)
    {
        $domain = Request::instance()->domain();
        $notify = url('/pcshop/Pay/cbPayNotify');
        $data = array();
        $params = $order;
        $data['gymchtId'] = $this->mchId;//商户号
        $data['tradeSn'] = $params['tradeSn'];//'CS'.date('YmdHis').rand(1000,9999);//商户订单号
        $data['orderAmount'] = $params['orderAmount'];//订单金额
        $data['goodsName'] = "test20180424 1453";//商品名称
        $data['bankSegment'] = $params['bankSegment'];//银行代号
        $data['cardType'] = '00';//00-贷记 01-借记 02-准贷记
        $host = gethostbyname($_SERVER['SERVER_NAME']);
        $data['notifyUrl'] = $domain.$notify;//回调通知地址
        $data['callbackUrl'] = 'http://tavbkmqx.tw.lwork.com';//回调跳转地址
        $data['channelType'] = $params['channelType'];//1-pc 2-手机
        $data['nonce'] = md5(time().mt_rand(0,1000));//随机字符串
        $sign = $this->createSign($data, $this->key);//数字签名
        $data['sign'] = $sign;
        $rs = $this->httpClient($data, $this->applyUrl);
        $rs = json_decode($rs, true);
        if($rs['resultCode'] == '00000'){
            $sign = $rs['sign'];
            unset($rs['sign']);
            ksort($rs);
            if($this->isGySign($rs, $this->key,$sign)){
                return "<script>window.location.href='".$rs['payUrl']."';</script>";
            }else{
                return '返回签名错误';
            }
        }else{
            return $rs['message'];
        }
    }

    /**
     * 回调
     */
    public function noticeResult()
    {
        $rs = file_get_contents("php://input");
        $rs = json_decode($rs, true);
        if($this->isGySign($rs, $this->key,$rs['sign']))
        {
            if($rs['tradeState'] == 'SUCCESS'){
                // 更改订单状态;notify_url 有可能重复通知,商户系统需要做去重处理,避免多次发货
                // 根据tradeSn来追踪订单   便于回调到Leanwork
                $orderInfo = model('Crmorder')->getRow(['orderNo'=>$rs['tradeSn']],'a.*');
                $orderInfo = $orderInfo['data'];
                $md5Key = '386125028475603';
                $notifyUrl = $orderInfo['receiveUrl'];
                $trueSign = md5($orderInfo['signType'].$orderInfo['orderNo'].$orderInfo['orderAmount'].$orderInfo['orderCurrency'].$rs['transaction_id'].'success'.$md5Key);
                $param = 'signType=MD5&orderNo='.$rs['tradeSn'].'&orderAmount='.$orderInfo['orderAmount'].'&orderCurrency='.$orderInfo['orderCurrency'].'&transactionId='.$rs['transaction_id'].'&status=success&sign='.$trueSign;
                echo "success";
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
                curl_close($ch);
                exit();
            }else{
                echo "failure";
                exit();
            }
        }else{
            echo "failure";
        }

    }

    /*
     *      交易查询接口
     *
     */
    public function verifyResult()
    {
        $data = array();
        $params = $_POST;
        $data['gymchtId'] = $this->mchId;//商户号
        $data['tradeSn'] = $params['tradeSn'];//商户订单号
        $data['orderAmount'] = $params['orderAmount'];//订单金额
        $data['nonce'] = md5(time().mt_rand(0,1000));//随机字符串
        $sign = $this->createSign($data, $this->key);//数字签名
        $data['sign'] = $sign;
        $rs = $this->httpClient($data, $this->queryUrl);
        $rs = json_decode($rs, true);
        if(($rs['resultCode'] == '00000') && ($rs['tradeState']=='SUCCESS'))
        {
            if($this->isGySign($rs, $this->key,$rs['sign']))
            {
                //查询成功业务逻辑
                echo '此交易成功';
            }
        }else{
            echo '此交易失败';
        }

    }

    /**
     *       创建md5摘要,规则是:按参数名称a-z排序,遇到空值的参数不参加签名。
     *       @params         array               $data               参与签名的数组
     *       @params         string              $gymchtKey          商户密钥
     *       @return         string              $sign               返回生成的sign
     */
    public function createSign($data,$gymchtKey)
    {
        $signPars = "";

        ksort($data);

        foreach($data as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }

        $signPars .= "key=" . $gymchtKey;

        $sign = strtoupper(md5($signPars));

        return $sign;
    }

    /*
     *      验证是否国银签名
     *      @params         array               $data               参与签名的数组
     *      @params         string              $gymchtKey          商户密钥
     *      @return         bool                                    true:是 false:否
     */
    public function isGySign($data,$gymchtKey,$sign)
    {
        $gySign = $this->createSign($data,$gymchtKey);

        return $sign==$gySign;
    }

    /*
     *      设置调试内容
     *      @params         string               $debugInfo          调试内容
     *
     */
    public function setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }

    /*
     *      获取调试内容
     *
     *
     */
    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /*
     *      数据记录
     *
     *
     */
    public static function dataRecodes($title,$data)
    {
        $handler = fopen('result.txt','a+');
        $content = "================".$title."===================\n";
        if(is_string($data) === true){
            $content .= $data."\n";
        }
        if(is_array($data) === true){
            forEach($data as $k=>$v){
                $content .= "key: ".$k." value: ".$v."\n";
            }
        }
        $flag = fwrite($handler,$content);
        fclose($handler);
        return $flag;
    }


    /*
    *      post方法请求
    *      @param      array       $data       post数据
    *      @param      string      $url        server url
    *      @return     json        $rs         接口返回数据,此demo中为返回json数据
    */
    public function httpClient($data, $url)
    {
        $postdata = http_build_query($data);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $res = curl_exec($ch);
            curl_close($ch);
            return $res;
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            return false;
        }
    }
}
