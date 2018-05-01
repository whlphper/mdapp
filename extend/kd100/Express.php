<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/1 0001
 * Time: 14:46
 * Comment:
 */

namespace kd100;

class Express
{

    //订单物流信息请求curl
    public function getUrlJson($url, $type = 0)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $rs = curl_exec($ch);
        if ($type == 1) {
            $rs = json_decode($rs, true);
        }
        return $rs;
    }

    /**
     * 物流查询api
     * @param $express_num  运单号
     * @param $type         运单公司
     * @return bool
     */
    public function getRoute($express_num,$type)
    {
        try{
            if(empty($express_num) || empty($type)){
                throw new \Exception('物流公司,运单号必填');
            }
            $url = "http://www.kuaidi100.com/query?type=" . $type . "&postid=" . $express_num . "&id=1&valicode=&temp=" . time() . rand(100000, 999999);
            $kuaidis = $this->getUrlJson($url, 1);
            if ($kuaidis) {
                return $kuaidis['data'];
            } else {
                throw new \Exception('接口异常');
            }
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}