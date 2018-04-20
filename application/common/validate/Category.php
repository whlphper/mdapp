<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/17 0017
 * Time: 10:39
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule =   [
        'name|分类名称'  => 'require|unique:Category',
        //'url|分类地址'   => '',//require
        'sort|分类排序'   => 'require|number',
        'pid|父级分类'   => 'require|number',
    ];

    protected $message  =   [
        'name.require' => '名称必填',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Menus','sort','pid'],//'url',
        'update'  =>  ['name'=>'require|unique:Menus,name^id','sort','pid'],//,'url'
    ];
}