<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 9:04
 * Desc:
 */
namespace app\common\model;
use app\common\model\Base;

class Task extends Base{

    public function getAllowAttr($value)
    {
        $status = [null=>'<p style="color: darkred">未通过</p>',0=>'<p style="color: darkred">未通过</p>',1=>'<p style="color: darkgreen">通过</p>'];
        return $status[$value];
    }

    public function upAllowStatus($autoId,$status=1,$reason=null)
    {
       $result = $this->save(['allow'=>$status,'reason'=>$reason],['autoid'=>$autoId]);
    }
}