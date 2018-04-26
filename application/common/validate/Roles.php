<?php
namespace app\common\validate;
use think\Validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29 0029
 * Time: 21:06
 */
class Roles extends Validate
{
    protected $rule =   [
        'name|角色名称'  => 'require|unique:Roles',
        'sort|角色排序'   => 'require|number',
        'morelevel|所属组'   => 'require',
        'menu_ids|角色权限' => 'require',
    ];

    protected $message  =   [
        'name.unique' => '角色名称已经被占用',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Roles','sort','menu_ids'],//'url',,'morelevel'
        'update'  =>  ['name'=>'require|unique:Roles,name^id','sort','menu_ids'],//,'url','morelevel'
    ];
}