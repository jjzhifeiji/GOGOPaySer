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

//跨域
header('Access-Control-Allow-Origin:*');  //支持全域名访问，不安全，部署后需要固定限制为客户端网址,允许所有来源访问
header('Access-Control-Allow-Methods:POST'); //支持的http 动作
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1728000");
header('Access-Control-Allow-Headers: Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control, token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie');

const RESOURCE_DIR = '/home/Resource/GOGOPAY/';
const IMAGE_SOURCE_CONFIG = 'image/config/';
const IMAGE_SOURCE_COLLECT = 'image/collect/';
const IMAGE_SOURCE_ALI = 'image/ali/';
const IMAGE_SOURCE_WX = 'image/wx/';
const CACHE_RESOURCE = 'cache/';
const HTTP_RESOURCE = 'http://source.gogopay.top/';
const HTTP_SHOW = 'http://show.gogopay.top/';


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
