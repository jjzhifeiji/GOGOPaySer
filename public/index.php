<?php
/**
 * 统一访问入口
 */

require_once dirname(__FILE__) . '/init.php';

$pai = new \PhalApi\PhalApi();
$pai->response()->output();

$request = \PhalApi\DI()->request->getAll();
$response = \PhalApi\DI()->response->getAll();
\PhalApi\DI()->logger->debug('request', $request);
\PhalApi\DI()->logger->debug('response', $response);