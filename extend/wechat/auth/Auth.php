<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 2:28
 * Desc:
 */
namespace wechat\auth;

use think\Controller;
use think\Request;
use think\Db;
use think\Cache;

class Auth extends Controller{

    private $appid = null;
    private $appsecret = null;
    private $authnotify = null;

    public function __construct($appId,$appSecret,$notify=''){
        //获取微信所需参数
        $this->appid = $appId;//config('weixin.appId');
        $this->appsecret = $appSecret;//config('weixin.appSecret');
        // 授权的notify url
        $this->authnotify = $notify;
        parent::__construct();
    }

    /**
     * 网页授权回调接口
     * @return array|\think\response\Json
     */
    public function notify(){
        try{
            $code = input("get.code","");
            $accArr = $this->oauth2_access_token($code);
            $userInfo = $this->oauth2_get_user_info($accArr["access_token"],$accArr["openid"]);
            return ['code'=>0,'msg'=>'success','openId'=>$accArr["openid"],'baseInfo'=>$userInfo];
        }catch(\Exception $e){
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