<?php
/**
 * Created by whlphper.
 * User: Administrator
 * Date: 2018/5/16 0016
 * Time: 上午 10:09
 * Desc:
 */
namespace app\chat\controller;
use app\common\controller\Base;
use think\Request;
use Workerman\Worker;
use GatewayWorker\Register;
class Sregister extends Base{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        // register 服务必须是text协议
        $register = new Register('text://0.0.0.0:1236');
        // 如果不是在根目录启动，则运行runAll方法
        if(!defined('GLOBAL_START'))
        {
            Worker::runAll();
        }
    }

    public function index()
    {
        return view();
    }
}