<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 11:38
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Brand extends Validate
{
    protected $rule =   [
        'name|品牌名称'  => 'require|unique:Category',
        //'url|品牌地址'   => '',//require
        'sort|品牌排序'   => 'require|number',
    ];

    protected $message  =   [
        'name.require' => '名称必填',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Menus','sort'],//'url',
        'update'  =>  ['name'=>'require|unique:Menus,name^id','sort'],//,'url'
    ];
}