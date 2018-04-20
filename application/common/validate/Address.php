<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 14:01
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Address extends Validate
{
    protected $rule =   [
        'userId|用户'  => 'require',
        'region|省市区'   => 'require',
        'reciver|收货人'   => 'require',
        'mobile|手机号'   => 'require|mobile',
        'isDefalt|是否默认'   => 'number',
        'address|详细地址'   => 'require',
    ];

    protected $message  =   [

    ];

    protected $scene = [
        'insert'  =>  ['reciver'=>'require|unique:Address,reciver^userId','userId','mobile','address'],//'url','region',
        'update'  =>  ['reciver'=>'require|unique:Address,reciver^id','userId','mobile','address'],//,'url','region',
    ];
}