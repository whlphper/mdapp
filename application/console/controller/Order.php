<?php

namespace app\console\controller;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\output\question\Confirmation;
use think\console\output\Ask;
use think\console\output\Question;

/**
 * 这是一个测试命令行类
 * Class Order
 * @package app\console\controller
 */
class Order extends Command
{
    protected function configure()
    {
        $this->setName('clearSurplusOrder')->setDescription('clearSurplusOrder');
        //参数1
        $this->addArgument('name', Argument::REQUIRED, "whlphper");
        //参数2
        $this->addArgument('age', Argument::REQUIRED, "25");
        //参数3
        $this->addArgument('email', Argument::OPTIONAL, "761243073@qq.com");
        // 运行命令时使用 "--help" 选项时的完整命令描述
        $this->setHelp("This command will be clear Surplus Order");
        // 选项
        $this->addOption(
            'num',
            null,
            Option::VALUE_OPTIONAL,
            'How many messages should be print?',
            1
        );
    }

    protected function execute(Input $input, Output $output)
    {
        /*-----------设置隐藏域---------    这个没搞明白啥情况    浏览器上调用这些是错误的
        $question = new Question('Please enter your password');
        $question->setValidator(function ($value) {
            if (trim($value) != '123000') {
                throw new \Exception('Illegal key');
            }
            return $value;
        });
        $question->setHidden(true);
        $question->setMaxAttempts(2);*/



        /*-----------添加一个询问层------                      浏览器上调用这些是错误的
        $question = new Confirmation('Continue with this action?', false, '/^(y|j)/i');
        $ask = new Ask($input, $output, $question);
        $answer = $ask->run();
        /*
        if ($input->isInteractive()) {
            // 输出空行
            $output->newLine();
        }

        if (!$answer) {
            $output->writeln('your cancel this command');
            return;
        }
        */
        /*-----------用户选择-----------                       浏览器上调用这些是错误的
        $color = $output->choice(
            $input,
            'Please select your favorite color (defaults to red)',
            ['red', 'blue', 'yellow'],
            'red'
        );
        $output->writeln("Your choose :".$color);*/

        /*-----------获取参数-----------*/
        $name = $input->getArgument('name');
        $age = $input->getArgument('age');
        $email = $input->getArgument('email');

        /*-----------输出参数-----------*/
        $output->writeln('hi your name is :'.$name);
        $output->writeln('hi '.$name.'your age is :'.$age);
        $output->writeln('hi '.$name.'your email is :'.$email);

        $output->writeln('INPUT  data'.json_encode($input));
        $output->writeln('OUTPUT data'.json_encode($output));
        $output->writeln("TestCommand:");

        /*-----------获取选项-----------*/
        $optNum = $input->getOption('num');
        for ($i = 0; $i < $optNum; $i++) {
            $output->writeln($i);
        }
        // 红色背景上的白字
        $output->writeln('<error>white text on a red background</error>');
    }
}
