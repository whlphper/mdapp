<?php
namespace app\wechat\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Cache;

class Auth extends Controller
{
    private $appid = null;
    private $appsecret = null;
    private $authnotify = null;

    public function __construct(){
        parent::__construct();
        //获取微信所需参数
        /*$this->appid = config('weixin.appId');
        $this->appsecret = config('weixin.appSecret');*/
        $this->appid = 'wx0e2195a85fb69f6f';
        $this->appsecret = 'c15343dd89bb8c1f8699ef1a3c2175f4';
        // 授权的notify url
        $this->authnotify = Request::instance()->domain().url('wechat/Auth/saveUser');
    }

    /**
     * 网页授权回调接口
     * @return array|\think\response\Json
     */
    public function saveUser(){
        Db::startTrans();
        try{
            $code = input("get.code","");
            $accArr = $this->oauth2_access_token($code);
            $userInfo = $this->oauth2_get_user_info($accArr["access_token"],$accArr["openid"]);
            $data["openId"] = $userInfo["openid"];
            // user表暂时只有name  所以把微信昵称存到name字段了
            $data["name"] = $userInfo["nickname"];
            // 微信头像不知道存那个字段,暂时没存
            $data['password'] = md5('888888');
            $data['type'] = '8003002';//企业用户
            $row = findById('user',['openId'=>$userInfo["openid"],'id,openId,mobile']);
            if($row['code'] == 0){
                // 不存在此openId
            }else{
                $data['id'] = $row['data']['id'];
            }
            $result = saveData('user',$data);
            if($result['code'] == 0){
                throw new \think\Exception('用户保存失败'.$result['msg']);
            }
            // 设置cookie
            cookie("openId",$data["openId"]);
            cookie("userId",$result['data']);
            // 下面是测试信息 测试完删了就好
            $noyify = '<h1>欢迎 '.$userInfo["nickname"].'; 你的openId=>'.$userInfo['openid'].'</h1>';
            Db::rollback();
            echo $noyify;
            exit;
            return ['code'=>0,'msg'=>'用户信息记录成功','data'=>$result['data']];
        }catch(\Exception $e){
            Db::rollback();
            c_Log($e);
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /***
     * 生成OAuth2的Access Token
     * @param $code
     * @return mixed
     */
    public function oauth2_access_token($code)
    {
        // access token 缓存7200
        if(!Cache::get('access_token')){
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=".$code."&grant_type=authorization_code";
            $res = $this->http_request($url);
            $access = json_decode($res, true);
            Cache::set('access_token',$access,'7200');
            return $access;
        }else{
            return Cache::get('access_token');
        }
    }

    /**
     * //获取用户基本信息
     * @param $access_token
     * @param $openid
     * @return mixed
     */
    public function oauth2_get_user_info($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }


    /**
     * HTTP请求（支持HTTP/HTTPS，支持GET/POST）
     * @param $url
     * @param null $data
     * @return mixed
     */
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 当用户未授权调用
     * 暂时判断的是cookie('openId')
     */
    public function oauth2_access(){
        $rec_url = urlencode($this->authnotify);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$rec_url."&oauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        $this->redirect($url);
    }

}
