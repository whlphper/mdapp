<?php
namespace app\common\model;
use app\common\model\Base;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 10:40
 */
class Menus extends Base
{
    /*
     * 获取菜单列表,url为处理过后的地址
     * @param $idStr 菜单string
     * */
    public function getList($idStr)
    {

        $list = Menus::all(function($query)use($idStr){
            //->where('level','<',2)
            if(session("user.type") == '1001001'){
                $query->alias('a')->where('a.deleted_at',null)->where('is_open',1)->where('is_menu',1)->order('sort', 'desc')->field('a.id,a.name,a.flag,a.url,a.pid');
            }else{
                $query->alias('a')->where('a.deleted_at',null)->where('a.id','in',$idStr)->where('is_open',1)->where('is_menu',1)->order('sort', 'desc')->field('a.id,a.name,a.flag,a.url,a.pid');
            }
        });
        $collection = array();
        foreach($list as $k=>$v){
            // 处理url
            $v->data['url'] = url($v->data['url']);
            $collection[] = $v->data;
        }
        return $collection;
    }


}