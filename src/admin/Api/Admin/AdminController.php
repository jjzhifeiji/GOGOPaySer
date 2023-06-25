<?php

namespace Admin\Api\Admin;

use Admin\Common\BaseController;
use Admin\Common\GoogleAuthenticator;

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
                'code' => array('name' => 'code', 'require' => false, 'desc' => 'google code'),
            ),
            'getAdminList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => '20', 'desc' => '数量'),
            ),
            'addAdmin' => array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => '名称'),
                'account' => array('name' => 'account', 'require' => true, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'desc' => '密码'),
            ),
            'delAdmin' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => 'id'),
            ),
            'setGoogleAuthenticator' => array(
                'secret' => array('name' => 'secret', 'require' => true, 'desc' => '密钥'),
                'code' => array('name' => 'code', 'require' => true, 'desc' => 'code'),
            )

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
        $code = $this->code;   // 密码参数

        $user = $this->_getAdminDomain()->getAdminAccount($admin_name);

        if (!empty($user['google_authenticator'])) {
            $google = new GoogleAuthenticator();
            if (empty($code)) {
                return $this->api_error(1003, '请输入google code');
            } else if (!$google->verifyCode($user['google_authenticator'], $code)) {
                return $this->api_error(1004, 'google code错误');
            }
        }


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

    public function createGoogleAuthenticator()
    {
        $google = new GoogleAuthenticator();
        $secret = $google->createSecret();
        return $this->api_success($secret);
    }

    public function setGoogleAuthenticator()
    {
        $admin = $this->member_arr;
        $secret = $this->secret;
        $code = $this->code;

        $google = new GoogleAuthenticator();

        if (!$google->verifyCode($secret, $code)) {
            $this->_getAdminDomain()->setAdminAccount($admin['id'], $secret);
            return $this->api_success();
        }

        return $this->api_error(1004, 'google code错误');

    }

    /**
     * 获取管理信息
     */
    public function getInfo()
    {
        $admin = $this->member_arr;
        $res = $this->_getAdminDomain()->getAdminAccount($admin['account']);
        return $this->api_success($res);
    }

    /**
     * 获取管理信息
     */
    public function getAdminList()
    {
        $admin = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $res = $this->_getAdminDomain()->getAdminList($page, $limit);
        return $this->api_success($res);
    }

    /**
     * 添加管理员
     */
    public function addAdmin()
    {
        $admin = $this->member_arr;
        if ($admin['id'] !== 1) {
            return $this->api_error(1003, '权限不足，请使用超级管理员账户');
        }
        $name = $this->name;
        $account = $this->account;
        $pwd = $this->pwd;
        $this->_getAdminDomain()->addAdmin($name, $account, $pwd);
        return $this->api_success();
    }

    /**
     * 删除管理员
     */
    public function delAdmin()
    {
        $admin = $this->member_arr;
        if ($admin['id'] !== 1) {
            return $this->api_error(1003, '权限不足，请使用超级管理员账户');
        }
        $id = $this->id;

        $res = $this->_getAdminDomain()->delAdmin($id);
        return $this->api_success();
    }

}
