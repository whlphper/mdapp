<?php
namespace app\common\model;
use think\Model;
use think\Db;
use think\Request;

/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 18:17
 */
class Base extends Model
{
    // 表对应需要处理图片的字段
    public function imgField($table='Advertisement')
    {
        $imgField = [
            'Product'=>['album'],
            'Advertisement'=>['poster'],
            'Brand'=>['poster'],
            'Category'=>['poster'],
            'User'=>['avatar'],
        ];
        return isset($imgField[$table]) ? $imgField[$table] : [];
    }
    /*
     * 获取表格数据
     * @ $CurrentCon 查询条件
     * @ $field      查询字段
     * @ $join       链接查询
     * @ $curOrder   排序
     */
    public function getBootstrapeTable($CurrentCon = array(), $field = 'a.*', $join = [], $curOrder = '')
    {
        try{
            if (!$join || $join == '') {
                $join = array();
            }
            $condition = array_merge($CurrentCon,[]);
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
            $list = $this->alias('a')->where($condition)->where('a.deleted_at',null)->join($join)->order($order)->field($field)->limit($offset . ',' . $limit)->select();
            /*$collation = [];
            foreach ($list as $k=>$v)
            {
                $collation[] = $list[$k]->toArray();
            }*/
            $total = $this->alias('a')->where($condition)->where('a.deleted_at',null)->join($join)->field('a.id')->count();
            $curModelImg = $this->imgField($this->name);
            return ['total'=>$total,'rows'=>$list];
        }catch(\Exception $e){
            mdLog($e);
            return ['total'=>0,'rows'=>[],'error'=>$e->getMessage()];
        }
    }

