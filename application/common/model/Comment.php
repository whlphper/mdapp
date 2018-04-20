<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 8:53
 * Comment:
 */
namespace app\common\model;
use app\common\model\Base;
class Comment extends Base{

    public $adOrder = 'a.created_at desc';
    public $adField = 'a.*';
    public $adJoin = [];

}