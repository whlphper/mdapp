<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/26 0026
 * Time: 17:28
 * Comment:
 */

namespace ipsPay;

use think\Db;
use think\Exception;
use think\Request;
use ipsPay\IpsPaySubmit;
use ipsPay\IpsPayNotify;

/**
 * IPS环迅支付接口类
 * Class Ips
 * @package ipsPay
 */
class Ips
{

    public $ipspay_config = [];

    public function __construct($notify,$pickUrl)
    {
        $ipspay_config['Version'] = 'v1.0.0';
        //商戶號
        $ipspay_config['MerCode'] = '';
        //交易賬戶號
        $ipspay_config['Account'] = '';
        //商戶證書
        $ipspay_config['MerCert'] = '';
        //請求地址
        $ipspay_config['PostUrl'] = 'https://newpay.ips.com.cn/psfp-entry/gateway/payment.do';
        //服务器S2S通知页面路径
        $ipspay_config['S2Snotify_url'] = $notify;
        //页面跳转同步通知页面路径
        $ipspay_config['return_url'] = $pickUrl;
        //156#人民币
        $ipspay_config['Ccy'] = "156";
        //GB中文
        $ipspay_config['Lang'] = "GB";
        //订单支付接口加密方式 5#订单支付采用Md5的摘要认证方式
        $ipspay_config['OrderEncodeType'] = "5";
        //返回方式 1#S2S返回
        $ipspay_config['RetType'] = "1";
        //消息ID
        $ipspay_config['MsgId'] = rand(1000,9999);
        $this->ipspay_config = $ipspay_config;
    }

    public function getPayParams($order)
    {
        try{
            // 商户订单号，商户网站订单系统中唯一订单号，必填
            $inMerBillNo = $order['orderNo'];
            //支付方式 01#借记卡 02#信用卡 03#IPS账户支付
            $selPayType = !empty($order['selPayType']) ? $order['selPayType'] : '01';
            //商戶名
            $inMerName = !empty($order['InMerName']) ? $order['InMerName'] : 'test';
            //订单日期
            $inDate = date('Ymd');//$order['InDate'];
            //订单金额
            $inAmount = $order['orderAmount'];
            //支付结果失败返回的商户URL
            $inFailUrl = $order['InFailUrl'];
            //商户数据包
            $inAttach = !empty($order['InAttach']) ? $order['InAttach'] : '商品';
            //交易返回接口加密方式
            $selRetEncodeType = !empty($order['selRetEncodeType']) ? $order['selRetEncodeType'] : 17;
            //订单有效期
            $inBillEXP = !empty($order['InBillEXP']) ? $order['InBillEXP'] : 1;
            //商品名称
            $inGoodsName = !empty($order['InGoodsName']) ? $order['InGoodsName'] : '商品';
            //银行号
            $inBankCode = !empty($order['InBankCode']) ? $order['InBankCode'] : '';
            //产品类型  1个人  2企业
            $selProductType = !empty($order['selProductType']) ? $order['selProductType'] : 1;
            //直连选项  空 非直连  1直连
            $selIsCredit = !empty($order['selIsCredit']) ? $order['selIsCredit'] : '';

            /************************************************************/
            //构造要请求的参数数组
            $ipspay_config = $this->ipspay_config;
            $parameter = array(
                "Version" => $ipspay_config['Version'],
                "MerCode" => $ipspay_config['MerCode'],
                "Account" => $ipspay_config['Account'],
                "MerCert" => $ipspay_config['MerCert'],
                "PostUrl" => $ipspay_config['PostUrl'],
                "S2Snotify_url" => $ipspay_config['S2Snotify_url'],
                "Return_url" => $ipspay_config['return_url'],
                "CurrencyType" => $ipspay_config['Ccy'],
                "Lang" => $ipspay_config['Lang'],
                "OrderEncodeType" => $ipspay_config['OrderEncodeType'],
                "RetType" => $ipspay_config['RetType'],
                "MerBillNo" => $inMerBillNo,
                "MerName" => $inMerName,
                "MsgId" => $ipspay_config['MsgId'],
                "PayType" => $selPayType,
                "FailUrl" => $inFailUrl,
                "Date" => $inDate,
                "ReqDate" => date("YmdHis"),
                "Amount" => $inAmount,
                "Attach" => $inAttach,
                "RetEncodeType" => $selRetEncodeType,
                "BillEXP" => $inBillEXP,
                "GoodsName" => $inGoodsName,
                "BankCode" => $inBankCode,
                "IsCredit" => $selIsCredit,
                "ProductType" => $selProductType,
            );
            //建立请求
            $ipspaySubmit = new IpsPaySubmit($ipspay_config);
            $html_text = $ipspaySubmit->buildRequestForm($parameter);
            return $html_text;
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }



    /**
     * 获取支付成功的返回数据
     */
    public function respond()
    {
        try{
            $ipspayNotify = new IpsPayNotify($this->ipspay_config);
            $verify_result = $ipspayNotify->verifyReturn();
            if ($verify_result) {
                // 验证成功
                echo "ipscheckok";
                Log::notice('IPS环迅支付通知成功');
                // 验证成功
                $paymentResult = $_REQUEST['paymentResult'];
                $xmlResult = new \SimpleXMLElement($paymentResult);
                $status = $xmlResult->GateWayRsp->body->Status;
                if($status == "Y"){
                    // 商户订单号
                    $merBillNo = $xmlResult->GateWayRsp->body->MerBillNo;
                    // 总额
                    $amount = $xmlResult->GateWayRsp->body->Amount;
                    // IPS订单号
                    $ipsBillNo = $xmlResult->GateWayRsp->body->IpsBillNo;
                    // IPS交易流水号
                    $ipsTradeNo = $xmlResult->GateWayRsp->body->IpsTradeNo;
                    // 银行订单号
                    $bankBillNo = $xmlResult->GateWayRsp->body->BankBillNo;
                    $message = "交易成功";
                    return ['code'=>1,'msg'=>$message,'merBillNo'=>$merBillNo,'amount'=>$amount,'ipsTradeNo'=>$ipsTradeNo];
                }elseif($status == "N"){
                    $message = "交易失败";
                    return ['code'=>2,'msg'=>$message];
                }else{
                    $message = "交易处理中";
                    return ['code'=>3,'msg'=>$message];
                }
            }else{
                echo "ipscheckfail";
                return ['code'=>0,'msg'=>'ipscheckfail'];
            }
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}