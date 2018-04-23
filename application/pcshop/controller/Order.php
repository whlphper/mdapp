<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 12:44
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class Order extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->modelName = 'Order';
        $this->theme = '订单';
        $this->order = 'a.created_at desc';
        $this->field = 'a.*,b.name,c.savePath as albumPath';
        $this->join = [['Product b','a.productId=b.id','left'],['File c','b.album=c.id','left']];
        $this->model = model($this->modelName);
    }

    /**
     * 生成订单信息
     * @param $orderGoods
     * @return \think\response\Json|\think\response\View
     */
    public function checkoutOrder($orderGoods)
    {
        Db::startTrans();
        try{
            $flag = true;
            $orderData['product'] = $orderGoods;
            $orderExists = model('Order')->getRow(['userId'=>session("pcshopUserId"),'product'=>$orderGoods],'id');
            if($orderExists['code'] == 1){
                $flag = false;
            }
            // 将需要购买的商品列出
            $orderGoods = explode("|",$orderGoods);
            $allNumber = 0;
            $total = 0;
            $info = '';
            $orderDetail = [];
            foreach($orderGoods as $k=>$v){
                $proNum = explode(":",$v);
                // 订单中商品信息  将其返回页面供查看
                $cartGoods = model('Product')->getRow(['a.id'=>$proNum[0]],'a.id,a.shopPrice,a.keyword,a.marketPrice,a.name,b.savePath as albumPath',[['File b','a.album=b.id','left']]);
                $cartGoods = $cartGoods['data'];
                $info = $info . $cartGoods['name'].'x'.$proNum[1];
                $cartGoods['number'] = $proNum[1];
                $total += $proNum[1]*$cartGoods['shopPrice'];
                $allNumber += $proNum[1];
                $multyGoods[] = $cartGoods;
                if($flag){
                    // 订单详情数据
                    $curDetail['productId'] = $cartGoods['id'];
                    $curDetail['number'] = $proNum[1];
                    $curDetail['shopPrice'] = $cartGoods['shopPrice'];
                    $curDetail['marketPrice'] = $cartGoods['marketPrice'];
                    $orderDetail[] = $curDetail;
                }
            }
            if($flag) {
                // 写入订单信息
                $orderData['userId'] = session("pcshopUserId");
                $orderData['desc'] = $info;
                $orderData['total'] = $total;
                $orderData['tradeNumber'] = $this->merchantOrder();
                $orderResult = model('Order')->saveData($orderData, 'Order', '用户下单', '999');
                if ($orderResult['code'] == 0) {
                    throw new \think\Exception('下单失败' . $orderResult['msg']);
                }
                $orderId = $orderResult['data'];
                // 写入订单详情  加入订单ID
                foreach($orderDetail as $k=>$v){
                    $orderDetail[$k]['orderId'] = $orderId;
                }
                // 批量新增到订单详情表
                $detailResult = model('OrderDetail')->saveAll($orderDetail);
                if(empty($detailResult)){
                    throw new \think\Exception('下单失败,订单详情记录出错');
                }
            }else{
                $orderId = $orderExists['data']['id'];
            }

            // 获取当前用户所有收货地址
            $userAddress = model('Address')->getDataList(['userId'=>session('pcshopUserId')],'a.id,a.address,a.userId,a.region,a.mobile,a.reciver,a.isDefault',[],'a.isDefault desc,a.created_at desc');
            Db::commit();
            return view('',['address'=>$userAddress['data'],'cartGoods'=>$multyGoods,'total'=>$total,'allNumber'=>$allNumber,'orderId'=>$orderId]);
        }catch(\Exception $e){
            Db::rollback();
            mdLog($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function addrRegion()
    {
        // 获取省市区信息
        $regInfo = model('Region')->getRegion();
        return $regInfo;
    }

    public function perfectOrder($id,$payType,$addressId)
    {
        $result = $this->saveToTable();
        return $result;
    }

    public function checkOrder(Request $request)
    {

    }

    public function orderSuccess()
    {
        return view();
    }

    public function unionpayNoyify()
    {

    }

    private function merchantOrder()
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}