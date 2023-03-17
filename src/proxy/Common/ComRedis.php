<?php

/**
 * Created by PhpStorm.
 * UserModel: lijingzhe
 * Date: 2019/3/1
 * Time: 9:40 PM
 */

namespace Proxy\Common;

use PhalApi\Tool;

class ComRedis
{

    /**
     * 内部切换Redis-DB
     * @param string $name redis name
     * @return \Redis
     */
    public static function getRedis($name = '')
    {
        $redis = \PhalApi\DI()->redis->getRedis();
        $arr = \PhalApi\DI()->config->get('sys.redis.DB');
        if (is_int($name)) {
            $db = $name;
        } else {
            $db = $arr[$name] ?? 0;
        }
        \PhalApi\DI()->logger->info('获取Redis', $db);
        $redis->select($db);
        return $redis;
    }


    /**-----------------------------------------------redis-lock--start--------------------------------------------------------*/


    /**
     * 加锁
     * @param $id
     * @return bool
     */
    public static function lock($id)
    {
        $redis = self::getRedis('lock');
        $uid = Tool::createRandStr(10);
        $is_lock = $redis->setnx($id, $uid);
        $count = 0;
        while (!$is_lock) {
            $count++;
            usleep(50000);;//50毫秒拿一次锁
            $is_lock = $redis->setnx($id, $uid);
            \PhalApi\DI()->logger->info($id . '锁被占用，重新获取', $is_lock);
            \PhalApi\DI()->logger->info($id . '锁被占用，重新获取', $uid);
            \PhalApi\DI()->logger->info($id . '锁被占用，重新获取' . $is_lock, $redis->get($id));
            if ($count > 200)//10秒
                break;
        }
        if ($is_lock) {
            $is_time = $redis->expire($id, 5);
            if (!$is_time) {
                $redis->del($id);
                $is_lock = false;
            } else {
                $redis->setex('key_look' . $id, 5, $uid);
            }
        }
        \PhalApi\DI()->logger->info($id . '拿锁', $is_lock);
        \PhalApi\DI()->logger->info($id . '拿锁', $uid);
        \PhalApi\DI()->logger->info($id . '拿锁', $redis->get($id));
        $redis->close();
        return $is_lock;
    }

    /**
     * 解锁
     * @param $id
     */
    public static function unlock($id)
    {
        $redis = self::getRedis('lock');
        $uid = $redis->get('key_look' . $id);
        if ($uid == $redis->get($id)) {
            \PhalApi\DI()->logger->info($id . '还锁', $uid);
            \PhalApi\DI()->logger->info($id . '还锁', $redis->get($id));
            $redis->del($id);
            $redis->del('key_look' . $id);
        } else {
            \PhalApi\DI()->logger->info($id . '还锁失败', $uid);
            \PhalApi\DI()->logger->info($id . '还锁失败', $redis->get($id));
        }
        $redis->close();
    }

    /**-----------------------------------------------redis-lock--end--------------------------------------------------------*/


    /**-----------------------------------------------redis-task--start--------------------------------------------------------*/


    /**
     * @param $json_msg
     */
    public static function pushTask($json_msg)
    {
        \PhalApi\DI()->logger->info('推送队列', $json_msg);
        $redis = self::getRedis('push');
        $redis->lPush('order_push_msg', $json_msg);
        $redis->close();
    }

    /**
     * @param $json_msg
     */
    public static function noticeTask($json_msg)
    {
        \PhalApi\DI()->logger->info('通知队列', $json_msg);
        $redis = self::getRedis('notice');
        $redis->lPush('platform_notice', $json_msg);
        $redis->close();
    }


    /**-----------------------------------------------redis-task--end--------------------------------------------------------*/


    /**-----------------------------------------------redis-cache--start--------------------------------------------------------*/

    /**
     * @param $key
     * @param $val
     */
    public static function setRCache($key, $val)
    {
        if (empty($val)) return;
        $redis = self::getRedis('cache');
        $redis->set($key, json_encode($val));
        $redis->close();
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getRCache($key)
    {
        $redis = self::getRedis('cache');
        $json_msg = $redis->get($key);
        $redis->close();
        return json_decode($json_msg);
    }

    /**-----------------------------------------------redis-cache--end--------------------------------------------------------*/


    /**-----------------------------------------------redis-list--start--------------------------------------------------------*/

    /**
     * @param $key
     * @param $val
     */
    public static function pushOrder($val)
    {
        if (empty($val)) return;
        $redis = self::getRedis();
        $redis->rPush('RESERVEORDER', $val);
        $redis->close();
    }

    /**-----------------------------------------------redis-list--end--------------------------------------------------------*/


}
