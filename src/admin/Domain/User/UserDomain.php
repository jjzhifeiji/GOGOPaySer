<?php

namespace Admin\Domain\User;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 * 用户
 *
 * - 可用于自动生成一个新用户
 *
 * @author dogstar 20200331
 */
class UserDomain extends BaseDomain
{

    /**
     * 注册新用户
     */
    public function register($user_name, $user_account, $collect_free, $out_free)
    {

        $is_top = 1;

        $u = $this->_getUserModel()->getUserAccount($user_account);
        if (!empty($u)) {
            return '已存在';
        }

        //注册用户
        $newUserInfo['user_name'] = $user_name;
        $newUserInfo['account'] = $user_account;
        $newUserInfo['pwd'] = $this->encryptPassword('123456');
        $newUserInfo['status'] = 1;
        $newUserInfo['type'] = 1;
        $newUserInfo['group_id'] = '';
        $newUserInfo['group_name'] = '';
        $newUserInfo['group_account'] = '';
        $newUserInfo['account_amount'] = 0;
        $newUserInfo['is_top'] = $is_top;
        $newUserInfo['collect_free'] = $collect_free;
        $newUserInfo['out_free'] = $out_free;

        $this->_getUserModel()->addUser($newUserInfo);

        $uu = $this->_getUserModel()->getUserAccount($user_account);
        $data = array(
            'group_id' => $uu['id'],
            'group_name' => $uu['user_name'],
            'group_account' => $uu['account']
        );
        $this->_getUserModel()->update($uu['id'], $data);

        return true;
    }

    // 密码加密算法
    private function encryptPassword($password)
    {
        return md5(md5(md5($password)));
    }


    /**
     * 获取用户信息
     * @param  $userId
     * @return mixed
     */
    public function getUserInfo($userId)
    {
        return $this->_getUserModel()->getInfo($userId);
    }

    public function getUser($id)
    {
        return $this->_getUserModel()->get($id);
    }

    public function modUserStatus($id, $status)
    {
        $user = $this->_getUserModel()->getUserId($id);
        if (empty($user) || $status == $user['status'] || !is_numeric($status)) {
            return '用户有误';
        }
        $data = array(
            'status' => $status
        );
        $this->_getUserModel()->modUserStatus($id, $data);
    }

    public function getUserList($page, $limit, $name, $status, $group_id)
    {
        $file = array();
        $like_file = '1=1';
        if (is_numeric($status)) {
            $file['status'] = $status;
        }

        if (is_numeric($group_id)) {
            $file['group_id'] = $group_id;
        }
        if (!empty($name)) {
            $like_file = 'account like "%' . $name . '%" or user_name like "%' . $name . '%"';
        }

        return $this->_getUserModel()->getUserList($file, $like_file, $page, $limit);
    }


    public function getsUserGroup()
    {
        return $this->_getUserModel()->getsUserGroup();
    }

    public function getGroupUser()
    {
        $all = $this->_getUserModel()->getsAllUser();

        $res = array();
        foreach ($all as $user) {
            if ($user['is_top'] == 1) {
                array_push($res, $user);
            }
        }

        foreach ($res as $k => $top) {
            $u = array();
            foreach ($all as $user) {
                \PhalApi\DI()->logger->debug('getGroupUser', $top['id'] == $user['group_id']);
                \PhalApi\DI()->logger->debug('getGroupUser' . $top['id'] . '  ' . $user['group_id']);

                if ($top['id'] == $user['group_id']) {
                    array_push($u, $user);
                }
            }
            \PhalApi\DI()->logger->debug('getGroupUser' . $u);

            array_push($res[$k]['child'], $u);

            \PhalApi\DI()->logger->debug('getGroupUser' . $res);

        }

        return $res;
    }


}
