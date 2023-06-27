<?php

namespace App\Domain\User;

use App\Common\BaseDomain;
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

    // 密码加密算法
    private function encryptPassword($password)
    {
        return md5(md5(md5($password)));
    }

    // 账号登录
    public function login($user_account, $password)
    {
        $user = $this->_getUserModel()->getInfoAccount($user_account);
        if (!$user || $user['status'] == 0) {
            return false;
        }

        $encryptPassword = $this->encryptPassword($password);
        if ($encryptPassword !== $user['pwd']) {
            \PhalApi\DI()->logger->debug('pwd', $encryptPassword);
            \PhalApi\DI()->logger->debug('userpwd', $user['pwd']);
            return false;
        }

        return true;
    }

    /**
     * 获取用户信息
     * @param  $userId
     * @return mixed
     */
    public function getUserInfo($userId)
    {
        $res = $this->_getUserModel()->getInfo($userId);
        return $res;
    }

    public function getMyInfo($userId)
    {
        $res = $this->_getUserModel()->getInfo($userId);
        return $res;
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

    public function getMyBill($id, $page, $limit)
    {
        $file = array('user_id' => $id);
        return $this->_getUserAmountRecordModel()->getMyRecord($file, $page, $limit);
    }

    public function register($user_name, $user_account, $pwd, $invitation_code)
    {

        $invitation = $this->_getInvitationModel()->getMyInvitationCode($invitation_code);
        if (empty($invitation) || $invitation['invitationed_num'] >= $invitation['invitation_num']) {
            return '邀请码有误';
        }

        $u = $this->_getUserModel()->getInfoAccount($user_account);
        if (!empty($u)) {
            return '已存在';
        }

        $group = $this->_getUserModel()->getInfo($invitation['user_id']);
        if (empty($group)) {
            return '邀请码有误2';
        }

        //注册用户
        $newUserInfo['user_name'] = $user_name;
        $newUserInfo['account'] = $user_account;
        $newUserInfo['pwd'] = $this->encryptPassword($pwd);
        $newUserInfo['status'] = 1;
        $newUserInfo['type'] = 1;
        $newUserInfo['group_id'] = $group['id'];
        $newUserInfo['group_name'] = $group['user_name'];
        $newUserInfo['group_account'] = $group['account'];
        $newUserInfo['account_amount'] = 0;
        $newUserInfo['is_top'] = 0;
        $newUserInfo['bank_collect_val'] = $invitation['bank_max_val'];
        $newUserInfo['wx_collect_val'] = $invitation['wx_max_val'];
        $newUserInfo['ali_collect_val'] = $invitation['ali_max_val'];
        $newUserInfo['bank_out_val'] = $invitation['bank_out_max_val'];
        $newUserInfo['wx_out_val'] = $invitation['wx_out_max_val'];
        $newUserInfo['ali_out_val'] = $invitation['ali_out_max_val'];

        $this->_getUserModel()->insert($newUserInfo);
        $this->_getInvitationModel()->plusMyInvitation($invitation_code, $invitation['invitation_num'] + 1);


        return true;

    }

    public function setAssign($id, $status)
    {
        if ($status == 1) {
            $this->_getUserModel()->setAssign($id, 1);
            $this->_getUserCollectInfoModel()->upCollectInfo(array('user_id' => $id), array('assign' => 1));
        } else {
            $this->_getUserModel()->setAssign($id, 0);
            $this->_getUserCollectInfoModel()->upCollectInfo(array('user_id' => $id), array('assign' => 0));
        }
        return '';
    }

}
