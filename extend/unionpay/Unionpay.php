<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 9:47
 * Comment:
 */
namespace unionpay;
use think\Db;
use think\Exception;
use think\Request;
/**
 * 对接支付接口类
 * Class Unionpay
 * @package unionpay
 */
class Unionpay{
    // 域名信息
    // 测试环境：http://user.sdecpay.com/
    // 正式环境：https://user.ecpay.cn/
    // 为了保证对授权合作商户的身份进行验证，由联行支付为每个合作商户制定一个128位授权码（key），
    // 在计算签名时加入这个授权码，通过验证签名就可以确认合作商户的身份。
    // 授权码只在联行支付和特定合作商户之间共享使用，请合作商户妥善包管此授权码，
    // 一旦发生丢失或泄漏，必须马上和支付平台联系。（建议授权码定期更换）';
    //private $key = 'sZEYTSOzxCtSiEqs9kHVHasNMkX98Qdzw3BwUBZInZ1qg3p2PU3MYI0XsEpfgqtbhjmHRzutKX2xVALmumnXECoFO8DM4JoArK80SgeiE4jhF7guxtf1rVcfdEK7u0eP';
    private $key = 'vuTHjC5lCUy2Yfvz0nKNoTluktcgVjWcGPXMzeDqf8aJkoZg5xixVz8Q9cZnX09QyxEmQ1oyihNC1YJoYHB8jYrl1WlG5ovsQDOgieaR76Z7QbVVpc3maraD2tISiHjS';
    private $merName = '北京汇众成信息技术服务有限公司';
    //private $merId = '114571';
    private $merId = '879711';

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     * @return string
     */
    public function get_code($order, $payment=[]){
        try{
            $domain = Request::instance()->domain();
            //商户代码（merId）
            $merId = $this->merId;
            //商户系统生成的订单号
            $dealOrder = $order['tradeSn'];
            //支付金额，保留两个小数位
            $dealFee	= number_format($order['orderAmount'],2);;
            //订单支付结果同步返回地址  也就是对用户呈现的界面
            $dealReturn = $order['pickupUrl'];
            //订单支付结果异步返回地址  也就是异步修改订单状态的接口
            $dealNotify = $domain.url('pcshop/Pay/lwUnionpayNoyify');
            //生成签名
            $dealSignure=sha1($merId.$dealOrder.$dealFee.$dealReturn.$this->key);
            //获得表单传过来的数据
            $def_url  = '<br />';
            //测试地址
            $def_url  = '<form id="unionpaysubmit" method="post" action="https://user.ecpay.cn/paygate.html" >';
            //商户编号
            $def_url .= '	<input type = "hidden" name = "merId"	value = "'.$merId.'">';
            //商品名称
            $def_url .= '	<input type = "hidden" name = "dealName"	value = "'.$order['desc'].'">';
            //订单标号
            $def_url .= '	<input type = "hidden" name = "dealOrder" 				value = "'.$dealOrder.'">';
            //订单金额
            $def_url .= '	<input type = "hidden" name = "dealFee" 			value = "'.$dealFee.'">';
            //订单数据签名
            $def_url .= '	<input type = "hidden" name = "dealSignure"			value = "'.$dealSignure.'">';
            //支付完成后将结果返回到此url
            $def_url .= '	<input type = "hidden" name = "dealReturn"			value = "'.$dealReturn.'">';
            //支付完成后将结果通知到此url
            $def_url .= '	<input type = "hidden" name = "dealNotify"			value = "'.$dealNotify.'">';
            $def_url .= '	<input  style="padding: 5px;width: 100%;background-color: darkseagreen;cursor: pointer;" type=submit value="立即付款">';
            $def_url .= '</form>';
            $def_url .= '<script>unionpaysubmit.submit();</script>';
            return $def_url;
        }catch(\Exception $e){
            mdLog($e);
            return $e->getMessage();
        }

    }

    /**
     * 也就是支付结果回调
     * 响应操作
     */
    public function respond(){
        try{
            $dealOrder = $_REQUEST['dealOrder'];
            $dealFee = $_REQUEST['dealFee'];
            $dealState = $_REQUEST['dealState'];
            $dealSignature = $_REQUEST['dealSignure'];
            $dealId = isset($_REQUEST['dealId']) ? $_REQUEST['dealId'] : null ;
            //生成签名
            $strSignature = sha1($dealOrder.$dealState.$this->key);
            //记录下返回的订单信息
            file_put_contents("unipayresponse.txt",json_encode($_REQUEST));
            //我们自己生成的签名
            file_put_contents("unipaySign.txt",$strSignature);
            if ( empty($dealSignature) || ($dealSignature != $strSignature)){
                throw new Exception('签名校验失败');
            }else{
                // 判断支付状态
                if($dealState === 'SUCCESS'){
                    // 处理支付成功的订单
                }else{
                    throw new Exception('订单支付失败');
                }
            }
            return ['code'=>1,'msg'=>'支付成功','data'=>$dealOrder,'total'=>$dealFee,'dealId'=>$dealId];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    private function merchantOrder()
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
