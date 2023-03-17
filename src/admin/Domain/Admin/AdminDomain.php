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


}
