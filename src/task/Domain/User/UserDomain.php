<?php

namespace Task\Domain\User;

use Task\Common\BaseDomain;
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
    public function register($user_name, $user_account, $group_id, $remark)
    {
        $group = $this->_getGroupModel()->getGroupId($group_id);
        if (empty($group)) {
            return '小组有误';
        }

        //注册用户
        $newUserInfo['user_name'] = $user_name;
        $newUserInfo['user_account'] = $user_account;
        $newUserInfo['pwd'] = $this->encryptPassword('123456');
        $newUserInfo['status'] = 1;
        $newUserInfo['type'] = 1;
        $newUserInfo['device_id'] = "";
        $newUserInfo['group_id'] = $group['id'];
        $newUserInfo['group_name'] = $group['name'];
        $newUserInfo['remark'] = $remark;

        $this->_getUserModel()->insert($newUserInfo);


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

    public function delUser($id)
    {
        return $this->_getUserModel()->delUser($id);
    }

    public function getsUser($page, $limit, $name, $type, $status, $group_id)
    {
        $file = array();
        $like_file = '1=1';
        if (is_numeric($status)) {
            $file['status'] = $status;
        }
        if (is_numeric($type)) {
            $file['type'] = $type;
        }
        if (is_numeric($group_id)) {
            $file['group_id'] = $group_id;
        }
        if (!empty($name)) {
            $like_file = 'user_account like "%' . $name . '%" or user_name like "%' . $name . '%"';
        }

        return $this->_getUserModel()->getUserList($file, $like_file, $page, $limit);
    }

    public function getAllUserList()
    {
        return $this->_getUserModel()->getAllUserList();
    }

    public function getsUserChildAll($u_id)
    {

        $file = array();
        $file['parent_id'] = $u_id;

        return $this->_getUserModel()->getsFileUser($file);
    }

    /**
     * 获取用户信息
     * @param  $userId
     * @return mixed
     */
    public function getUserInfoAccount($user_account)
    {
        return $this->_getUserModel()->getInfoAccount($user_account);
    }

    public function modPwd($user, $password, $newPwd)
    {
        $encryptPassword = $this->encryptPassword($password);
        if ($encryptPassword !== $user['pwd']) {
            \PhalApi\DI()->logger->debug('pwd', $encryptPassword);
            \PhalApi\DI()->logger->debug('userpwd', $user['pwd']);
            return false;
        } else {
            return $this->_getUserModel()->upUserPwd($user['id'], $this->encryptPassword($newPwd));
        }
    }

    public function getGroup()
    {
        return $this->_getGroupModel()->getGroup();
    }

    public function addGroup($name, $remark)
    {
        $res = $this->_getGroupModel()->getGroupName($name);
        if (!empty($res)) {
            if (intval($res['status']) === 1) {
                return '已存在';
            } else {
                return $this->_getGroupModel()->upGroup($res['id'], $remark);
            }
        } else {
            $data = array(
                'name' => $name,
                'create_time' => date("Y-m-d H:i:s"),
                'remark' => $remark
            );
            return $this->_getGroupModel()->addGroup($data);
        }
    }

    public function delGroup($id)
    {
        return $this->_getGroupModel()->delGroup($id);
    }

    public function handoverCustomerUser($from_user_id, $to_user_id)
    {
        //todo 用户交接
        $users = $this->_getUserModel()->getUsers($from_user_id);
        $user = $this->_getUserModel()->getInfo($to_user_id);
        if (empty($user)) {
            return "用户有误";
        }
        $arr_id = array();
        foreach ($users as $u) {
            array_push($arr_id, $u['id']);
        }
        $customers = $this->_getCustomerModel()->getsCustomerForUserIds($arr_id);
        if (empty($customers) || sizeof($customers) == 0) {
            return "客户有误";
        }

        // 变更原有客户状态
        // 复制客户信息
        $res_ids = array();
        $c_ids = array();
        $new_customers = array();
        foreach ($customers as $c) {

            $tem = array(
                'res_id' => $c['res_id'],
                'name' => $c['name'],
                'mobile' => $c['mobile'],
                'status' => $c['status'],
                'create_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s"),
                'user_id' => $user['id'],
                'group_id' => $user['group_id'],
                'contact_times' => $c['contact_times'],
                'desire' => $c['desire'],
                'is_wechat' => $c['is_wechat'],
                'remark' => $c['remark']
            );

            array_push($c_ids, $c['id']);
            array_push($res_ids, $c['res_id']);
            array_push($new_customers, $tem);
        }
        $this->_getCustomerModel()->upCustomer($c_ids, array('status' => 12, 'update_time' => date("Y-m-d H:i:s")));
        $this->_getCustomerModel()->insertMore($new_customers);

        // 更改资源所属信息
        $rd = array(
            'group_id' => $user['group_id'],
            'user_id' => $user['id']
        );
        $this->_getResourceModel()->upResource($res_ids, $rd);

        return 1;
    }

    public function getResourceUser()
    {
        $users = $this->_getUserModel()->getAllUserList();
        foreach ($users as $k => $u) {
            $ur = $this->_getResourceModel()->getResourceUser($u['id']);
            $users[$k]['num'] = $ur;
        }
        return $users;
    }

    //修改用户信息
    public function upUser($id, $status)
    {
        $user = $this->_getUserModel()->getUserId($id);
        if (empty($user) || $status == $user['status'] || !is_numeric($status)) {
            return '用户有误';
        }
        $data = array(
            'status' => $status
        );
        $this->_getUserModel()->upUser($id, $data);
        return 1;
    }

    //修改用户过滤筛选信息
    public function upFiltrationStatus($id, $status)
    {
        $user = $this->_getUserModel()->getUserId($id);
        if (empty($user) || $status == $user['filtration_status'] || !is_numeric($status)) {
            return '用户有误';
        }
        $data = array(
            'filtration_status' => $status
        );
        $this->_getUserModel()->upUser($id, $data);
        return 1;
    }


}
