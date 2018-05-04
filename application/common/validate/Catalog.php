<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 11:26
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Catalog extends Validate
{
    protected $rule =   [
        'title|帮助标题'  => 'require|unique:Category',
        //'url|品牌地址'   => '',//require
        'sort_order|排序'   => 'number',
    ];

    protected $message  =   [
        'name.require' => '名称必填',
    ];

    protected $scene = [
        'insert'  =>  ['title'=>'require|unique:Catalog','sort'],//'url',
        'update'  =>  ['title'=>'require|unique:Catalog,title^id','sort'],//,'url'
    ];
}