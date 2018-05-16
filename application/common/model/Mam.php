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

    public function getAccList()
    {
        $sql = 'select distinct(account) from mam';
        return $this->query($sql);
    }

    public function getUserInfo($openId)
    {
        if(empty($openId)){
            //throw new \Exception('用户信息不存在');
        }
        // 属于此openid的多个账户
        $baseInfo = $this->alias('a')->where('a.openId',$openId)->field('a.autoid,a.account,a.belongto,a.multiple,b.account as follow,c.balance')->join([['Mam b','a.belongto=b.account','left'],['Mtaccount c','a.account=c.account','left']])->select()->toArray();

        foreach ($baseInfo as $k=>$v)
        {
            // 当前账户的持仓记录
            $balance = model('Position')->where('account',$v['account'])->select()->toArray();
            $baseInfo[$k]['list'] = $balance;
        }
        // 获取账号记录
        return $baseInfo;
    }
}