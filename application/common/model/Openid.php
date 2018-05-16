<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 3:10
 * Desc:
 */
namespace app\common\model;
use app\common\model\Base;
class Openid extends Base{

    /*
     * 新增微信用户
     */
    public function addOpenId($openId,$nickname,$head)
    {
        if(empty($openId)){
            throw new \Exception('openId为空');
        }
        $data['openid'] = $openId;
        $data['nickname'] = $nickname;
        $data['headimgurl'] = $head;
        if($this->where('openid',$openId)->find()){
            $result = $this->save($data,['openid'=>$openId]);
        }else{
            $result = $this->insert($data);
        }
        return true;
    }
}