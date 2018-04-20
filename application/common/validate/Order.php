<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 12:47
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Order extends Validate
{
    protected $rule =   [
        'userId|用户'  => 'require|number',
        'productId|购物车商品'   => 'require|number',
        'number|商品数量'   => 'require|number',
        'addressId|收货信息'   => 'require',
        'payType|支付方式'   => 'require',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'insert'  =>  ['userId','productId'=>'require|unique:Carts,productId^userId','number'],//'url',
        'update'  =>  ['addressId','payType'],//,'url'
    ];
}