<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 14:16
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Product extends Validate
{
    protected $rule =   [
        'name|商品名称'  => 'require|unique:Category',
        'sort|商品排序'   => 'require|number',
        'stock|商品库存'   => 'number',
        'album|商品图片'   => 'require',
        'shopPrice|店内价格'   => 'decimal',
        'marketPrice|市场价格'   => 'decimal',
        'categoryId|分类'   => 'require',
        'brandId|品牌'   => 'require',
        'type|类型'   => 'require',
        'status|状态'   => 'require',
    ];

    protected $message  =   [
        'name.require' => '商品名称',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Product','album','sort','stock'=>'checkSku|number','shopPrice'=>'checkSku|decimal','marketPrice'=>'checkSku|decimal','caregoryId','brandId','type','status'],//'url',
        'update'  =>  ['name'=>'require|unique:Product,name^id','album','sort','stock'=>'checkSku|number','shopPrice'=>'checkSku|decimal','marketPrice'=>'checkSku|decimal','caregoryId','brandId','type','status'],//,'url'
    ];

    public function checkSku($value)
    {
        if(!empty($this->data['extraData'])){
            return true;
        }else{
            if($value <= 0){
                return '请填写数字';
            }
            return true;
        }
    }
}