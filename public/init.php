<?php
/**
 * 统一初始化
 */

// 定义项目路径
defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');

// 运行模式，可以是：dev, test, prod
defined('API_MODE') || define('API_MODE', 'prod');

// 引入composer
require_once API_ROOT . '/vendor/autoload.php';

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 引入DI服务
include API_ROOT . '/config/di.php';

// 允许跨域
$response = \PhalApi\DI()->response;
$response->addHeaders('Access-Control-Allow-Origin', '*'); // *代表允许任何网址请求
$response->addHeaders('Access-Control-Allow-Methods', '*'); // 允许请求的类型
$response->addHeaders('Access-Control-Allow-Headers', '*'); // 设置允许自定义请求头的字段
$response->addHeaders('Access-Control-Allow-Credentials', 'true'); // 设置是否允许发送 cookies

const RESOURCE_DIR = '/home/Resource/GOGOPAY/';
const IMAGE_SOURCE_CONFIG = 'image/config/';
const IMAGE_SOURCE_COLLECT = 'image/collect/';
const IMAGE_SOURCE_ALI = 'image/ali/';
const IMAGE_SOURCE_WX = 'image/wx/';
const CACHE_RESOURCE = 'cache/';
const HTTP_RESOURCE = 'https://source.gogopay.top/';
const HTTP_SHOW = 'https://show.tmpay777.com';


// 调试模式
if (\PhalApi\DI()->debug) {
    // 启动追踪器
    \PhalApi\DI()->tracer->mark('PHALAPI_INIT');

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

// 翻译语言包设定-简体中文
\PhalApi\SL(isset($_COOKIE['language']) ? $_COOKIE['language'] : 'zh_cn');

// English
// \PhalApi\SL('en');
