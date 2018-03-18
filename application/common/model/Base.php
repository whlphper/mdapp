<?php
namespace app\common\model;
use think\Model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 18:17
 */
class Base extends Model
{
    public function getBootstrapeTable($CurrentCon = array(), $field = '*', $join = [], $curOrder = '')
    {
        try{
            if (!$join || $join == '') {
                $join = array();
            }
            $condition = array_merge($CurrentCon, array('a.deleted_at' => null));
            $tableData = input("request.");
            $offset = $tableData['offset'];
            $limit = $tableData['limit'];
            unset($tableData['offset']);
            unset($tableData['limit']);
            //搜索字段
            /* if(isset($tableData['search']) && !empty($tableData['search'])){
                $condition['a.name'] = array('like','%'.$tableData['search'].'%');
            } */
            $order = 'a.created_at desc';
            if (!empty($curOrder)) {
                $order = $curOrder . ',' . $order;
            }
            //查询条件
            if (isset($tableData['sort']) && !empty($tableData['sort'])) {
                if (!empty($tableData['order'])) {
                    $order = 'a.' . $tableData['sort'] . ' ' . $tableData['order'];
                } else {
                    $order = 'a.' . $tableData['sort'] . ' asc';
                }
                unset($tableData['sort']);
            }
            unset($tableData['order']);
            //查询字段
            foreach ($tableData as $fields => $value) {
                if ($value && strpos($fields, '/') !== true) {
                    $condition['a.' . $fields] = ['like', "%$value%"];
                }
            }
            $list = self::all(function($query)use($condition,$field,$join,$order,$offset,$limit){
                $query->alias('a')->where($condition)->join($join)->order($order)->field($field)->limit($offset . ',' . $limit);
            });
            $collection = array();
            foreach($list as $k=>$v){
                $collection[] = $v->data;
            }
            $total = self::all(function($query)use($condition,$join){
                $query->alias('a')->where($condition)->join($join)->field('a.id')->count();
            });
            return ['total'=>count($total),'rows'=>$collection];
        }catch(\Exception $e){
            return ['total'=>0,'rows'=>[],'message'=>$e->getMessage()];
        }
    }
}