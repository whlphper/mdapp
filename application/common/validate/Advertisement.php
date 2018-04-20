<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 10:36
 * Comment:
 */
namespace app\common\validate;
use think\Validate;
class Advertisement extends Validate
{
    protected $rule =   [
        'name|广告位名称'  => 'require|unique:Category',
        'url|广告位地址'   => 'checkUrlIsStation',//require
        'sort|广告位排序'   => 'require|number',
        'position|广告位位置'   => 'require|number',
        'type|广告位类型'   => 'require|number',
        'dataId|关联数据ID'   => 'checkDataIdIsStation',
    ];

    protected $message  =   [
        'name.require' => '名称必填',
    ];

    protected $scene = [
        'insert'  =>  ['name'=>'require|unique:Advertisement','url','sort','position','type','dataId'],
        'update'  =>  ['name'=>'require|unique:Advertisement,name^id','url','sort','position','type','dataId'],
    ];

    /**
     * 检查关联的数据ID/广告位地址  是否是必填的（当类型为站内必填）
     * @param $value
     */
    public function checkUrlIsStation($value)
    {
        $flag = true;
        // 1站内商品2站内分类3站内新闻4外部链接
        if($this->data['type'] == 4){
            if(empty($value)){
                $flag =  '外部链接请填写跳转地址';
            }
        }
        return $flag;
    }

    /**
     * 检查关联的数据ID/广告位地址  是否是必填的（当类型为站内必填）
     * @param $value
     */
    public function checkDataIdIsStation($value)
    {
        $flag = true;
        // 1站内商品2站内分类3站内新闻4外部链接
        if($this->data['type'] != 4){
            if(empty($value)){
                $flag =  '站内广告请选择链接的数据';
            }
        }
        return $flag;
    }

}