<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/27 0027
 * Time: 10:29
 * Comment:
 */
namespace ipsPay;
ini_set('date.timezone','Asia/Shanghai');
use ipsPay\IpsPayCode;
use ipsPay\IpsPayMD5;
use think\Log;
class IpsPayNotify
{
    public $ipspay_config;

    function __construct($ipspay_config){
        $this->ipspay_config = $ipspay_config;
    }

    function IpsPayNotify($ipspay_config) {
        $this->__construct($ipspay_config);
    }

    function verifyReturn(){
        try {
            if(empty($_REQUEST)) {
                return false;
            }
            else {
                $paymentResult = $_REQUEST['paymentResult'];
                Log::notice("环迅支付返回报文:" . $paymentResult);

                $xmlResult = new \SimpleXMLElement($paymentResult);
                $strSignature = $xmlResult->GateWayRsp->head->Signature;

                $retEncodeType =$xmlResult->GateWayRsp->body->RetEncodeType;
                $ipsCode = new IpsPayCode();
                $strBody = $ipsCode->subStrXml("<body>","</body>",$paymentResult);
                $rspCode = $xmlResult->GateWayRsp->head->RspCode;
                if($rspCode == "000000")
                {
                    $md5 = new IpsPayMD5();
                    if($md5->md5Verify($strBody,$strSignature,$this->ipspay_config["MerCode"],$this->ipspay_config["MerCert"])){
                        return true;
                    }else{
                        Log::notice("环迅支付返回报文验签失败:" . $paymentResult);
                        return false;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::notice("环迅支付异常:" . $e);
        }
        return false;
    }
}