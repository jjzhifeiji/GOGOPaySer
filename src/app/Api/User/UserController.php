<?php

namespace App\Api\User;


use App\Common\BaseController;
use App\Common\GoogleAuthenticator;

/**
 * 用户数据5000
 */
class UserController extends BaseController
{
    public function getRules()
    {
        return array(
            'register' => array(
                'user_name' => array('name' => 'user_name', 'require' => true, 'desc' => '昵称'),
                'user_account' => array('name' => 'user_account', 'require' => true, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'desc' => '密码'),
                'code' => array('name' => 'code', 'require' => true, 'desc' => '验证码'),
                'invitation' => array('name' => 'invitation', 'require' => true, 'desc' => '邀请码'),
            ),
            'login' => array(
                'user_account' => array('name' => 'user_account', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
            ),
            'modPwd' => array(
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '旧密码'),
                'newPwd' => array('name' => 'newPwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '新密码'),
            ),
            'getMyBill' => array(
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'setAssign' => array(
                'status' => array('name' => 'status', 'require' => true, 'desc' => '状态 0关闭 1开启'),
            ),
        );
    }


    /**
     * 注册
     * @desc 注册
     */
    public function register()
    {
        $user_name = $this->user_name;
        $user_account = $this->user_account;
        $pwd = $this->pwd;
        $code = $this->code;
        $invitation = $this->invitation;

        if ($code != $this->getCache($user_account)) {
            return $this->api_error(5002, "验证吗有误");
        }

        $res = $this->_getUserDomain()->register($user_name, $user_account, $pwd, $invitation);

        if ($res == true) {
            return $this->api_success();
        } else {
            return $this->api_error(5001, $res);
        }
    }

    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     */
    public function login()
    {
        $user_account = $this->user_account;   // 账号参数
        $pwd = $this->pwd;   // 密码参数

        $user = $this->_getUserDomain()->getUserInfoAccount($user_account);

        if (empty($user) || empty($user['id'])) {
            return $this->api_error(5003, 'Account Error');
        }
        $is_login = $this->_getUserDomain()->login($user_account, $pwd);
        if ($is_login) {
            $token = $this->getToken(intval($user['id']));
            $res = array(
                'id' => $user['id'],
                'account' => $user['account'],
                'user_name' => $user['user_name'],
                'type' => $user['type'],
                'status' => $user['status'],
                'token' => $token,
            );
            return $this->api_success($res);
        } else {
            return $this->api_error(5004, '密码错误');
        }
    }


    /**
     * 创建谷歌密钥
     * @desc 创建谷歌密钥
     */
    public function createGoogleAuthenticator()
    {

        $user = $this->member_arr;
        $status = $this->status;

        if ($status == 1) {
            $google = new GoogleAuthenticator();
            $secret = $google->createSecret();
            $name = $user['account'];
            $qr = $google->getQRCodeGoogleUrl($name, $secret);
            $this->_getUserDomain()->setSecret($user['id'], $secret);
            $res = array(
                'name' => $name,
                'code' => $secret,
                'qr' => $qr
            );
            $this->api_success($res);
        } else {
            $secret = '';
            $this->_getAdminDomain()->setSecret($admin['id'], $secret);
            $this->api_success();
        }

    }

    /**
     * 用户信息
     * @desc 获取用户信息
     */
    public function getUserInfo()
    {
        $user = $this->member_arr;
        $user = $this->_getUserDomain()->getMyInfo($user['id']);
        return $this->api_success($user);
    }


    /**
     * 修改密码
     * @desc 修改密码
     */
    public function modPwd()
    {
        $id = $this->member_arr['id'];
        $pwd = $this->pwd;
        $newPwd = $this->newPwd;
        $res = $this->_getUserDomain()->modPwd($id, $pwd, $newPwd);
        if ($res) {
            $this->removeToken($id);
            return $this->api_success();
        } else {
            return $this->api_error(5005, 'fail');
        }
    }


    /**
     * 自动接单
     * @desc 自动接单开关
     */
    public function setAssign()
    {
        $id = $this->member_arr['id'];
        $status = $this->status;
        $res = $this->_getUserDomain()->setAssign($id, $status);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(5005, 'fail');
        }
    }

    /**
     * 获取变帐记录
     * @用户余额变动记录
     */
    public function getMyBill()
    {
        $id = $this->member_arr['id'];
        $page = $this->page;
        $limit = $this->limit;
        $res = $this->_getUserDomain()->getMyBill($id, $page, $limit);
        return $this->api_success($res);
    }

}
