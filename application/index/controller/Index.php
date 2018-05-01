<?php

namespace app\index\controller;

use app\common\model\Crmorder;
use think\Controller;
use think\queue\Job;
use think\Db;

class Index extends Controller
{
    protected $beforeActionList = [
        //'first',                                //在执行所有方法前都会执行first方法
        //'second' => ['except' => 'hello'],       //除hello方法外的方法执行前都要先执行second方法
        //'three' => ['only' => 'hello,data'],    //在hello/data方法执行前先执行three方法
    ];

    public function second()
    {
        echo 'second';
        exit;
    }

    public function hello()
    {
        echo 'hello';
        exit;
    }

    public function three()
    {
        $this->redirect(url('/pcshop/'));
        echo 'three';
        exit;
    }

    public function index()
    {
        $menuInfo = model('Roles')->getMenuStr(1);
        dump($menuInfo);
        if (empty($menuInfo['menu_ids'])) {
            echo 0;
        }
        dump(model('Menus')->getList('1,2,3'));
    }

    public function service()
    {
        include ROOT_PATH . 'webservice/Service.php';
    }

    public function createWebservice()
    {
        include_once(ROOT_PATH . 'webservice/Service.php');
        include_once(ROOT_PATH . 'webservice/SoapDiscovery.class.php');
        $wsdl = new SoapDiscovery('Service', 'soap');//第一参数为类名，也是生成wsdl的文件名Service.wsdl，第二个参数是服务的名字可以随便写
        $wsdl->getWSDL();
    }

    public function client()
    {
        try {
            //libxml_disable_entity_loader(false);
            ini_set('soap.wsdl_cache_enabled', '0');//关闭缓存
            $soap = new \SoapClient(null, array('location' => 'http://127.0.0.1/mdapp/webservice/Service.php', 'uri' => 'Service.php', 'soap_version' => 'SOAP_1_1'));
            $soap->soap_defencoding = 'UTF-8';
            $soap->xml_encoding = 'UTF-8';
            //echo $soap->Add(1,2);
            echo $soap->_soapCall('Add', array(1, 2));//或者这样调用也可以
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    public function test1()
    {
        WebService(url('index/Index/test1'), 'Index');
    }

    public function test2()
    {
        $client = WebClient();
        $res = $client->itemType('swiper_price', '1234');
        dump($res);
        exit;
    }

    public function itemType($type = '', $style = '')
    {
        echo $type . $style;
    }

    /**
     * 开启队列
     */
    public function queue1()
    {
        \think\Queue::push('app\index\controller\Index@fire', 'whlphper', $queue = '测试队列一');
        echo 'ok';
    }

    /**
     * 队列开始
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data)
    {
        $i = 0;
        //执行业务逻辑
        while ($i<10){
            Db::name('Jobs')->insert(['attempts' => $job->attempts(), 'created_at' => date('Y-m-d H:i:s')]);
            $i++;
        }
        $isJobDone = true;
        if ($isJobDone) {
            //成功删除任务
            $job->delete();
        } else {
            //任务轮询4次后删除
            if ($job->attempts() > 3) {
                // 第1种处理方式：重新发布任务,该任务延迟10秒后再执行
                //$job->release(10);
                // 第2种处理方式：原任务的基础上1分钟执行一次并增加尝试次数
                //$job->failed();
                // 第3种处理方式：删除任务
                $job->delete();
            }
        }
    }

    /**
     * 队列完毕
     * @param $data
     */
    public function failed($data)
    {
        // ...任务达到最大重试次数后，失败了
        file_put_contents('countEnd.txt', '任务完毕');
    }
}
