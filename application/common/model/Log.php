<?php

namespace app\common\model;
use app\common\model\Base;
use think\Model;

class Log extends Base
{
    /*
     * 获取日志表信息
     * */
    public function getLogRecord($table=false,$code=false,$userId=false)
    {
        try{
            $condition = [];
            if($table){
                $table = strtolower($table);
                $condition['a.table'] = $table;
            }
            if($code){
                $condition['a.code'] = $code;
            }
            if($userId){
                $condition['a.created_user'] = $userId;
            }
            $field = 'a.id,a.name,a.code,data_id,created_at,updated_at,old,new,table';
            $list = self::all(function($query)use($condition,$field){
                $query->alias('a')->where($condition)->field($field);
            });
            $collection = array();
            foreach($list as $k=>$v){
                $collection[] = $v->data;
            }
            return ['code'=>1,'msg'=>'','data'=>$collection];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }


}
