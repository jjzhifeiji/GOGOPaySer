<?php

namespace Proxy\Api\User;

use Proxy\Common\BaseController;

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
            'delUser' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
            ),
            'addUser' => array(
                'user_name' => array('name' => 'user_name', 'require' => true, 'desc' => '名称'),
                'user_account' => array('name' => 'user_account', 'require' => true, 'desc' => '名称'),
                'group_id' => array('name' => 'group_id', 'require' => true, 'desc' => '名称'),
                'remark' => array('name' => 'remark', 'require' => false, 'desc' => '描述'),
            ),
            'upUser' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => 'ID'),
                'status' => array('name' => 'status', 'require' => false, 'desc' => '状态'),
            ),
            'modPwd' => array(
                'pwd' => array('name' => 'pwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '旧密码'),
                'newPwd' => array('name' => 'newPwd', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '新密码'),
            ),
            'addGroup' => array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => '名称'),
                'remark' => array('name' => 'remark', 'require' => true, 'desc' => '描述'),
            ),
            'delGroup' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => 'id'),
            ),
            'getsUser' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => '20', 'desc' => '数量'),
                'name' => array('name' => 'name', 'desc' => ''),
                'name_status' => array('name' => 'name_status', 'type' => 'int', 'desc' => ''),
                'type' => array('name' => 'type', 'desc' => ''),
                'status' => array('name' => 'status', 'desc' => ''),
                'group_id' => array('name' => 'group_id', 'desc' => '')
            ),
            'handoverCustomerUser' => array(
                'from_user_id' => array('name' => 'from_user_id', 'desc' => '交接'),
                'to_user_id' => array('name' => 'to_user_id', 'type' => 'int', 'desc' => '交接'),
            ),
            'upFiltrationStatus' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => 'ID'),
                'status' => array('name' => 'status', 'require' => false, 'desc' => '状态'),
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
        $group_id = $this->group_id;
        $remark = $this->remark;

        $res = $this->_getUserDomain()->register($user_name, $user_account, $group_id, $remark);
        return $this->api_success($res);
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
    public function delUser()
    {
        $id = $this->id;
        $res = $this->_getUserDomain()->delUser($id);
        $this->upUserCheck($id);
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function getsUser()
    {
        $page = $this->page;
        $limit = $this->limit;

        $name = $this->name;
        $type = $this->type;
        $status = $this->status;
        $group_id = $this->group_id;

        $res = $this->_getUserDomain()->getsUser($page, $limit, $name, $type, $status, $group_id);
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function upUser()
    {
        $id = $this->id;
        $status = $this->status;

        $res = $this->_getUserDomain()->upUser($id, $status);
        if ($res == 1) {
            $this->upUserCheck($id);
            return $this->api_success();
        } else {
            return $this->api_error(8004, $res);
        }

    }

    /**
     * 筛选状态
     */
    public function upFiltrationStatus()
    {
        $id = $this->id;
        $status = $this->status;

        $res = $this->_getUserDomain()->upFiltrationStatus($id, $status);
        if ($res == 1) {
            $this->upUserCheck($id);
            return $this->api_success();
        } else {
            return $this->api_error(8004, $res);
        }

    }

    /**
     * 用户信息
     */
    public function getAllUserList()
    {
        $res = $this->_getUserDomain()->getAllUserList();
        return $this->api_success($res);
    }

    /**
     * 用户信息
     */
    public function getResourceUser()
    {
        $res = $this->_getUserDomain()->getResourceUser();
        return $this->api_success($res);
    }

    /**
     * 获取人员小组
     */
    public function getGroup()
    {
        $res = $this->_getUserDomain()->getGroup();
        return $this->api_success($res);
    }

    /**
     *  添加资源类型
     */
    public function addGroup()
    {
        $name = $this->name;
        $remark = $this->remark;
        $res = $this->_getUserDomain()->addGroup($name, $remark);
        if ($res == 1) {
            return $this->api_success();
        } else {
            return $this->api_error(8002, $res);
        }
    }

    /**
     *  删除资源类型
     */
    public function delGroup()
    {
        $id = $this->id;
        if (intval($id) === 1) {
            return $this->api_error(8003, '无法删除默认类型');
        }
        $this->_getUserDomain()->delGroup($id);
        return $this->api_success();
    }

    /**
     *  用户交接
     */
    public function handoverCustomerUser()
    {
        $from_user_id = $this->from_user_id;
        $to_user_id = $this->to_user_id;

        $res = $this->_getUserDomain()->handoverCustomerUser(explode(",", $from_user_id), $to_user_id);
        if ($res == 1) {
            return $this->api_success();
        } else {
            return $this->api_error(8004, $res);
        }
    }

}
