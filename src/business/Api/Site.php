<?php

namespace Portal\Api;

use PhalApi\Api;

/**
 * 默认接口服务类
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Site extends Api
{
    public function getRules()
    {
        return array(
            'index' => array(
                'username' => array('name' => 'username', 'default' => 'PhalApi', 'desc' => '用户名'),
            ),
            // 字符串
            'str' => array(
                'str' => array('name' => 'str', 'desc' => '简单的字符串参数'),
            ),
            'defaultStr' => array(
                'str' => array('name' => 'str', 'type' => 'string', 'require' => true, 'default' => 'PhalApi', 'desc' => '默认字符串参数，且参数必须'),
                'strHide' => array('name' => 'str_hide', 'type' => 'string', 'require' => true, 'default' => 'PhalApi', 'desc' => '默认字符串参数，且参数必须', 'is_doc_hide' => true), // 接口文档隐藏参数，但实际仍然可使用
                'strRemove' => null, // 移除接口参数，在PHP后端代码中不可用，且不会在接口文档显示
            ),
            'regexStr' => array(
                'str' => array('name' => 'str', 'regex' => "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", 'desc' => '指定正则的字符串参数'),
            ),

            // 整数
            'number' => array(
                'number' => array('name' => 'number', 'type' => 'int', 'require' => true, 'desc' => '必须的整数参数'),
            ),
            'rangeNumber' => array(
                'number' => array('name' => 'number', 'type' => 'int', 'min' => 1, 'max' => 100, 'default' => 1, 'desc' => '指定范围且有默认值的整数参数'),
            ),

            // 浮点数，和整数类似，略……

            // 布尔值
            'trueOrFalse' => array(
                'switch' => array('name' => 'switch', 'type' => 'boolean', 'desc' => '以下值会转换为TRUE：ok，true，success，on，yes，1，以及其他PHP作为TRUE的值')
            ),

            // 日期
            'dateStr' => array(
                'date' => array('name' => 'date', 'type' => 'date', 'defaut' => '2019-03-01 00:00:00', 'desc' => '日期参数，没有强制的格式要求'),
            ),
            'dateTimestamp' => array(
                'date' => array('name' => 'date', 'type' => 'date', 'format' => 'timestamp', 'desc' => '会自动转为时间戳的日期参数')
            ),

            'jsonArray' => array(
                'datas' => array('name' => 'datas', 'type' => 'array', 'format' => 'json', 'default' => array(), 'desc' => 'JSON格式的数组参数，例如：datas={"name":"PhalApi"}'),
            ),
            'explodeArray' => array(
                'datas' => array('name' => 'datas', 'type' => 'array', 'format' => 'explode', 'default' => array(1, 2, 3), 'separator' => ',', 'min' => 1, 'max' => 10, 'desc' => '以英文逗号分割的数组，数组个数最少1个，最多10个，例如：datas=1,2,3'),
            ),

            // 枚举
            'sexEnum' => array(
                'sex' => array('name' => 'sex', 'type' => 'enum', 'range' => array('female', 'male'), 'desc' => '性别，female为女，male为男。'),
            ),
            'statusEnum' => array(
                'status' => array('name' => 'type', 'require' => true, 'type' => 'enum', 'range' => array('0', '1', '2'), 'desc' => '状态，注意：如果需要配置数值的枚举型，请使用字符串类型进行配置，避免误判。通常此时建议改用int整型。'),
            ),

            // 回调类型
            'versionCallback' => array(
                'version' => array('name' => 'version', 'require' => true, 'type' => 'callback', 'callback' => 'App\\Common\\Request\\Version::formatVersion', 'desc' => '版本号，指定回调函数进行检测，版本号格式例如：2.6.0。'),
            ),
        );
    }

    /**
     * 默认接口服务
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function index()
    {
        return array(
            'title' => 'Hello ',
            'version' => 1,
            'time' => $_SERVER['REQUEST_TIME'],
        );
    }
}
