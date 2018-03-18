<?php
namespace app\common\model;
use think\Model;
use app\common\model\Base;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18 0018
 * Time: 9:56
 */
class Roles extends Base
{
    /*
     * 获取menu_ids
     * @param  数据ID
     * @return 菜单id
     * */
    public function getMenuStr($id)
    {
        $menu = Roles::get($id);
        if(!$menu){
            return ['code'=>0,'msg'=>'数据不存在'];
        }
        return $menu->data;
    }
}
