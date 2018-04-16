<?php
namespace app\index\controller;
class Index
{
    public function index()
    {
        $menuInfo = model('Roles')->getMenuStr(1);
        dump($menuInfo);
        if(empty($menuInfo['menu_ids'])){
            echo 0;
        }
        dump(model('Menus')->getList('1,2,3'));
    }

    public function service()
    {
        include ROOT_PATH.'webservice/Service.php';
    }

    public function createWebservice()
    {
        include_once(ROOT_PATH.'webservice/Service.php');
        include_once(ROOT_PATH.'webservice/SoapDiscovery.class.php');
        $wsdl=new SoapDiscovery('Service','soap');//第一参数为类名，也是生成wsdl的文件名Service.wsdl，第二个参数是服务的名字可以随便写
        $wsdl->getWSDL();
    }

    public function client(){
        try{
            //libxml_disable_entity_loader(false);
            ini_set('soap.wsdl_cache_enabled','0');//关闭缓存
            $soap= new \SoapClient(null,array('location'=>'http://127.0.0.1/mdapp/webservice/Service.php','uri' => 'Service.php','soap_version'=>'SOAP_1_1'));
            $soap->soap_defencoding = 'UTF-8';
            $soap->xml_encoding = 'UTF-8';
            //echo $soap->Add(1,2);
            echo $soap->_soapCall('Add',array(1,2));//或者这样调用也可以
        }catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function test1()
    {
        WebService(url('index/Index/test1'),'Index');
    }

    public function test2()
    {
        $client = WebClient();
        $res = $client->itemType('swiper_price','1234');
        dump($res);exit;
    }

    public function itemType( $type='', $style='' )
    {
        echo $type.$style;
    }
}
