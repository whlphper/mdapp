<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/16 0016
 * Time: 下午 12:55
 * Desc:
 */
namespace app\chat\controller;
use app\common\controller\Base;
use think\Request;
use Workerman\Worker;
use GatewayWorker\Register;
class Index extends Base{

    public function index()
    {
        if(empty($_GET['userId'])){
            $this->assign('userId',1);
            $this->assign('name','adminChater1');
            $this->assign('face','http://www.iubotest.com/public/weiubo/images/userimg.png');
        }else{
            $this->assign('userId',$_GET['userId']);
            $this->assign('name','adminChater-'.$_GET['userId']);
            $this->assign('face','http://www.iubotest.com/public/weiubo/images/userimg.png');
        }
        return view();
    }

    public function freindList(){
        return [
            'status'=>1,
            "msg"=>'ok',
            'data'=>[
                [
                    "name"=>'在线好友',
                    'nums'=>29,
                    'id'=>1,
                    'item'=>[
                        [
                            'id'=>'1212',
                            "face"=>"http://tp3.sinaimg.cn/3850354634/180/40048989275/0",
                            'name'=>'test',
                        ],
                    ],
                ]
            ],
        ];
    }
}