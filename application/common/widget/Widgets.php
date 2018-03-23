<?php
namespace app\common\widget;
use think\Controller;
class Widgets extends Controller{

    // 初始化字典下拉列表
    public function initExplain(){

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
    public function initSelect($table,$condition,$field,$order,$pField,$cField,$default)
    {
        $list = model($table)->getCommonCollection($condition,$field,$order);
        if($list['code'] == 0){
            return $list;
        }
        $list = $list['data'];
        $html = '';
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
        return $html;
    }

    public function initRegion()
    {

    }

}