<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 13:33
 * Comment:
 */

namespace app\common\model;

use app\common\model\Base;

class Product extends Base
{

    public $proField = 'a.id,a.name,a.album,a.skuId,a.shopPrice,a.marketPrice,a.status,a.type,a.desc,a.stock,a.detail,a.brandId,b.savePath as albumPath';
    public $proJoin = [['File b', 'a.album=b.id', 'left']];
    public $proOrder = 'a.sort desc';

    public function getStatusAttr($value)
    {
        $status = [1 => '正常', 2 => '下架'];
        return $status[$value];
    }

    public function getTypeAttr($value)
    {
        $status = [1 => '新品', 2 => '热销', 3 => '赠品', 4 => '促销', 5 => '团购'];
        return $status[$value];
    }

    public function getProductDetail($id = 0)
    {
        // 获取基本信息
        $proInfo = $this->getRow(['a.id' => $id], $this->proField, $this->proJoin);
        if ($proInfo['code'] == 0) {
            return ['code' => 0, 'msg' => $proInfo['msg']];
        }
        $proInfo = $proInfo['data'];
        $result['proInfo'] = $proInfo;
        // 获取此商品对应的SKU信息
        if ($proInfo['skuId']) {
            $skuInfo = model('Spec')->getProSku($proInfo['id']);
            $regular = $this->getHashSku($skuInfo['data']);
        }
        $result['skuInfo'] = !empty($regular) ? $regular : [];
        // 获取此商品所属品牌
        $brandInfo = model('Brand')->getRow(['a.id' => $proInfo['brandId']], 'a.name,a.poster,a.desc,b.savePath as posterPath', [['File b', 'a.poster=b.id', 'left']], 'a.sort desc');
        if ($brandInfo['code'] == 0) {
            return ['code' => 0, 'msg' => $brandInfo['msg']];
        }
        $result['brandInfo'] = $brandInfo['data'];
        // 获取对应品牌下的商品
        $brandProList = $this->getDataList(['brandId' => $proInfo['brandId'], 'a.id' => ['<>', $proInfo['id']]], 'a.id,a.name,a.shopPrice,a.album,b.savePath as albumPath', $this->proJoin, $this->proOrder, 4);
        if ($brandProList['code'] == 0) {
            return ['code' => 0, 'msg' => $brandProList['msg']];
        }
        $result['brandProList'] = $brandProList['data'];

        // 获取商城支付服务
        // 获取商品详情页面广告位信息
        $adList = model('Advertisement')->getAdByPosition(2);
        if ($adList['code'] == 0) {
            return ['code' => 0, 'msg' => $adList['msg']];
        }
        $result['adList'] = $adList['data'];
        // 获取商品全部分类 这个在base已经aiisgn过了
        // 获取商品排序后信息 --  1热卖（销量做多的） 2热评（评论最多的）
        // 获取此商品的评价信息
        $commentList = model('Comment')->getDataList(['productId' => $proInfo['id']], 'a.*,b.nick_name,c.savePath as avatarPath', [['User b', 'a.created_user=b.id', 'left'], ['File c', 'b.avatar=c.id', 'left']], 'a.created_at desc', 4);
        $result['commentList'] = $commentList['data'];
        // 获取此商品的咨询信息
        $consulation = model('Consulation')->getDataList(['productId' => $proInfo['id']], 'a.*', [], 'a.created_at desc', 4);
        $result['consulation'] = $consulation['data'];
        return $result;
    }

    /**
     * 获取商品SKU详情数组
     * @param $data
     * @return array
     */
    private function getHashSku($data)
    {
        if (!is_array($data) || empty($data)) {
            return $data;
        }
        $sku = [];
        foreach ($data as $k => $v) {
            $curIndex['id'] = $v['attr_key_id'];
            $curIndex['name'] = $v['attr_key_name'];
            $curChild['id'] = $v['attr_value'];
            $curChild['value'] = $v['attr_key_value'];
            if (empty($sku)) {
                $curIndex['child'][] = $curChild;
                $sku[$curIndex['id']] = $curIndex;
            } else {
                $flag = true;
                foreach ($sku as $key => $item) {
                    if ($item['id'] != $v['attr_key_id']) {
                        $flag = false;
                    }
                }
                if(!$flag){
                    $curIndex['child'][] = $curChild;
                    unset($curIndex['child'][0]);
                    $sku[$curIndex['id']] = $curIndex;
                }else{
                    $sku[$curIndex['id']]['child'][] = $curChild;
                }
            }
        }
        return $sku;
    }

    /**
     * 获取销量最多
     * @param array $condition
     */
    public function getSaleList($condition = [])
    {

    }

    /**
     * 获取评论最多
     * @param array $condition
     */
    public function getCommentMaxList($condition = [])
    {

    }

