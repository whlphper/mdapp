<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 下午 2:33
 * Desc:
 */
namespace app\weiubo\controller;
use app\common\controller\Base;

class Follow extends Base{

    public function index($name,$account)
    {
        return view('',['bigName'=>$name,'account'=>$account]);
    }

    public function sure()
    {
        try{
            $data = $this->request->post();
            if(!empty($data['action']) && $data['action'] == 'joinStrong'){
                $valiMsg = $this->validate($data,'Task.followStrong');
                unset($data['action']);
                $msg = '您的申请已经提交,请等待审核';
            }else{
                $valiMsg = $this->validate($data,'Task.follow');
                $msg = '跟单成功,请等待审核';
            }
            if($valiMsg !== true){
                throw new \Exception($valiMsg);
            }
            unset($data['verifycode']);
            $result = model('Task')->insert($data);
            return ['code'=>1,'msg'=>$msg];
        }catch(\Exception $e){
            mdLog($e);
            return ['code'=>0,'msg'=>$e->getMessage()];
        }
    }
}