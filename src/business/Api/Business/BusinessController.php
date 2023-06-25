<?php

namespace Business\Api\Business;

use Business\Common\BaseController;
use Business\Common\GoogleAuthenticator;

/**
 *
 */
class BusinessController extends BaseController
{

    public function getRules()
    {
        return array(
            'login' => array(
                'account' => array('name' => 'account', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
            ),
            'getsAmountLog' => array(
                'type' => array('name' => 'type', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),

        );
    }

    public function getInfo()
    {
        $user = $this->member_arr;
        $res = $this->_getBusinessDomain()->getBusiness($user['id']);
        $r['id'] = $res['id'];
        $r['platform_sn'] = $res['platform_sn'];
        $r['name'] = $res['name'];
        $r['account'] = $res['account'];
        $r['status'] = $res['status'];
        $r['private_key'] = $res['private_key'];
        $r['business_amount'] = $res['business_amount'];

        $r['bank_collect_val'] = $res['collect_bank_free'];
        $r['wx_collect_val'] = $res['collect_wx_free'];
        $r['ali_collect_val'] = $res['collect_ali_free'];

        $r['bank_out_free'] = $res['bank_out_free'];
        $r['wx_out_free'] = $res['wx_out_free'];
        $r['ali_out_free'] = $res['ali_out_free'];

        $r['whitelist'] = $res['whitelist'];
        return $this->api_success($r);
    }


    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     */
    public function login()
    {
        $account = $this->account;   // 账号参数
        $pwd = $this->pwd;   // 密码参数

        $user = $this->_getBusinessDomain()->getBusinessAccount($account);

        if (empty($user) || empty($user['id'])) {
            return $this->api_error(1001, '账户有误');
        }
        if (!empty($user['google_auth'])) {
            $google = new GoogleAuthenticator();
            if (empty($code)) {
                return $this->api_error(1003, '请输入google code');
            } else if (!$google->verifyCode($user['google_auth'], $code)) {
                return $this->api_error(1004, 'google code错误');
            }
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
                'token' => $token,
            );
            return $this->api_success($res);
        }
    }

    /**
     * 创建谷歌密钥
     * @desc 创建谷歌密钥
     */
    public function createGoogleAuthenticator()
    {
        $google = new GoogleAuthenticator();
        $secret = $google->createSecret();
        return $this->api_success($secret);
    }

    /**
     * 设置 谷歌密钥
     * @desc 设置 谷歌密钥
     */
    public function setGoogleAuthenticator()
    {
        $admin = $this->member_arr;
        $secret = $this->secret;
        $code = $this->code;

        $google = new GoogleAuthenticator();

        if (!$google->verifyCode($secret, $code)) {
            $this->_getBusinessDomain()->setSecret($admin['id'], $secret);
            return $this->api_success();
        }

        return $this->api_error(1004, 'google code错误');

    }

    public function getsAmountLog()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $type = $this->type;
        $res = $this->_getBusinessDomain()->getsAmountLog($user['id'], $type, $page, $limit);
        return $this->api_success($res);
    }


}
