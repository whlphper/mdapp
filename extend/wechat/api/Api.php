<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 3:37
 * Desc:
 */
namespace wechat\api;

class Api{
    // 定义配置项
    public $config=array(
        'APPID'              => 'wxc6d2ab98c8f72b1d', // 微信支付APPID
        // 'MCHID'              => '', // 微信支付MCHID 商户收款账号
        // 'KEY'                => '', // 微信支付KEY
        'APPSECRET'          => '3ab8e74d4bcc465dc1d435159eda39e5',  //小程序secert
        'TOKEN'              => 'mengd',
    );
    // access_token
    private $access_token = null;
    // 构造函数
    public function __construct(){
        //获取access_token,如果缓存失效重新curl并设置缓存
        if(!cache('wx_access_token')){
            $this->get_access_token(true);
        }else{
            $this->access_token = cache('wx_access_token');
        }
    }

    /**
     * 获取access_token
     * @param  boolean $isCache [缓存是否失效]
     * @return [type]           [设置类属性]
     * 调用所有api的凭证
     */
    private function get_access_token($isCache=false){

        // 当缓存失效后,重新写入缓存文件
        if($isCache){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->config['APPID']."&secret=".$this->config['APPSECRET'];
            $result = $this->httpGet($url);
            if(isset($result['errcode'])){
                echo $result['errmsg'];
                exit;
            }
            // 写入缓存
            $options = [
                // 缓存类型为File
                'type'   => 'File',
                // 缓存有效期为永久有效
                'expire' => 7200,
                // 指定缓存目录
                'path'   => APP_PATH . 'runtime/access_toke_cache/',
            ];
            // 缓存初始化
            // 不进行缓存初始化的话，默认使用配置文件中的缓存配置
            cache($options);
            // 设置缓存数据
            cache('wx_access_token', $result['access_token'], 3600);
            $this->access_token = trim($result['access_token']);
        }
    }

