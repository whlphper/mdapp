<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 10:58
 * Desc:
 */
namespace app\common\model;
use app\common\model\Base;
class Record extends Base{

    public $isLimit = false;

    public function getAccountList()
    {
        $sql = 'select distinct(accountfrom) from record';
        return $this->query($sql);
    }

    public function getOrderList()
    {
        $sql = 'select distinct(ticketfrom) from record';
        return $this->query($sql);
    }

    public function getSymbolList()
    {
        $sql = 'select distinct(symbol) from record';
        return $this->query($sql);
    }
}