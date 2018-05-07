<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 15:27
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class History extends Base{

    public $isLimit = false;

    public function getAccountList()
    {
        $sql = 'select distinct(account) from history';
        return $this->query($sql);
    }
}