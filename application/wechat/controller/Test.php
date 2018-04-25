<?php

namespace app\wechat\controller;

use think\Controller;
use think\Request;
use \wechat\sdk\Jsapi;
use \wechat\pay\Wxpay;

class Test extends Controller
{
    /**
     * 获取前端JS 配置信息
     * JS-SDK的页面必须先注入配置信息
     * @return \think\Response
     */
    public function index()
    {
        $jsapi = new Jsapi();
        $jsConfig = $jsapi->getSignPackage();
        if(isset($jsConfig['code']) && $jsConfig['code'] == 0){
            return json(['code'=>0,'msg'=>$jsConfig['msg']]);
        }
        return json($jsConfig);
    }

    /**
     * 微信base64转图标保存
     * @return array
     */
    public function baseToIamge()
    {
        if (request()->isPost()) {
            header('Content-type:text/html;charset=utf-8');
            $base64_image_content = $_POST['imgBase'];
            //将base64编码转换为图片保存
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
                $type = $result[2];
                $new_file = ROOT_PATH.'/public/files/cutimg/';
                if (!is_dir($new_file)) {
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($new_file, 0700);
                }
                $img=time() . ".{$type}";
                $new_file = $new_file . $img;
                //将图片保存到指定的位置
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                    // 将图片保存
                    $data['fileName'] = 'cut';
                    $data['savePath'] = '/files/cutimg/'.$img;
                    // 存储图片
                    $fileId = 5;
                    // 存储图片
                    return array('code'=>1,'msg'=>'base64转图片成功','data'=>$fileId);
                }else{
                    return array('code'=>0,'msg'=>'base64转图片出错');
                }
            }else{
                return array('code'=>0,'msg'=>'上传失败,请重试');
            }

        }
    }

    /**
     * 发起支付
     * @param $out_trade_no
     * @return array
     */
    public function getPayParam($out_trade_no)
    {
        if(empty($out_trade_no)){
            return ['code'=>0,'msg'=>'订单号不能为空'];
        }
        $wxpay = new Wxpay();
        $result = $wxpay->getParameters($out_trade_no);
        return $result;
    }

    /**
     * 微信支付回调
     */
    public function notify(){
        $wxpay=new Wxpay();
        $result=$wxpay->notify();
        // 记录每次支付后返回的数据
        trace(json_encode($result,true),'info');
        if ($result) {
            // 支付成功,写自己的业务逻辑
        }

    }
}