    /**
     * 微信公众号创建菜单
     * @param  array  $menuArr [菜单数组]
     * @return [boolean/string]  [成功/失败信息]
     *
    1、自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。
    2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。
    3、创建自定义菜单后，菜单的刷新策略是，在用户进入公众号会话页或公众号profile页时，如果发现上一次拉取菜单的请求在5分钟以前，就会拉取一下菜单，如果菜单有更新，就会刷新客户端的菜单。测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。
    数组的形式就如上所示,然后转化为json,push给微信
     *
     */
    public function creatWxmenu($menuArr=array()){
        if(!is_array($menuArr)){
            return false;
        }
        $menuArr = '{
                     "button":[
                     {    
                          "type":"view",
                          "name":"微商城",
                          "url":"http://jlyx.ibicw.cn/Home"
                     },
                     {    
                          "name":"菜单",
                           "sub_button":[
                           {    
                               "type":"view",
                               "name":"搜索",
                               "url":"http://www.soso.com/"
                            },
                            {
                               "type":"click",
                               "name":"赞一下我们",
                               "key":"V1001_GOOD"
                            }]
                     }
                    ]
                 }';

        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
        $result = $this->httpPost($url,$menuArr);
        dump($result);exit;
        if($result['errcode'] == 0){
            return true;
        }
        return $result['errmsg'];
    }

    /**
     * 获取公众号自定义菜单
     * @return [array] [菜单数组]
     */
    public function getWxmenu(){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$this->access_token;
        $result = $this->httpGet($url);
        return $result;
    }

    /**
     * 自定义菜单查询接口
     * @return [array] [返回数据,$result[errcode]=0代表删除成功]
     */
    public function delteWxmenu(){
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$this->access_token;
        $result = $this->httpGet($url);
        return $result;
    }

    //自定义菜单事件推送,没理解,回头写
    public function menuToadmin(){

    }

    //获取自定义菜单配置接口
    /*
	    is_menu_open	菜单是否开启，0代表未开启，1代表开启
		selfmenu_info	菜单信息
		button	菜单按钮
		type	菜单的类型，公众平台官网上能够设置的菜单类型有view（跳转网页）、text（返回文本，下同）、img、photo、video、voice。使用API设置的则有8种，详见《自定义菜单创建接口》
		name	菜单名称
		value、url、key等字段	对于不同的菜单类型，value的值意义不同。官网上设置的自定义菜单：
		Text:保存文字到value； Img、voice：保存mediaID到value； Video：保存视频下载链接到value； News：保存图文消息到news_info，同时保存mediaID到value； View：保存链接到url。
		使用API设置的自定义菜单： click、scancode_push、scancode_waitmsg、pic_sysphoto、pic_photo_or_album、	pic_weixin、location_select：保存值到key；view：保存链接到url
		news_info	图文消息的信息
		title	图文消息的标题
		digest	摘要
		author	作者
		show_cover	是否显示封面，0为不显示，1为显示
		cover_url	封面图片的URL
		content_url	正文的URL
		source_url	原文的URL，若置空则无查看原文入口
     */
    public function getWxmenuConfig(){
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token='.$this->access_token;
        $result = $this->httpGet($url);
        return $result;
    }

    /**
     * 获取用户基本信息
     * @param  [string] $openid [用户在公众号的唯一标识]
     * @return [array]         [用户信息]
     * subscribe	用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
    openid	用户的标识，对当前公众号唯一
    nickname	用户的昵称
    sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
    city	用户所在城市
    country	用户所在国家
    province	用户所在省份
    language	用户的语言，简体中文为zh_CN
    headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
    subscribe_time	用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
    unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
    remark	公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
    groupid	用户所在的分组ID（兼容旧的用户分组接口）
    tagid_list	用户被打上的标签ID列表
     */
    public function getUserOpeninfo($openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = $this->httpGet($url);
        return json_encode($result,true);
    }

    /**
     * 公众号用户数据分析
     * @param  [string] $start [开始时间]
     * @param  [string] $end   [结束时间]
     * @return [array]         [数据数组]
     */
    public function userCount($start,$end){
        if(empty($start) || empty($end)){
            return;
        }
        $data['begin_date'] = $start;
        $data['end_date'] = $end;
        $data = json_encode($data);
        $url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$this->access_token;
        $result = $this->httpPost($url,$data);
    }

    /**
     * 图文数据分析
     * @param  string $action   [对应微信pai]
     * @param  [string] $start  [开始时间]
     * @param  [string] $end    [结束时间]
     * @return [array]          [数据数组]
     * 获取图文群发每日数据（getarticlesummary）	1
     * 获取图文群发总数据（getarticletotal）		1
     * 获取图文统计数据（getuserread）				3
     * 获取图文统计分时数据（getuserreadhour）		1
     * 获取图文分享转发数据（getusershare）			7
     * 获取图文分享转发分时数据（getusersharehour） 1
     */
    public function imagetextCount($action="",$start,$end){
        if(empty($action) || empty($start) || empty($end)){
            return '缺少必要参数';
        }
        $data['begin_date'] = $start;
        $data['end_date'] = $end;
        $data = json_encode($data);
        $url = 'https://api.weixin.qq.com/datacube/'.$action.'?access_token='.$this->access_token;
        $result = $this->httpPost($url,$data);
    }

    /**
     * 消息数据分析
     * @param  string $action   [对应微信pai]
     * @param  [string] $start  [开始时间]
     * @param  [string] $end    [结束时间]
     * @return [array]          [数据数组]
     * 获取消息发送概况数据（getupstreammsg）		7
     * 获取消息分送分时数据（getupstreammsghour）	1
     * 获取消息发送周数据（getupstreammsgweek）		30
     * 获取消息发送月数据（getupstreammsgmonth）	30
     * 获取消息发送分布数据（getupstreammsgdist）	15
     * 获取消息发送分布周数据（getupstreammsgdistweek）30
     * 获取消息发送分布月数据（getupstreammsgdistmonth）30
     */
    public function mssageCount($action="",$start,$end){
        if(empty($action) || empty($start) || empty($end)){
            return '缺少必要参数';
        }
        $data['begin_date'] = $start;
        $data['end_date'] = $end;
        $data = json_encode($data);
        $url = 'https://api.weixin.qq.com/datacube/'.$action.'?access_token='.$this->access_token;
        $result = $this->httpPost($url,$data);
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function toXml($data){
        if(!is_array($data) || count($data) <= 0){
            throw new WxPayException("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    //上传图文素材
    public function addSourceMaterial($data)
    {
        if(!$data && !is_array($data)){
            return false;
        }
        $url  = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$this->access_token;
        $result = $this->httpPost($url,json_encode($data));
    }

    //新增其他类型永久素材
    ////媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
    public function addMaterial($data,$type='image')
    {
        $url  = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->access_token.'&type='.$type;
        $result = $this->httpPost($url,$data);
        return $result;
    }

    /**
     * @param $url
     * @param $data
     * @return mixed
     */
    private function httpPost($url,$data){
        //初始化
        $ch = curl_init();
        //设置参数
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        //采集
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            dump(curl_error($ch));
        }
        //关闭
        curl_close($ch);
        $res = json_decode($output,true);
        return $res;
    }


    /**
     * @param $url
     * @return mixed
     */
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        /*curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);*/
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        if(curl_errno($curl)){
            dump(curl_error($curl));
        }
        curl_close($curl);
        return json_decode($res,true);
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->config['TOKEN'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg($type='text',$keyword,$fromUsername,$toUsername,$time)
    {
        $textTpl = "<xml>
	                <ToUserName><![CDATA[%s]]></ToUserName>
	                <FromUserName><![CDATA[%s]]></FromUserName>
	                <CreateTime>%s</CreateTime>
	                <MsgType><![CDATA[%s]]></MsgType>
	                <Content><![CDATA[%s]]></Content>
	                <FuncFlag>0</FuncFlag>
	                </xml>";

        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $type, $keyword);
        echo $resultStr;
    }

}