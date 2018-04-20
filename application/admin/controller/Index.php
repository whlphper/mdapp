<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\Request;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 11:51
 */
class Index extends Base{

    public function test()
    {
        dump(model('Roles')->getField(['id'=>1],'name'));
        exit;
        $curAction = model("Menus")->getRow(['url'=>Request::instance()->path()],'id,url,name');
        if($curAction){
            $curAction = $curAction->toArray();
        }
        echo "<pre>";
        print_r($curAction);
    }
    //当第一次跑的时候,获取到信息
    public function init()
    {
        $category = [];
        // 获取所有
        $allExplain = model("Dict")->getCommonCollection(['is_open'=>1],'id,name,code,level','a.code asc');
        if($allExplain['code'] == 0){
            return ['code'=>0,'msg'=>$allExplain['msg']];
        }
        $allExplain = $allExplain['data'];
        // 处理数据
        foreach($allExplain as $k=>$v){
            if($v['level'] == 1){
                $curCategory = [];
                $curCategory['sign'] = $v['code'];
                unset($allExplain[$k]);
                foreach ($allExplain as $key=>$item)
                {
                    if(substr($item['code'],0,4) == $v['code'] && $item['level'] > 1){
                        $curNode = [];
                        $curNode['name'] = $item['name'];
                        $curNode['code'] = $item['code'];
                        $curCategory['node'][] = $curNode;
                    }
                }
                $category[] = $curCategory;
            }
        }
        $path = ROOT_PATH.'/public/dict';
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $file = $path.'/dict.js';
        file_put_contents($file,'var allExplain='.json_encode($category));
    }

    /*
     * 初始化select
     * @param $table 表名
     * @param $condition 查询条件
     * @param $field     查询字段
     * @param $pField    父级字段名称
     * @param $cField    子级字段名称
     * @param $default   初始化值
     * */
    public function initSelect($table,$condition,$field,$order,$pField='id',$cField='pid',$default='')
    {
        if($condition == ''){
            $condition = [];
        }
        if(is_array($condition) && !empty($condition)){
            $conditionBack[$condition[0]] = $condition[1];
        }else{
            $conditionBack = [];
        }
        $list = model($table)->getCommonCollection($conditionBack,$field,$order);
        if($list['code'] == 0){
            return $list;
        }
        $list = $list['data'];
        $html = '<option value="">请选择</option><option value="0">顶级</option>';
        if(strpos($field,'level')){
            $list = getSubs($list,0,1,$cField);
        }
        foreach($list as $k=>$v){
            if($default == $v['id']){
                $html = $html . '<option value="'.$v['id'].'" selected>'.$v['name'].'</option>';
            }else{
                $html = $html . '<option value="'.$v['id'].'">'.$v['name'].'</option>';
            }
        }
        echo $html;
    }

    public function uploadImage($folder = "default", $extType = "image", $defaultSize = 15)
    {
        $result = upload($folder,$extType,$defaultSize);
        return $result;
    }
}