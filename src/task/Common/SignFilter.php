<?php

namespace Task\Common;

use PhalApi\Exception\BadRequestException;

/**
 *  简单的MD5拦截器
 *
 * - 签名的方案如下：
 *
 * + 1、排除签名参数（默认是sign）
 * + 2、将剩下的全部参数，按参数名字进行字典排序
 * + 3、将排序好的参数，全部用字符串拼接起来
 * + 4、进行md5运算
 *
 * 注意：无任何参数时，不作验签
 *
 */
class SignFilter
{

    public function check($params, $sign, $private)
    {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            if (isset($val) && $val) {
                $paramsStrExceptSign .= $val;
            }
        }
        $expectSign = md5($paramsStrExceptSign . $private);
        $oldExpectSign = md5($paramsStrExceptSign);

        if ($expectSign != $sign && $oldExpectSign != $sign) {
            \PhalApi\DI()->logger->error('Wrong Sign', array('sign' => $sign, 'needSign' => $expectSign));
            \PhalApi\DI()->logger->error('Wrong Sign', $paramsStrExceptSign . $private);
            throw new BadRequestException(\PhalApi\T('wrong sign'), 6);
        }
    }

    public function encryptAppKey($params, $private)
    {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            if (isset($val) && $val) {
                $paramsStrExceptSign .= $val;
            }
        }
        $expectSign = md5($paramsStrExceptSign . $private);

        return $expectSign;
    }

}
