<?php

namespace App\Api\Common;


use App\Common\BaseController;

/**
 * 公共数据控制器4000
 */
class CommonController extends BaseController
{
    public function getRules()
    {
        return array(
            'sendEmailCode' => array(
                'email' => array('name' => 'email', 'require' => true, 'desc' => ''),
            ),
        );
    }


    /**
     * 发送验证吗
     */
    public function sendEmailCode()
    {
        $email = $this->email;

        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            return $this->api_error('非法邮箱格式');
        }


        $code = rand(100000, 999999);
        $this->setCache($email, $code, 5 * 60);

        \PhalApi\DI()->mailer->send($email, 'GOGOPAY', '尊敬的用户：您好！
        您的验证吗未' . $code . ',有效期5分钟');
        return $this->api_success();
    }


    /**
     * 检查更新
     */
    public function checkVersion()
    {
        $res = $this->_getCommonDomain()->checkVersion();
        return $this->api_success($res);
    }


}
