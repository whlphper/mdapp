<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/27 0027
 * Time: 10:18
 * Comment:
 */
namespace ipsPay;
use think\Log;

/**
 * MD5
 * 详细：MD5加密
 * 日期：2016-12-15
 * Class IpsPayMD5
 * @package ipsPay
 */
class IpsPayMD5{

    /**
     * @param $prestr  需要签名的字符串
     * @param $merCode 商戶號
     * @param $key     私钥
     * @return string  签名结果
     */
    function md5Sign($prestr, $merCode, $key)
    {
        $prestr = $prestr . $merCode . $key;
        return md5($prestr);
    }

    /**
     * @param $prestr  需要签名的字符串
     * @param $sign    签名结果
     * @param $merCode 商戶號
     * @param $key     私钥
     * @return bool    签名结果
     */
    function md5Verify($prestr, $sign, $merCode, $key)
    {
        $prestr = $prestr . $merCode . $key;
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证签名
     * @param $prestr  需要签名的字符串
     * @param $sign
     * @param $rsaPubKey
     * @return bool    签名结果
     */
    function rsaVerify($prestr, $sign, $rsaPubKey)
    {
        try {
            $signBase64 = base64_decode($sign);
            Log::info("=========1111111=========:" . $signBase64);
            $public_key = file_get_contents('rsa_public_key.pem');

            $pkeyid = openssl_get_publickey($public_key);
            if ($pkeyid) {

                $verify = openssl_verify($prestr, $signBase64, $pkeyid);

                openssl_free_key($pkeyid);
            }
            Log::info("==================:" . openssl_error_string());
            if ($verify == 1) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            Log::notice("环迅支付rsaVerify异常:" . $e);
        }
        return false;
    }
}