<?php

namespace Admin\Domain\Admin;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 * 用户
 *
 * - 可用于自动生成一个新用户
 *
 * @author dogstar 20200331
 */
class AdminDomain extends BaseDomain
{

    /**
     * 获取用户信息
     * @param  $userId
     * @return mixed
     */
    public function getAdminAccount($account)
    {
        return $this->_getAdminModel()->getAdminAccount($account);
    }


    /**
     * 获取用户信息
     * @param  $userId
     * @return mixed
     */
    public function getAdminId($id)
    {
        return $this->_getAdminModel()->getAdminId($id);
    }

    public function addAdmin($name, $account, $pwd)
    {
        $data = array(
            'name' => $name,
            'account' => $account,
            'pwd' => $pwd,
            'type' => 2,
            'status' => 1,
            'desc' => ''
        );
        return $this->_getAdminModel()->insert($data);
    }

    public function delAdmin($id)
    {
        $data = array(
            'status' => 0,
        );
        return $this->_getAdminModel()->update($id, $data);
    }

    public function getAdminList($page, $limit)
    {
        $file = array(
            'status' => 1
        );
        return $this->_getAdminModel()->getAdminList($file, $page, $limit);
    }

    public function setSecret($id, $secret)
    {
        $data = array(
            'google_auth' => $secret,
        );
        $this->_getAdminModel()->update($id, $data);
        return;
    }


}
