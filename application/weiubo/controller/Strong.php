<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 4:01
 * Desc:
 */
namespace app\weiubo\controller;
use app\common\controller\Base;

class Strong extends Base{



    public function __construct()
    {
        parent::__construct();
        $this->modelName = 'Accinfo';
        $this->theme = '牛人信息';
        $this->model = model($this->modelName);
        $this->assign('theme',$this->theme);
    }

    public function info($account)
    {
        // 牛人基本信息
        $data = $this->model->where('account',$account)->find()->toArray();
        // 牛人头像
        $headimg = model('Mam')->alias('a')->where('account',$account)->field('b.headimgurl')->join([['Openid b','a.openid=b.openid','left']])->find();
        $data['headimgurl'] = $headimg['headimgurl'];
        // 10条操作记录
        $this->getLog($account);
        // 增长率曲线
        $growth = model('History')->getRate($account,'growth');
        $growthX = [];
        $growthY = [];
        foreach ($growth as $k=>$v){
            $growthX[] = $v['timeclose'];
            $growthY[] = $v['growth'];
        }
        return view('',['data'=>$data,'growth'=>$growth,'growthX'=>json_encode($growthX),'growthY'=>json_encode($growthY)]);
    }

    public function getLog($account)
    {
        $offset = input("offset") ? input("offset") : 0;
        $limit = input("limit") ? input("limit") : 10;
        // 牛人操作历史记录
        $log = model('History')->listData(['account'=>$account],'a.*','',[],$offset,$limit);
        $this->assign('offset',$offset);
        $this->assign('limit',$limit);
        $this->assign('account',$account);
        //$log['offset'] = $offset+$limit;
        if(!empty($log['data'])){
            $this->assign('log',$log['data']);
            return $log['data'];
        }
    }

    public function allRecord($account)
    {
        $this->getLog($account);
        return view('');
    }
}