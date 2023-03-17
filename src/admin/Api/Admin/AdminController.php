<?php

namespace Admin\Api\Admin;

use Admin\Common\BaseController;

/**
 * admin数据1000
 */
class AdminController extends BaseController
{

    public function getRules()
    {
        return array(
            'login' => array(
                'admin_name' => array('name' => 'admin_name', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
            ),

        );
    }

    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     */
    public function login()
    {
        $admin_name = $this->admin_name;   // 账号参数
        $pwd = $this->pwd;   // 密码参数

        $user = $this->_getAdminDomain()->getAdminAccount($admin_name);

        if (empty($user) || empty($user['id'])) {
            return $this->api_error(1001, '账户有误');
        }
        if ($user['pwd'] !== $pwd) {
            return $this->api_error(1002, '账户密码错误');
        } else {
            $token = $this->getToken(intval($user['id']));
            $res = array(
                'id' => $user['id'],
                'name' => $user['name'],
                'account' => $user['account'],
                'status' => $user['status'],
                'type' => $user['type'],
                'token' => $token,
            );
            return $this->api_success($res);
        }
    }

    public function getInfo()
    {
        $admin = $this->member_arr;
        $res = $this->_getAdminDomain()->getAdminAccount($admin['account']);
        return $this->api_success($res);
    }

}
