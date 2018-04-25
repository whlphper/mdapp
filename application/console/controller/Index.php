<?php

namespace app\console\controller;

use think\Controller;
use think\Console;
use think\Request;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * 这是用来调用命令行的类
 * Class Index
 * @package app\console\controller
 */
class Index extends Controller
{
    public function test1()
    {
        // 调用命令行的指令
        $output = Console::call('clearSurplusOrder', ['im from controller','120','666@163.com','--num','12']);
        // 获取输出信息
        return $output->fetch();
    }
}
