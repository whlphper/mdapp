<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/27 0027
 * Time: 10:14
 * Comment:
 */
namespace ipsPay;
ini_set('date.timezone','Asia/Shanghai');
use ipsPay\IpsPayMD5;
use think\Log;


class IpsPaySubmit
{
    public $ipspay_config;

    function __construct($ipspay_config){
        $this->ipspay_config = $ipspay_config;
    }

    function IpsPaySubmit($ipspay_config) {

        $this->__construct($ipspay_config);
    }
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    function buildRequestForm($para_temp) {
        //待请求参数xml
        $para = $this->buildRequestPara($para_temp);

        $sHtml = "<form id='ipspaysubmit' name='ipspaysubmit' method='post' action='".$this->ipspay_config['PostUrl']."'>";

        $sHtml.= "<input type='hidden' name='pGateWayReq' value='".$para."'/>";

        $sHtml = $sHtml."<input type='submit' style='display:none;'></form>";

        $sHtml = $sHtml."<script>document.forms['ipspaysubmit'].submit();</script>";

        return $sHtml;
    }

    /**
     * 生成要请求给IPS的参数XMl
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数XMl
     */
    function buildRequestPara($para_temp) {
        $sReqXml = "<Ips>";
        $sReqXml .= "<GateWayReq>";
        $sReqXml .= $this->buildHead($para_temp);
        $sReqXml .= $this->buildBody($para_temp);
        $sReqXml .= "</GateWayReq>";
        $sReqXml .= "</Ips>";
        Log::notice("环迅支付：请求给IPS的参数XMl:" . $sReqXml);
        return $sReqXml;
    }
    /**
     * 请求报文头
     * @param   $para_temp 请求前的参数数组
     * @return 要请求的报文头
     */
    function buildHead($para_temp){
        $md5 = new \ipsPay\IpsPayMD5();
        $sReqXmlHead = "<head>";
        $sReqXmlHead .= "<Version>".$para_temp["Version"]."</Version>";
        $sReqXmlHead .= "<MerCode>".$para_temp["MerCode"]."</MerCode>";
        $sReqXmlHead .= "<MerName>".$para_temp["MerName"]."</MerName>";
        $sReqXmlHead .= "<Account>".$para_temp["Account"]."</Account>";
        $sReqXmlHead .= "<MsgId>".$para_temp["MsgId"]."</MsgId>";
        $sReqXmlHead .= "<ReqDate>".$para_temp["ReqDate"]."</ReqDate>";
        $sReqXmlHead .= "<Signature>".$md5->md5Sign($this->buildBody($para_temp),$para_temp["MerCode"],$this->ipspay_config['MerCert'])."</Signature>";
        $sReqXmlHead .= "</head>";
        return $sReqXmlHead;
    }
    /**
     *  请求报文体
     * @param  $para_temp 请求前的参数数组
     * @return 要请求的报文体
     */
    function buildBody($para_temp){
        $sReqXmlBody = "<body>";
        $sReqXmlBody .= "<MerBillNo>".$para_temp["MerBillNo"]."</MerBillNo>";
        $sReqXmlBody .= "<GatewayType>".$para_temp["PayType"]."</GatewayType>";
        $sReqXmlBody .= "<Date>".$para_temp["Date"]."</Date>";
        $sReqXmlBody .= "<CurrencyType>".$para_temp["CurrencyType"]."</CurrencyType>";
        $sReqXmlBody .= "<Amount>".$para_temp["Amount"]."</Amount>";
        $sReqXmlBody .= "<Lang>".$para_temp["Lang"]."</Lang>";
        $sReqXmlBody .= "<Merchanturl><![CDATA[".$para_temp["Return_url"]."]]></Merchanturl>";
        $sReqXmlBody .= "<FailUrl><![CDATA[".$para_temp["FailUrl"]."]]></FailUrl>";
        $sReqXmlBody .= "<Attach><![CDATA[".$para_temp["Attach"]."]]></Attach>";
        $sReqXmlBody .= "<OrderEncodeType>".$para_temp["OrderEncodeType"]."</OrderEncodeType>";
        $sReqXmlBody .= "<RetEncodeType>".$para_temp["RetEncodeType"]."</RetEncodeType>";
        $sReqXmlBody .= "<RetType>".$para_temp["RetType"]."</RetType>";
        $sReqXmlBody .= "<ServerUrl><![CDATA[".$para_temp["S2Snotify_url"]."]]></ServerUrl>";
        $sReqXmlBody .= "<BillEXP>".$para_temp["BillEXP"]."</BillEXP>";
        $sReqXmlBody .= "<GoodsName>".$para_temp["GoodsName"]."</GoodsName>";
        $sReqXmlBody .= "<IsCredit>".$para_temp["IsCredit"]."</IsCredit>";
        $sReqXmlBody .= "<BankCode>".$para_temp["BankCode"]."</BankCode>";
        $sReqXmlBody .= "<ProductType>".$para_temp["ProductType"]."</ProductType>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }

    function respond()
    {
        $ipspayNotify = new IpsPayNotify($ipspay_config);
        $verify_result = $ipspayNotify->verifyReturn();

        if ($verify_result) { // 验证成功
            echo "ipscheckok";
        } else {
            echo "ipscheckfail";
        }
    }
}