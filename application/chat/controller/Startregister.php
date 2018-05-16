<?php
namespace app\chat\controller;
use think\Controller;
use \Workerman\Worker;
use \GatewayWorker\Register as vdvdvc;
class Startregister extends Controller{
    /**
     * This file is part of workerman.
     *
     * Licensed under The MIT License
     * For full copyright and license information, please see the MIT-LICENSE.txt
     * Redistributions of files must retain the above copyright notice.
     *
     * @author walkor<walkor@workerman.net>
     * @copyright walkor<walkor@workerman.net>
     * @link http://www.workerman.net/
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    public function index()
    {
        // register 服务必须是text协议
        $register = new vdvdvc('text://0.0.0.0:1236');
        // 如果不是在根目录启动，则运行runAll方法
        if(!defined('GLOBAL_START'))
        {
            Worker::runAll();
        }
    }

}


