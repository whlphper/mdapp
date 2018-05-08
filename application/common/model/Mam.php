<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 11:18
 * Desc:
 */
namespace app\common\model;
use app\common\model\Base;
class Mam extends Base{

    public $isLimit = false;

    public function getBelongList()
    {
        $sql = 'select distinct(belongto) from mam';
        return $this->query($sql);
    }

}