<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/9 0009
 * Time: 下午 3:35
 * Desc:
 */
namespace app\weapi\controller;
use wechat\api\Api;
class Index extends Api{
    // openid
    public $fromUsername = null;
    // appid
    public $toUsername = null;
    //AOcw5VYjuM2yv4Z939iw6TLYxFBpbJUF0oraA6a7Blf  EncodingAESKey
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (isset($_GET['echostr'])) {
            $this->valid();
        }
        // 用php://input 代替 $GLOBALS['HTTP_RAW_POST_DATA']
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            \think\Log::notice($postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            // openid
            $fromUsername = $postObj->FromUserName;
            $this->fromUsername = $fromUsername;
            // wpmid
            $toUsername = $postObj->ToUserName;
            $this->toUsername = $toUsername;
            $time = time();
            $Event = $postObj->Event;
            //获取消息ID
            if($Event == 'MASSSENDJOBFINISH')
                $MsgId = $postObj->MsgID;
            else
                $MsgId = $postObj->MsgId;

            $MsgId = htmlentities($MsgId);

            //通过消息ID，进行消息队列处理
            if(session($MsgId)){
                exit();
            }
            session($MsgId,'lockTeam');
            $default = "亲，没有找到您想要的";
            switch($Event){
                case "subscribe"://订阅
                    //关注后的默认业务
                    $this->subscribe($fromUsername,$fromUsername,$toUsername,$time);
                    break;

                case "unsubscribe":
                    $this->onSubscribe($fromUsername);
                    break;

                case "SCAN"://用户已关注，扫描二维码

                    break;

                case "LOCATION":
                    $Latitude = trim($postObj->Latitude);//xaxis 维度
                    $Longitude = trim($postObj->Longitude);//yaxis 精度
                    $Precision = trim($postObj->Precision);
                    $this->saveLocation($fromUsername,$Latitude,$Longitude);
                    $this->responseMsg('text','位置绑定成功',$fromUsername,$toUsername,$time);
                    break;

                case "CLICK":
                    $EventKey = trim($postObj->EventKey);


                    break;

                case "VIEW":
                    $EventKey = trim($postObj->EventKey);
                    break;

                case "MASSSENDJOBFINISH"://消息群发后的事件

                    break;

                default:
                    $cont = $postObj->Content;
                    $cont = strtolower(trim($cont));
                    // 保存粉丝留言
                    $this->saveUserMsg($fromUsername,$cont,$time);
                    $this->responseMsg('text',$default.$cont,$fromUsername,$toUsername,$time);
                    // 系统自动回复
                    break;
            }
        }
    }

    public function subscribe($openId,$fromUsername,$toUsername,$time)
    {
        $userInfo = $this->getUserOpeninfo($openId);
        // 记录下用户信息
        // 保存用户信息
        $this->saveWpmUser(json_decode($userInfo,true));
        $type = Db::name('WpmAutoreply')->where('replyId',1)->value("type");
        if($type == 1){
            $detail = Db::name('WpmAutoreply')->where('replyId',1)->value("detail");
            $this->responseText($detail);
        }else if($type == 3){
            $detail = Db::name('WpmAutoreply')->where('replyId',1)->value("detail");
            $detail = unserialize($detail);
            $this->responseMoreTextImg($detail);
        }


    }

    public function onSubscribe($openId)
    {
        // file_put_contents('nosub.txt',$openId);
        if(Db::name('user')->where('openId',$openId)->find()){
            Db::name('user')->where('openId',$openId)->update(array('isAllow'=>0));
        }

    }

    public function saveWpmUser($information)
    {
        $data['nickName'] = $information['nickname'];
        $data['wx_img'] = $information['headimgurl'];
        $data['remark'] = $information['remark'];
        $data['groupId']= $information['groupid'];
        $data['openId']= $information['openid'];
        $data['sex'] = $information['sex'];
        $data['isAllow'] = 1;
        if($cur = Db::name('user')->where('openId',$information['openid'])->value('id')){
            $data['id'] = $cur;
            Db::name('user')->update($data);
        }else{
            Db::name('user')->insert($data);
        }
    }

    public function saveUserMsg($openId,$msg,$time)
    {
        $msgData['openId'] = $openId;
        $msgData['detail'] = $msg;
        $msgData['addtime'] = $time;
        Db::name("WpmUsermsg")->insert($msgData);
    }

    public function saveLocation($openId,$lat,$lot)
    {
        $location['lat'] = $lat;
        $location['lot'] = $lot;
        Db::name('user')->where('openId',$openId)->update($location);
    }

    /**
     * 输出xml字符
     * @throws
     **/
    public function toXml($data){
        if(!is_array($data) || count($data) <= 0){
            die("数组数据异常！");
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


    public function responseText($detail)
    {
        $textTpl = "<xml>
	                <ToUserName><![CDATA[%s]]></ToUserName>
	                <FromUserName><![CDATA[%s]]></FromUserName>
	                <CreateTime>%s</CreateTime>
	                <MsgType><![CDATA[%s]]></MsgType>
	                <Content><![CDATA[%s]]></Content>
	                <FuncFlag>0</FuncFlag>
	                </xml>";

        $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, time(), 'text', $detail);
        echo $resultStr;
    }

    public function responseMoreTextImg($newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
		        <Title><![CDATA[%s]]></Title>
		        <Description><![CDATA[%s]]></Description>
		        <PicUrl><![CDATA[%s]]></PicUrl>
		        <Url><![CDATA[%s]]></Url>
		    </item>
		";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['title'], $item['description'], $item['picurl'], $item['url']);
        }
        $xmlTpl = "<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>%s</ArticleCount>
		<Articles>
		$item_str</Articles>
		</xml>";

        $result = sprintf($xmlTpl, $this->fromUsername, $this->toUsername, time(), count($newsArray));
        file_put_contents('mmmmmmmmmm.txt',$result);
        echo $result;
    }

    // 上传图文素材
    public function newMaterial($data)
    {
        $data['articles'] = array(array('title'=>'测试新增永久素材','thumb_media_id'=>mt_rand(1,99999),'author'=>'MengD','digest'=>'MengD开发商','show_cover_pic'=>1,'content'=>'测试新增永久素材测试新增永久素材测试新增永久素材','content_source_url'=>'http://my.ibicw.cn/platform/'));
        $result = $this->addSourceMaterial($data);
        $mediaId = $result['media_id'];

    }
}