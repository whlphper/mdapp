// 清除缓存命令 ` php think clear`


// 生成数据表字段缓存  这样可以优化下查询  ` php think optimize:schema`

// 生成类库的映射文件  提高系统的加载性能   `php think optimize:autoload`

// 生成控制器        `php think make:controller index/Index  --plain`

// 生成模型          `php think make:model      index/Index`

// 因为在系统中可能会需要一些大批量数据出来,避免宕机,应该用命令行来

// 下面记录下自定义命令行  对应文件为 **\application\console\controller**


// 之前SKU生成思路

```**$attrid = substr(input("attrid"),0,strlen(input("attrid"))-1);
$data['attr'] = $attrid;
$attrid = explode(';',$attrid);
dump($attrid);exit;
//获取图片路径
$data['image'] = input("image");
//print_r($data);exit;
$id = input("id");
if(empty($id)){
//result就是新增后的商品ID值
$result = $model->insert($data,false,true,null);
//当选择多规格多库存多价格时，处理商品的规格库存
//这里是生成了商品对应的属性名称表
foreach($attrid as $v){
    $attrkeyData['attr_key_id'] = $v;
    $attrkeyData['item_id'] = $result;
    $attrkeyData['attr_name'] = $goodsAttr->getOne($v);
    $attrkeyTable->insert($attrkeyData);
}
//先获取该商品下的item_attr_key中的数据
//还要根据算法来生成item_attr_val表数据
$index = 1;
$itemkeyData = $attrkeyTable->where('item_id',$result)->order('attr_key_id')->select();
foreach($itemkeyData as $k=>$v){
    //先根据attr_key_id获取到规格下的属性值数组
    $theCurrent = $goodsAttr->getOne($v['attr_key_id'],true);
    foreach($theCurrent as $v2){
        $attrvalData['attr_key_id'] = $v['attr_key_id'];
        $attrvalData['item_id'] = $v['item_id'];
        $attrvalData['symbol'] = $index;
        $attrvalData['attr_value'] = $v2;
        $attrvalTable->insert($attrvalData);
        $index++;
    }
}
//最后再插入商品sku表
//还得先获取item_attr_val表中该商品的数据
//先找出符合条件的属性组有几个
$groupNumber = $attrvalTable->where('item_id',$result)->group('attr_key_id')->field('attr_key_id')->order('symbol')->select();
//当只有一个规格时候
if(count($attrid) == 1){
    $groupData = $attrvalTable->where('attr_key_id',$v['attr_key_id'])->where('item_id',$result)->field('symbol')->select();
    foreach ($groupData as $key => $value) {
        $itemskuData['item_id'] = $result;
        $itemskuData['attr_symbol_path'] = $value['symbol'];
        $itemSkuTable->insert($itemskuData);
    }
}else{
    //先把数据处理成属性对应多个值的数组，然后去生生成笛卡尔积
    $dikaer = array();
    foreach($groupNumber as $v){
        $list = array();
        $groupData = $attrvalTable->where('attr_key_id',$v['attr_key_id'])->where('item_id',$result)->field('symbol')->select();
        foreach($groupData as $v2){
            if(!in_array($v2['symbol'],$list)){
                array_push($list,$v2['symbol']);
            }
        }
        $dikaer[] = $list;
    }
    //获取笛卡尔积
    $dikaer2 = $this->CartesianProduct($dikaer);
    //插入item_sku表
    foreach($dikaer2 as $k=>$v){
        $itemskuData['item_id'] = $result;
        $itemskuData['attr_symbol_path'] = $v;
        $itemSkuTable->insert($itemskuData);
    }
}**```