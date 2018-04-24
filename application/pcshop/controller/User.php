<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 15:49
 * Comment:
 */
namespace app\pcshop\controller;
use app\common\controller\Base;
use think\Request;
use think\Db;

class User extends Base
{
    public $selfModel = null;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->selfModel = model('User');
        $this->modelName = 'User';
        $this->theme = '商城用户';
        $this->order = 'a.created_at desc';
        $this->field = 'a.id,a.account_number,a.roles_id,a.franchisee_id,a.password,a.avatar,a.nick_name,a.mobile,a.type,a.real_name,b.savePath as avatarPath';
        $this->join = [['File b','a.avatar=b.id','left']];
        $this->model = model($this->modelName);
    }

    /**
     * 登录- 是否自动登录
     * @return \think\response\View
     */
    public function login()
    {
        return view('',['username'=>cookie('username'),'password'=>passport_decrypt(cookie('password'))]);
    }

    /**
     * 用户登录 加密cookie
     * @param Request $request
     * @return array
     */
    public function checkuser(Request $request)
    {
        try{
            $code = 0;
            $param = $request->only(['username','password','isRemember']);
            $result = $this->selfModel->getDataList(['account_number'=>$param['username']],$this->field,$this->join,$this->order,1);
            if($result['code'] == 0){
                throw new \think\Exception('系统出错'.$result['msg']);
            }
            if(empty($result['data'])){
                $code = 0;
                throw new \think\Exception('账号不存在');
            }
            if(md5($param['password']) != $result['data'][0]['password']){
                $code = 2;
                throw new \think\Exception('密码错误');
            }
            // 记住密码
            if(isset($param['isRemember']) && $param['isRemember'] == 1){
                // 加密
                $cookieUserPwd = passport_encrypt($param['password']);
                // 设置
                cookie('password', $cookieUserPwd, 7200);
                cookie('username', $param['username'], 7200);
            }else{
                cookie('password',null);
                cookie('username',null);
            }
            // 写入session 商城用户ID
            session('pcshopUserId',$result['data'][0]['id']);
            session('pcshopUser',$result['data'][0]);
            return ['code'=>1,'msg'=>'success'];
        }catch(\Exception $e){
            session('pcshopUserId',null);
            session('pcshopUser',null);
            mdLog($e);
            return ['code'=>$code,'msg'=>$e->getMessage()];
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('pcshopUserId',null);
        session('pcshopUser',null);
        $this->redirect(url('/pcshop'));
    }


    /**
     * 需要把数组转换下
     */
    public function transData()
    {
        $sql = 'select DISTINCT(album),id from md_product';
        $imgBox = model('Product')->query($sql);
        echo "<pre>";
        print_r($imgBox);
        foreach($imgBox as $k=>$v){
            $imgId = model('File')->insertGetId(['savePath'=>$v['album']]);
            model('Product')->save(['album'=>$imgId],['id'=>$v['id']]);
        }
    }

    public function carts()
    {
        $data = model('Carts')->getPcShopCarts(['userId'=>session('pcshopUserId')]);
        return view('',['data'=>$data['data']]);
    }

    public function orderList()
    {
        $object  = model('Order')->alias('a')->where(['userId'=>session('pcshopUserId'),'deleted_at'=>null])->field('a.id,a.product,a.tradeNumber,a.created_at,a.total,a.progress,a.status')->order('a.created_at desc')->join([])->paginate(1,false,[
            'type'     => 'Pcshoppage',
            'var_page' => 'p',
        ]);
        $return['data'] = $object->toArray();
        $return['page'] = $object->render();
        return view('',['data'=>$return['data']['data'],'page'=>$return['page']]);
    }

    public function intro()
    {
        $info = model('User')->getRow(['id'=>session('pcshopUserId')],'a.id,a.account_number,a.nick_name,a.mobile,a.email');
        return view('',['data'=>$info['data']]);
    }

    public function saveInfo(Request $request)
    {
        $data = $request->only(['id','nick_name']);
        $result = model('User')->save(['nick_name'=>$data['nick_name']],['id'=>$data['id']]);
        if($result){
            $row = model('User')->get($data['id']);
            session('pcshopUser',$row);
            return ['code'=>1,'msg'=>'信息保存成功'];
        }else{
            return ['code'=>0,'msg'=>'没有可以更改的数据'];
        }
    }
}