    /**
     * @param array $CurrentCon
     * @param string $field
     * @param array $join
     * @param string $curOrder
     * @return array
     */
    public function getDataList($CurrentCon = array(), $field = '*', $join = [], $curOrder = '',$limit=false)
    {
        try{
            if (!$join || $join == '') {
                $join = array();
            }
            $condition = array_merge($CurrentCon, array('a.deleted_at' => null));
            $order = 'a.created_at desc';
            if (!empty($curOrder)) {
                $order = $curOrder . ',' . $order;
            }
            if($limit){
                $list = self::all(function($query)use($condition,$field,$join,$order,$limit){
                    $query->alias('a')->where($condition)->join($join)->order($order)->field($field)->limit($limit);
                });
            }else{
                $list = self::all(function($query)use($condition,$field,$join,$order){
                    $query->alias('a')->where($condition)->join($join)->order($order)->field($field);
                });
            }
            $collection = array();
            /******当必要时候,需要处理数据中的图片 start*******/
            foreach($list as $k=>$v){
                // 是否处理图片 暂时写了单张图片
                $curModelImg = $this->imgField($this->name);
                if(!empty($curModelImg)){
                    $docRoot = str_replace('/index.php','/public',Request::instance()->root());
                    foreach($curModelImg as $key=>$value){
                        if(strpos($field,$value) !== false && $v->data[$value] && isset($v->data[$value.'Path'])){
                            $v->data[$value.'Array'][] = ['id'=>$v->data[$value],'path'=>$v->data[$value.'Path']];
                        }
                    }
                }
                $collection[] = $v->data;
            }
            /******当必要时候,需要处理数据中的图片 end*******/
            $total = self::all(function($query)use($condition,$join){
                $query->alias('a')->where($condition)->join($join)->field('a.id')->count();
            });
            return ['code'=>1,'total'=>count($total),'data'=>$collection];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'total'=>0,'data'=>[],'msg'=>$e->getMessage()];
        }
    }


    /*
     * 查询集合-公共
     */
    public function getCommonCollection($condition,$field='id',$order='',$join=[])
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
            return ['code'=>1,'msg'=>'','data'=>$collection];
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
    public function deleteData($idStr,$table,$name,$code,$old=[],$new=[])
    {
        Db::startTrans();
        try{
            if(empty($idStr)){
                throw new \Exception('删除的数据为空');
            }
            $condition['id'] = ['in',$idStr];
            $result = self::save(['deleted_at'=>date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']),'updated_at'=>date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])],function($query)use($condition){
                $query->where($condition);
            });
            if($result){
                $request = \think\Request::instance();
                $curAction = model("Menus")->getRow(['url'=>$request->path()],'id,url,name');
                if($curAction['code'] == 1){
                    $curAction = $curAction->toArray();
                    $logData['name'] = $curAction['name'];
                    $logData['code'] = $curAction['id'];
                }else{
                    $logData['name'] = $name;
                    $logData['code'] = $code;
                }
                // 记录删除日志
                $logData['created_user'] = session("userId");
                $logData['data_id'] = $idStr;
                $logData['table']= $table;
                $logData['url'] = $request->path();
                $logData['ip'] = $request->ip();
                $logData['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                if(!empty($old)){
                    $logData['old'] = serialize($old);
                }
                if(!empty($new)){
                    $logData['new'] = serialize($new);
                }
                $log = model("Log")->create($logData);
                if($log && !empty($log['id'])){
                    $return =  ['code'=>1,'msg'=>'日志记录成功','data'=>$log['id']];
                }else{
                    throw new \Exception('日志记录失败');
                }
            }else{
                $return =  ['code'=>1,'msg'=>'没有可以删除的数据'];
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
    public function saveData($data,$table,$name,$code,$old=[],$new=[])
    {
        Db::startTrans();
        try{
            if (isset($data['old']) || isset($data['new'])) {
                $old = $data['old'];
                $new = $data['new'];
                // 要unset掉
                unset($data['old']);
                unset($data['new']);
            } else {
                $old = [];
                $new = [];
            }
            $request = Request::instance();
            // 当数据操作成功 记录日志
            $logData['created_user'] = session("userId");
            $curAction = model("Menus")->getRow(['url'=>$request->path()],'id,url,name');
            if($curAction['code'] == 1){
                $logData['code'] = $curAction['data']['id'];
            }else{
                $logData['code'] = $code;
            }
            $logData['table']= strtolower($table);
            $logData['name']= $name ? $name : $this->name;
            $logData['url'] = $request->path();
            $logData['ip'] = $request->ip();
            $logData['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
            if(empty($data['id'])){
                $data['created_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $data['created_user'] = session("userId");
                if($this->name == 'User'){
                    $data['account_number'] = $data['mobile'];
                    $data['password'] = md5($data['password']);
                    $curModule = Request::instance()->module();
                    switch($curModule){
                        case 'pcshop':
                            $data['type'] = '1001004';
                    }
                }
                $result = self::create($data);
                if(empty($result->data['id'])){
                    throw new \Exception($name.'失败');
                }
                $dataId = $result->data['id'];
            }else{
                $data['updated_at'] = date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
                $result = self::allowField(true)->save($data,['id'=>$data['id']]);
                if(!$result){
                    throw new \Exception($name.'失败，没有可以更改的数据');
                }
                $dataId = $data['id'];
                // 判断是否含有差异  记录差异字段
                if($old && $new){
                    // 获取字段的注释
                    $diffInfo = $this->getSqlComment($table,$old,$new);
                    $logData['old'] = serialize($diffInfo['old']);
                    $logData['new'] = serialize($diffInfo['new']);
                }
            }
            $logData['data_id'] = $dataId;
            $log = model("Log")->create($logData);
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

    /* 获取字段的注释
     * 当创建表的时候要写清楚每个字段的注释  example：字段含义-其他备注
     * @param $table     表名称
     * @param $old       旧字段数组
     * @param $new       新字段数组
     */
    public function getSqlComment($table='',$old=[],$new=[])
    {
        if($old && $new){
            // 处理为完整表名称
            $tableArr = preg_split("/(?=[A-Z])/", $table);
            $prefix = config('database.prefix');
            foreach($tableArr as $k=>$v){
                if(empty($v)){
                    unset($tableArr[$k]);
                }else{
                    $tableArr[$k] = strtolower($v);
                }
            }
            $fulltTable = $prefix.implode("_",$tableArr);
            $fieldArr = array_keys($old);
            $fieldToComment = [];
            // 只获取字段名称数组
            $query = self::query("show full fields from ".$fulltTable);
            foreach($query as $k=>$v){
                $curField = $v['Field'];
                $curComment = explode('-',$v['Comment']);
                if(in_array($curField,$fieldArr)){
                    $fieldToComment[$curField] = $curComment[0];
                }
            }
            // 声明新旧数组 以注释作为key
            $comOld = [];
            $comNew = [];
            foreach($fieldToComment as $k=>$v){
                foreach($old as $k2=>$v2){
                    if($k == $k2){
                        $comOld[$v] = $v2;
                    }
                }
                foreach($new as $k2=>$v2){
                    if($k == $k2){
                        $comNew[$v] = $v2;
                    }
                }
            }
            $return['old'] = $comOld;
            $return['new'] = $comNew;
            return $return;
        }
    }

    /**
     * 获取一条数据
     * @param array $condition
     * @param $field
     * @param array $order
     * @param array $join
     * @return null|static
     */
    public function getRow($condition=[],$field,$join=[],$order=''){
        try{
            if(empty($condition['a.deleted_at'])){
                $condition = array_merge($condition,['a.deleted_at'=>null]);
            }
            $list = self::get(function($query)use($condition,$field,$order,$join){
                $query->alias('a')->where($condition)->order($order)->join($join)->field($field)->limit(1);
            });
            if(!$list){
                throw new \Exception('数据不存在');
            }
            $list = $list->data;
            /******当必要时候,需要处理数据中的图片 start*******/
            foreach($list as $k=>$v){
                // 是否处理图片 暂时写了单张图片
                $curModelImg = $this->imgField($this->name);
                if(!empty($curModelImg)){
                    $docRoot = str_replace('/index.php','/public',Request::instance()->root());
                    foreach($curModelImg as $key=>$value){
                        if($list[$k] && $k == $value){
                            // 获取对应的多张图片
                            $imgList = model('File')->where('id','in',$list[$k])->field('id,savePath')->select();
                            foreach($imgList as $item=>$items){
                                $list[$value.'Array'][] = ['id'=>$items['id'],'path'=>$items['savePath']];
                            }
                        }
                    }
                }
            }
            /******当必要时候,需要处理数据中的图片 end*******/
            return ['code'=>1,'msg'=>'','data'=>$list];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    /**
     * 获取一个字段值
     * @param array $condition
     * @param $field
     * @param string $order
     * @param array $join
     * @return array
     */
    public function getField($condition=[],$field,$order='',$join=[])
    {
        try{
            if(empty($condition['a.deleted_at'])){
                $condition = array_merge($condition,['a.deleted_at'=>null]);
            }
            $result = self::where($condition)->alias('a')->order($order)->join($join)->value($field);
            return ['code'=>1,'msg'=>'success','data'=>$result];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }

    /**
     * 获取一列值
     * @param array $condition
     * @param $field
     * @return array
     */
    public function getColumn($condition=[],$field)
    {
        try{
            if(empty($condition['a.deleted_at'])){
                $condition = array_merge($condition,['a.deleted_at'=>null]);
            }
            $result = self::where($condition)->alias('a')->column($field);
            return ['code'=>1,'msg'=>'success','data'=>$result];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}