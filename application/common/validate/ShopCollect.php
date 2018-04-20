<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 10:49
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class ShopCollect extends Validate
{
    protected $rule =   [
        'userId|收藏人'  => 'require|number',
        //'url|品牌地址'   => '',//require
        'productId|收藏商品'   => 'require|number',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'insert'  =>  ['userId','productId'=>'require|unique:ShopCollect,productId^id'],//'url',
        'update'  =>  ['userId','productId'],//,'url'
    ];
}