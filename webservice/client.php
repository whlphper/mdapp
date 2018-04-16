<?php
use webservice\Service;

ini_set('soap.wsdl_cache_enabled','0');//关闭缓存
$soap=new SoapClient('http://127.0.0.1/mdapp/index.php/Service.php?wsdl');
echo $soap->Add(1,2);
//echo $soap->_soapCall('Add',array(1,2))//或者这样调用也可以
?>