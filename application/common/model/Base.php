<?php
namespace app\common\model;
use think\Model;
use think\Request;
use think\Db;

/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 18:17
 */
class Base extends Model
{
    /*
     * 获取表格数据
     * @ $CurrentCon 查询条件
     * @ $field      查询字段
     * @ $join       链接查询
     * @ $curOrder   排序
     */
    public function getBootstrapeTable($CurrentCon = array(), $field = '*', $join = [], $curOrder = '')
    {
        try{
            if (!$join || $join == '') {
                $join = array();
            }
            $condition = array_merge($CurrentCon, array('a.deleted_at' => null));
            $tableData = input("request.");
            if(isset($tableData['_tm'])){
                unset($tableData['_tm']);
            }
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
            return ['total'=>0,'rows'=>[],'error'=>$e->getMessage()];
        }
    }


    /*
     * 查询集合-公共
     */
    public function getCommonCollection($condition,$field,$order,$join=[])
    {
        try{
            if(empty($condition['a.deleted_at'])){
                $condition = array_merge($condition,['a.deleted_at'=>null]);
            }
            $list = self::all(function($query)use($condition,$field,$order,$join){
                $query->alias('a')->where($condition)->order($order)->join($join)->field($field);
            });
            $collection = array();
            foreach($list as $k=>$v){
                $collection[] = $v->data;
            }
            return ['code'=>1,'msg'=>'ok','data'=>$collection];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    /*
     * 软删除 会生成删除记录 log表
     * @return boolean
     * @param $condition 删除条件
     */
    public function deleteData($idStr,$name,$code,$old=[],$new=[])
    {
        Db::startTrans();
        try{
            $condition['id'] = ['in',$idStr];
            $result = self::save(['deleted_at'=>date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),'updated_at'=>date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])],function($query)use($condition){
                $query->where($condition);
            });
            if($result){
                // 记录删除日志
                $logData['created_user'] = session("userId");
                $logData['data_id'] = $idStr;
                $logData['name'] = $name;
                $logData['code'] = $code;
                $logData['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                if(!empty($old)){
                    $logData['old'] = serialize($old);
                }
                if(!empty($new)){
                    $logData['new'] = serialize($new);
                }
                $log = model("Log")::create($logData);
                if($log && !empty($log['id'])){
                    $return =  ['code'=>1,'msg'=>'日志记录成功','data'=>$log['id']];
                }else{
                    throw new \Exception('日志记录失败');
                }
            }else{
                throw new \think\Exception('数据删除失败');
            }
            Db::commit();
            return $return;
        }catch(\Exception $e){
            Db::rollback();
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    /*
     * 保存数据 同时生成修改记录
     * @param $data 要保存的数据
     * @param $name 操作名称
     * @param $code 日志代码
     * @param $old  旧字段
     * @param $new  新字段
     */
    public function saveData($data,$name,$code,$old=[],$new=[])
    {
        Db::startTrans();
        try{
            // 当数据操作成功 记录日志
            $logData['created_user'] = session("userId");
            $logData['name'] = $name;
            $logData['code'] = $code;
            $logData['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            if(empty($data['id'])){
                $result = self::create($data);
                if(empty($result->data['id'])){
                    throw new \Exception($name.'失败');
                }
                $dataId = $result->data['id'];
            }else{
                $result = self::allowField(true)->save($data,['id'=>$data['id']]);
                if(!$result){
                    throw new \Exception($name.'失败，没有可以更改的数据');
                }
                $dataId = $data['id'];
                // 判断是否含有差异  记录差异字段
                if($old && $new){
                    $logData['old'] = serialize($old);
                    $logData['new'] = serialize($new);
                }
            }
            $logData['data_id'] = $dataId;
            $log = model("Log")::create($logData);
            if(empty($log->data['id'])){
                throw new \Exception('日志记录失败');
            }
            $return =  ['code'=>1,'msg'=>$name.'成功','data'=>$dataId];
            Db::commit();
            return $return;
        }catch(\Exception $e){
            Db::rollback();
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}