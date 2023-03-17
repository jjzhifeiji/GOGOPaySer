<?php

namespace Task\Common;

/**
 * User: layne.xfl
 * Date: 2017/5/12
 * Time: 下午01:24
 */
class Arrow
{

    static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null == Arrow::$instance)
            Arrow::$instance = new Arrow();
        return Arrow::$instance;
    }

    public function run($rb)
    {

        $pid = pcntl_fork();
        if ($pid > 0) {
            pcntl_wait($status);
        } elseif ($pid == 0) {
            $cid = pcntl_fork();
            if ($cid > 0) {
                exit();
            } elseif ($cid == 0) {
                $rb();
            } else {
                exit();
            }
        } else {
            exit();
        }

    }
}

////离弦之箭---调用方法
//$time_out = 30;
//Arrow::getInstance()->run(function () use ($time_out) {
//    //这里写我们要执行的代码
//    sleep($time_out);
//});
