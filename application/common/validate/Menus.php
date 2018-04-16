<?php
namespace app\common\validate;
use think\Validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19 0019
 * Time: 21:50
 */
class Menus extends Validate
{
    protected $rule =   [
        'name|菜单名称'  => 'require|unique:Menus',
        //'url|菜单地址'   => '',//require
        'sort|菜单排序'   => 'require|number',
        'pid|父级菜单'   => 'require|number',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Menus','sort','pid'],//'url',
        'update'  =>  ['name'=>'require|unique:Menus,name^id','sort','pid'],//,'url'
    ];
}