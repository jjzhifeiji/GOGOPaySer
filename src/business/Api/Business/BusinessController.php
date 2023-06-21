<?php

namespace Business\Api\Business;

use Business\Common\BaseController;

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
        $r['bank_collect_val'] = $res['bank_collect_val'];
        $r['wx_collect_val'] = $res['wx_collect_val'];
        $r['ali_collect_val'] = $res['ali_collect_val'];
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
