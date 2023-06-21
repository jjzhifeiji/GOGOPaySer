<?php

namespace Admin\Api\User;

use Admin\Common\BaseController;

/**
 * 用户数据6000
 */
class UserController extends BaseController
{
    public function getRules()
    {
        return array(
            'getUser' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
            'addTopUser' => array(
                'user_name' => array('name' => 'user_name', 'require' => true, 'desc' => '名称'),
                'user_account' => array('name' => 'user_account', 'require' => true, 'desc' => '账号'),
                'bank_collect_val' => array('name' => 'bank_collect_val', 'require' => true, 'desc' => '银行卡收款'),
                'wx_collect_val' => array('name' => 'wx_collect_val', 'require' => true, 'desc' => '微信收款'),
                'ali_collect_val' => array('name' => 'ali_collect_val', 'require' => true, 'desc' => '支付宝收款'),
                'bank_out_val' => array('name' => 'bank_out_val', 'require' => true, 'desc' => '银行卡退款'),
                'wx_out_val' => array('name' => 'wx_out_val', 'require' => true, 'desc' => '微信退款'),
                'ali_out_val' => array('name' => 'ali_out_val', 'require' => true, 'desc' => '支付宝退款'),
            ),
            'addUser' => array(
                'user_name' => array('name' => 'user_name', 'require' => true, 'desc' => '名称'),
                'user_account' => array('name' => 'user_account', 'require' => true, 'desc' => '账号'),
                'group_id' => array('name' => 'group_id', 'require' => true, 'desc' => '所属组'),
                'bank_collect_val' => array('name' => 'bank_collect_val', 'require' => true, 'desc' => '银行卡收款'),
                'wx_collect_val' => array('name' => 'wx_collect_val', 'require' => true, 'desc' => '微信收款'),
                'ali_collect_val' => array('name' => 'ali_collect_val', 'require' => true, 'desc' => '支付宝收款'),
                'bank_out_val' => array('name' => 'bank_out_val', 'require' => true, 'desc' => '银行卡退款'),
                'wx_out_val' => array('name' => 'wx_out_val', 'require' => true, 'desc' => '微信退款'),
                'ali_out_val' => array('name' => 'ali_out_val', 'require' => true, 'desc' => '支付宝退款'),
            ),
            'getUserList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => '20', 'desc' => '数量'),
                'name' => array('name' => 'name', 'desc' => ''),
                'status' => array('name' => 'status', 'desc' => ''),
                'group_id' => array('name' => 'group_id', 'desc' => '')
            ),
            'modUserStatus' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'status' => array('name' => 'status', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
            'getCollectInfoList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => '20', 'desc' => '数量'),
                'user_id' => array('name' => 'user_id', 'desc' => ''),
                'status' => array('name' => 'status', 'desc' => ''),
                'type' => array('name' => 'type', 'desc' => ''),
            ),
        );
    }

//Request URL: http://ser.gogopay.top/Admin/User_UserController.addTopUser?user_name=123&user_account=123&group_id=&out_free=123&collect_free=123
//Request URL: http://ser.gogopay.top/Admin/User_UserController.addUser?group_id=4&user_name=qwe&user_account=123

    /**
     * 用户信息
     */
    public function addUser()
    {
        $user_name = $this->user_name;
        $user_account = $this->user_account;
        $group_id = $this->group_id;

        $bank_collect_val = $this->bank_collect_val;
        $wx_collect_val = $this->wx_collect_val;
        $ali_collect_val = $this->ali_collect_val;
        $bank_out_val = $this->bank_out_val;
        $wx_out_val = $this->wx_out_val;
        $ali_out_val = $this->ali_out_val;

        $res = $this->_getUserDomain()->register($user_name, $user_account, $group_id, $bank_collect_val, $wx_collect_val, $ali_collect_val, $bank_out_val, $wx_out_val, $ali_out_val);
        if ($res == '') {
            return $this->api_success();
        } else {
            return $this->api_error(6001, $res);
        }
    }

    public function addTopUser()
    {
        $user_name = $this->user_name;
        $user_account = $this->user_account;

        $bank_collect_val = $this->bank_collect_val;
        $wx_collect_val = $this->wx_collect_val;
        $ali_collect_val = $this->ali_collect_val;
        $bank_out_val = $this->bank_out_val;
        $wx_out_val = $this->wx_out_val;
        $ali_out_val = $this->ali_out_val;

        $res = $this->_getUserDomain()->registerTop($user_name, $user_account,$bank_collect_val, $wx_collect_val, $ali_collect_val, $bank_out_val, $wx_out_val, $ali_out_val);
        if ($res == '') {
            return $this->api_success();
        } else {
            return $this->api_error(6002, $res);
        }
    }


    /**
     * 用户信息
     */
    public function getUser()
    {
        $id = $this->id;
        $res = $this->_getUserDomain()->getUser($id);
        return $this->api_success($res);
    }

    /**
     * 获取组用户信息
     */
    public function getGroupUser()
    {
        $res = $this->_getUserDomain()->getGroupUser();
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function getUserList()
    {
        $page = $this->page;
        $limit = $this->limit;
        $group_id = $this->group_id;
        $status = $this->status;
        $name = $this->name;

        $res = $this->_getUserDomain()->getUserList($page, $limit, $name, $status, $group_id);
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function getsUserGroup()
    {
        $res = $this->_getUserDomain()->getsUserGroup();
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function modUserStatus()
    {
        $id = $this->id;
        $status = $this->status;
        $this->_getUserDomain()->modUserStatus($id, $status);
        return $this->api_success();
    }

    /**
     * 用户收款信息
     */
    public function getCollectInfoList()
    {
        $page = $this->page;
        $limit = $this->limit;
        $user_id = $this->user_id;
        $status = $this->status;
        $type = $this->type;

        $res = $this->_getCollectInfoDomain()->getCollectInfoList($page, $limit, $user_id, $status, $type);
        return $this->api_success($res);
    }

}
