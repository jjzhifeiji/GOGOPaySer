<?php

namespace Admin\Common;

use Admin\Domain\Admin\AdminDomain;
use Admin\Domain\Business\BusinessDomain;
use Admin\Domain\Common\CommonDomain;
use Admin\Domain\Order\CollectOrderDomain;
use Admin\Domain\Order\OutOrderDomain;
use Admin\Domain\User\CollectInfoDomain;
use Admin\Domain\User\UserDomain;
use PhalApi\Api;

class BaseController extends Api
{
    protected $member_arr = array();


    protected function userCheck()
    {
        $isWhiteList = $this->isServiceWhitelist();

        $token = \PhalApi\DI()->request->get('token');
        if (empty($token)) {
            $token = \PhalApi\DI()->request->getHeader("Token");
        }
        if (empty($token) && !$isWhiteList) {
            $this->api_error(502, 'token_null');
        }
        $this->member_arr = $this->getCache($token);
        if ((empty($this->member_arr)) && !$isWhiteList) {
            $this->api_error(501, 'token_error');
        } else {
            $this->upUserCheck($token, $this->member_arr);
        }
    }

    protected function upUserCheck($token, $admin)
    {
        $this->setCache($token, $admin);
        $this->setCache($admin['id'], $token);
    }

    //用户ID获取token
    protected function getToken($admin_id)
    {
        $admin = $this->_getAdminDomain()->getAdminId($admin_id);
        $this->removeToken($admin_id);

        $token = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 32));

        $admin['token'] = $token;
        $this->setCache($token, $admin);
        $this->setCache($admin['id'], $token);
        return $token;
    }


    //用户ID移除
    protected function removeToken($admin_id)
    {
        $token = $this->getCache($admin_id);
        $admin = $this->getCache($token);
        $this->delCache($token);
        $this->delCache($admin['id']);
    }

    protected function getCache($key)
    {
        return \PhalApi\DI()->cache->get('admin' . $key);
    }

    protected function setCache($key, $val, $time = 60 * 60 * 1)
    {
        \PhalApi\DI()->cache->set('admin' . $key, $val, );
    }

    protected function delCache($key)
    {
        \PhalApi\DI()->cache->delete('admin' . $key);
    }

    protected function api_success($data = '')
    {
        return $data;
    }

    /**
     * API错误返回
     * @param   $code $msg
     * @param string $msg
     * @return mixed
     * @throws RequestException
     */
    protected function api_error($code, string $msg = '')
    {
        throw new RequestException($msg, $code);
    }


    //------------------------------------------------------------------------------------------------------------
    //用户
    protected $UserDomain;

    protected function _getUserDomain(): UserDomain
    {
        return empty($this->UserDomain) ? new UserDomain() : $this->UserDomain;
    }

    //管理员用户
    protected $AdminDomain;

    protected function _getAdminDomain(): AdminDomain
    {
        return empty($this->AdminDomain) ? new AdminDomain() : $this->AdminDomain;
    }


    protected $BusinessDomain;

    protected function _getBusinessDomain(): BusinessDomain
    {
        return empty($this->BusinessDomain) ? new BusinessDomain() : $this->BusinessDomain;
    }

    protected $CollectOrderDomain;

    protected function _getCollectOrderDomain(): CollectOrderDomain
    {
        return empty($this->CollectOrderDomain) ? new CollectOrderDomain() : $this->CollectOrderDomain;
    }


    protected $OutOrderDomain;

    protected function _getOutOrderDomain(): OutOrderDomain
    {
        return empty($this->OutOrderDomain) ? new OutOrderDomain() : $this->OutOrderDomain;
    }


    protected $CollectInfoDomain;

    protected function _getCollectInfoDomain(): CollectInfoDomain
    {
        return empty($this->CollectInfoDomain) ? new CollectInfoDomain() : $this->CollectInfoDomain;
    }

    protected $CommonDomain;

    protected function _getCommonDomain(): CommonDomain
    {
        return empty($this->CommonDomain) ? new CommonDomain() : $this->CommonDomain;
    }


}
