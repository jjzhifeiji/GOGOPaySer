<?php

namespace Admin\Api\User;

use Admin\Common\BaseController;

/**
 * 用户数据8000
 */
class UserController extends BaseController
{
    public function getRules()
    {
        return array(
            'getUser' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
            'addUser' => array(
                'user_name' => array('name' => 'user_name', 'require' => true, 'desc' => '名称'),
                'user_account' => array('name' => 'user_account', 'require' => true, 'desc' => '名称'),
                'out_free' => array('name' => 'out_free', 'require' => false, 'desc' => '描述'),
                'collect_free' => array('name' => 'collect_free', 'require' => false, 'desc' => '描述'),
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

        );
    }


    /**
     * 用户信息
     */
    public function addUser()
    {
        $user_name = $this->user_name;
        $user_account = $this->user_account;
        $out_free = $this->out_free;
        $collect_free = $this->collect_free;

        $res = $this->_getUserDomain()->register($user_name, $user_account, $collect_free, $out_free);
        if ($res == true) {
            return $this->api_success();
        } else {
            return $this->api_error(8001, $res);
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

}
