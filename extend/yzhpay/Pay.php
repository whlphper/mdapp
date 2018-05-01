<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/1 0001
 * Time: 9:06
 * Comment:
 */

namespace yzhpay;

class Pay
{
    public $comid = 'TBwm0YYC0d';
    public $comkey = 'HJBFGFWqdYahwttLHZAxP2ZF1Gg1ER';
    public $merid = '14186';
    public $pay_url = 'http://www.yzhpay.com/Pay/Index/pay';

    /**
     * 订单支付
     * @param $order
     * @return mixed
     */
    public function getPayParams($order)
    {
        try{
            $post_url = $this->pay_url;///易智慧的请求地址
            $order_no = $order['orderNo'];
            $comid = $this->comid;///易智慧的密钥id
            $comkey = $this->comkey;///易智慧的密钥key
            $merid = $this->merid;//商户号
            $service_type = 802;//服务类型 802快捷 803网银
            $amount = strval($order['orderAmount'] * 100);//单位：分
            $subject = isset($order['desc']) ? $order['desc'] : '商品';//商品名称
            $result_url = $order['pickUrl'];//结果同步通知地址
            $notify_url = $order['notify'];//结果异步回调地址
            $type = 2;//类型
            $accNo = isset($order['accNo']) ? $order['accNo'] : '';;//银行卡号
            $post_data = array(
                'order_no' => $order_no,
                'mer_id' => $merid,///
                'service_type' => $service_type,
                'order_amount' => $amount,
                'subject' => $subject,
                'sign_type' => 'md5',//验签类型
                'type' => $type,
            );
            //md5加密
            $str = $this->linkString($post_data);
            $sign = md5($comid . $comkey . $str);
            $post_data['sign'] = $sign;
            $post_data['return_url'] = $result_url;
            $post_data['notify_url'] = $notify_url;
            $post_data['order_time'] = date('Y-m-d H:i:s');
            if ($accNo) {
                $post_data['accNo'] = $accNo;
            }
            $return = $this->createHtml($post_data, $post_url);
            return $return;
        }catch (\Exception $e){
            mdLog($e);
            echo $e->getMessage();
        }
    }

    /**
     * yzhPay 支付回调
     * @return array
     */
    public function respond()
    {
        try{
            //获取数据
            $comid = $this->comid;//秘钥id
            $comkey = $this->comkey;//秘钥key
            $mer_id = $this->merid;//商户号
            $order_no = $_POST['order_no'];//订单号
            $service_type = $_POST['service_type'];//服务类型
            $merOderidNum = $_POST['transaction_no'];//三方订单号
            $code = $_POST['resp_code'];//状态code
            $message = $_POST['resp_msg'];//状态信息
            $sign_type = $_POST['sign_type'];//验签类型
            $order_time = $_POST['order_time'];//订单时间
            $type = $_POST['type'];//通道类型
            $trade_status = $_POST['trade_status'];//支付状态
            $order_amount = $_POST['order_amount'];//金额(以分为单位)
            $sign = $_POST['sign'];//签值
            $subject = $_POST['subject'];//商品名称
            $amount = $order_amount / 100;//金额
            $data = array(
                'order_no' => $order_no,
                'mer_id' => $mer_id,
                'service_type' => $service_type,
                'order_amount' => $order_amount,
                'subject' => $subject,
                'sign_type' => $sign_type,
                'type' => $type
            );
            //验签
            $res = $this->publicSing($comid, $comkey, $data, $sign);
            if ($res == 'SUCCESS' && $trade_status == 'success') {
                //入库操作
                echo 'success';
                return ['code'=>1,'msg'=>'success','orderNo'=>$order_no,'total'=>$amount,'transcationId'=>$merOderidNum];
            } else {
                //验签失败
                echo 'fail';
                return ['code'=>0,'msg'=>'yzh pay fail','orderNo'=>$order_no];
            }
        }catch (\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage(),'orderNo'=>$order_no];
        }
    }

    /**
     * 支付表单
     * @param $params
     * @param $url
     * @return string
     */
    public function createHtml($params, $url)
    {
        $encodeType = isset ($params ['encoding']) ? $params ['encoding'] : 'UTF-8';
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset="UTF-8"/></head><body onload="javascript:document.pay_form.submit();">
			<form id="pay_form" name="pay_form" action="' . $url . '" method="post">';
        foreach ($params as $key => $value) {
            $html .= "<input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= '<!-- <input type="submit" type="hidden">--></form></body><script>document.forms[\'pay_form\'].submit();</script></html>';
        return $html;
    }

    //拼接字符串
    public function linkString($para, $sort = true, $encode = true)
    {
        if ($para == NULL || !is_array($para))
            return "";
        $linkString = "";
        if ($sort) {
            ksort($para);
        }
        while (list ($key, $value) = each($para)) {
            if ($value != '') {
                if ($encode) {
                    $value = urlencode($value);
                }
                $linkString .= $key . "=" . $value . "&";
            }
        }
        // 去掉最后一个&字符
        $linkString = substr($linkString, 0, count($linkString) - 2);
        return $linkString;
    }

    //验签方法
    public function publicSing($comid, $comkey, $data, $sign)
    {
        $params_str = createLinkString($data, true, false);
        $newsign = md5($comid . $comkey . $params_str);
        if ($newsign == $sign) {
            return 'SUCCESS';
        } else {
            return 'FAIL';
        }

    }

    /**
     * 讲数组转换为string
     *
     * @param $para 数组
     * @param $sort 是否需要排序
     * @param $encode 是否需要URL编码
     * @return string
     */
    public function createLinkString($para, $sort, $encode)
    {
        if ($para == NULL || !is_array($para))
            return "";

        $linkString = "";
        if ($sort) {
            ksort($para);
        }
        while (list ($key, $value) = each($para)) {
            if ($encode) {
                $value = urlencode($value);
            }
            $linkString .= $key . "=" . $value . "&";
        }
        // 去掉最后一个&字符
        $linkString = substr($linkString, 0, count($linkString) - 2);
        return $linkString;
    }
}