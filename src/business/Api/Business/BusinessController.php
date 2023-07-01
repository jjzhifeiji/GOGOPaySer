<?php

namespace Business\Api\Business;

use Business\Common\BaseController;
use Business\Common\GoogleAuthenticator;

/**
 * 商户信息
 */
class BusinessController extends BaseController
{

    public function getRules()
    {
        return array(
            'login' => array(
                'account' => array('name' => 'account', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '账号'),
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码'),
                'code' => array('name' => 'code', 'require' => false, 'desc' => 'code'),
            ),
            'getsAmountLog' => array(
                'type' => array('name' => 'type', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'createGoogleAuthenticator' => array(
                'status' => array('name' => 'status', 'require' => true, 'desc' => ''),
            ),
            'modPwd' => array(
                'pwd' => array('name' => 'pwd', 'require' => true, 'desc' => '密码'),
            ),
        );
    }

    /***
     * 商户信息
     * @desc 商户信息
     */
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
        $code = $this->code;   // 密码参数

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
        $admin = $this->member_arr;
        $status = $this->status;

        if ($status == 1) {
            $google = new GoogleAuthenticator();
            $secret = $google->createSecret();
            $name = $admin['account'];
            $qr = $google->getQRCodeGoogleUrl($name, $secret);
            $this->_getBusinessDomain()->setSecret($admin['id'], $secret);
            $res = array(
                'name' => $name,
                'code' => $secret,
                'qr' => $qr
            );
            return $this->api_success($res);
        } else {
            $secret = '';
            $this->_getBusinessDomain()->setSecret($admin['id'], $secret);
            return $this->api_success();
        }

    }


    /***
     * 商户变账
     * @desc 商户变账
     */
    public function getsAmountLog()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $type = $this->type;
        $res = $this->_getBusinessDomain()->getsAmountLog($user['id'], $type, $page, $limit);
        return $this->api_success($res);
    }

    /**
     * 修改密码
     */
    public function modPwd()
    {
        $admin = $this->member_arr;
        $pwd = $this->pwd;

        $this->_getBusinessDomain()->modPwd($admin['id'], $pwd);
        return $this->api_success();
    }

    /**
     * 商户首页信息
     */
    public function getBusinessHome()
    {
        $admin = $this->member_arr;

        $res = $this->_getBusinessDomain()->getBusinessHome($admin['id']);
        return $this->api_success($res);
    }

}