    /**
     * 新增商品
     * @param $data  商品基本信息
     * @param array $attrid SKU 规格ID集
     * @param array $attrVal SKU 规格ID对应值的集
     * @return array
     * @throws \think\exception\PDOException
     */
    public function addProduct($data, $attrid = [], $attrVal = [])
    {
        $this->startTrans();
        try {
            $goodsAttr = model('Spec');
            $attrkeyTable = model('ItemAttrKey');
            $attrvalTable = model('ItemAttrVal');
            $itemSkuTable = model('ItemSku');
            if (empty($data['id'])) {
                // result就是新增后的商品ID值
                $result = $this->insert($data, false, true, null);
                if ($attrid && $attrVal) {
                    // 当选择多规格多库存多价格时，处理商品的规格库存
                    // 这里是生成了商品对应的属性名称表
                    sort($attrid);
                    $itemkeyData = [];
                    foreach ($attrid as $k => $v) {
                        $attrkeyData['attr_key_id'] = $v;
                        $attrkeyData['data_index'] = $k;
                        $attrkeyData['item_id'] = $result;
                        $attrkeyData['attr_name'] = $goodsAttr->where('id', $v)->value('name');
                        $itemkeyData[] = $attrkeyData;
                    }
                    $attrkeyTable->insertAll($itemkeyData);
                    //先获取该商品下的item_attr_key中的数据
                    //还要根据算法来生成item_attr_val表数据
                    $index = 1;
                    $groupNumber = [];
                    foreach ($itemkeyData as $k => $v) {
                        //先根据attr_key_id获取到规格下的属性值数组
                        $theCurrent = $attrVal[$v['data_index']];
                        foreach ($theCurrent as $v2) {
                            $attrvalData['attr_key_id'] = $v['attr_key_id'];
                            $attrvalData['item_id'] = $v['item_id'];
                            $attrvalData['symbol'] = $index;
                            $attrvalData['attr_value'] = $v2;
                            $attrvalTable->insert($attrvalData);
                            $groupNumber[] = $attrvalData;
                            $index++;
                        }
                    }
                    //最后再插入商品sku表
                    //还得先获取item_attr_val表中该商品的数据
                    //先找出符合条件的属性组有几个
                    //当只有一个规格时候
                    $groupNumber2 = $attrvalTable->where('item_id', $result)->group('attr_key_id')->field('attr_key_id')->order('symbol')->select();
                    //先把数据处理成属性对应多个值的数组，然后去生生成笛卡尔积
                    //\think\Log::notice('商品对应的ITEM ATTR VAL'.json_encode($groupNumber2));
                    $dikaer = array();
                    foreach ($groupNumber2 as $v) {
                        $list = array();
                        $groupData = [];
                        foreach ($groupNumber as $k3 => $v3) {
                            if ($v3['attr_key_id'] == $v['attr_key_id']) {
                                $groupData[] = $groupNumber[$k3];
                            }
                        }
                        //\think\Log::notice('单个ATTR——key_id'.json_encode($groupData));
                        foreach ($groupData as $v2) {
                            //\think\Log::notice('AAAAAAAAA'.json_encode($v2));
                            if (!in_array($v2['symbol'], $list)) {
                                array_push($list, $v2['symbol']);
                            }
                        }
                        $dikaer[] = $list;
                    }
                    //\think\Log::notice('规格书组信息'.json_encode($dikaer));
                    //获取笛卡尔积
                    $dikaer2 = $this->CartesianProduct($dikaer);
                    //\think\Log::notice('规格书组信息-dikaer'.json_encode($dikaer2));
                    //插入item_sku表
                    //throw new \Exception('aaaaa');
                    $skuLis = [];
                    foreach ($dikaer2 as $k => $v) {
                        $itemskuData['item_id'] = $result;
                        $itemskuData['attr_symbol_path'] = $v;
                        $skuLis[] = $itemskuData;
                    }
                    $itemSkuTable->insertAll($skuLis);
                }
            } else {
                $result = $this->save($data, ['id' => $data['id']]);
            }
            $this->commit();
            return ['code' => 1, 'msg' => '商品添加成功'];
        } catch (\Exception $e) {
            mdLog($e);
            $this->rollback();
            return ['code' => 0, 'msgh' => $e->getMessage()];
        }

    }


    /**
     * 获取商品属性的笛卡尔积
     * @param $sets
     * @return array
     */
    private function CartesianProduct($sets)
    {
        // 保存结果
        $result = array();
        // 循环遍历集合数据
        for ($i = 0, $count = count($sets); $i < $count - 1; $i++) {
            // 初始化
            if ($i == 0) {
                $result = $sets[$i];
            }
            // 保存临时数据
            $tmp = array();
            // 结果与下一个集合计算笛卡尔积
            foreach ($result as $res) {
                foreach ($sets[$i + 1] as $set) {
                    $tmp[] = $res . ',' . $set;
                }
            }
            // 将笛卡尔积写入结果
            $result = $tmp;
        }
        return $result;
    }

}