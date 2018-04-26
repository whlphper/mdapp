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
/**
 * IPS环迅支付接口类
 * Class Ips
 * @package ipsPay
 */
class Ips{
    // 商户号
    private $mchId = '4545';
    // MD5 key
    private $key = '4545';
    private $mchName = 'test';
    private $account = 'aaa';

    public function getPayParams()
    {
        $ips['GateWayReq'] = [];
        // 参数-支付请求头数据  start
        $requestHeader = [];
        // Version v1.0.0
        $requestHeader['Version'] = 'v1.0.0';
        // MerCode 商户号
        $requestHeader['MerCode'] = $this->mchId;
        // MerName 商户名
        $requestHeader['MerName'] = $this->mchName;
        // Account 账户号
        $requestHeader['Account'] = $this->account;
        // MsgId   消息编号
        $requestHeader['MsgId'] = rand(1000,99999);
        // ReqDate   商户请求时间
        $requestHeader['ReqDate'] = time();
        // 参数-支付请求头数据  end


        // 参数-支付请求主题数据  start
        $requestBody = [];
        // MerBillNo  订单号
        // Amount   金额
        // Date 日期
        // CurrencyType 币种
        // GatewayType  支付方式
        // Lang 语言
        // Merchanturl  支付成功返回的商户url
        // FailUrl  支付失败返回的url
        // Attach   商户数据包
        // OrdeEncodeType   支付加密方式  5
        // RetEncodeType    交易返回接口加密方式
        // RetType  返回方式
        // ServerUrl 异步回调url
        // BillEXP  订单有效期
        // GoodsName    商品名称
        // IsCredit 直连选项
        // BankCode 银行号
        // ProductType  产品类型
        // UserRealName 姓名
        // UserId   平台用户名
        $ips['GateWayReq']['body'] = $requestBody;
        // 参数-支付请求主题数据  end
        // Signature   数字签名
        $requestHeader['Signature'] = $this->makeSign($requestBody);
        $ips['GateWayReq']['head'] = $requestHeader;

        // 获取form 返回
        $xml = $this->toXml($ips);
        $result = $this->getPayForm($xml);
        return $result;
    }

    /**
     * 获取支付form
     * @param $payParams 支付的form body 内容
     * @return string
     */
    private function getPayForm($payParams)
    {
       $form = '<form action="https://neway.ips.com.con/psfp-entry/gateway/payment.do" method="post"><input name="pGateWayReq" type="hidden" value="'.$payParams.'"></form>';
       return $form;
    }

    /**
     * 输出xml字符
     * @throws \Exception
     **/
    public function toXml($data){
        try{
            if(!is_array($data) || count($data) <= 0){
                throw new \Exception("数组数据异常！");
            }
            $xml = "<xml>";
            foreach ($data as $key=>$val){
                if (is_numeric($val)){
                    $xml.="<".$key.">".$val."</".$key.">";
                }else{
                    $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                }
            }
            $xml.="</xml>";
            return $xml;
        }catch(\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }

    }

    /**
     * 生成签名
     */
    public function makeSign($body){
        return md5($body.$this->mchId.$this->key);
    }

    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function toArray($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
}