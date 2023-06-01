<?php

namespace Business\Common;

use Business\Domain\Business\BusinessDomain;
use Business\Domain\Order\CollectOrderDomain;
use Business\Domain\Order\OutOrderDomain;
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
        $this->member_arr = $this->getTCache($token);
        if ((empty($this->member_arr)) && !$isWhiteList) {
            $this->api_error(501, 'token_error');
        } else {
            $this->upUserCheck($token, $this->member_arr);
        }
    }

    protected function upUserCheck($token, $user)
    {
        $this->setTCache($token, $user);
        $this->setTCache($user['id'], $token);
    }

    //用户ID获取token
    protected function getToken($user_id)
    {
        $user = $this->_getBusinessDomain()->getBusiness($user_id);
        $this->removeToken($user_id);

        $token = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 32));

        $user['token'] = $token;
        $this->setTCache($token, $user);
        $this->setTCache($user['id'], $token);
        return $token;
    }


    //用户ID移除
    protected function removeToken($user_id)
    {
        $token = $this->getTCache($user_id);
        $user = $this->getTCache($token);
        $this->delTCache($token);
        $this->delTCache($user['id']);
    }

    private function getTCache($key)
    {
        return \PhalApi\DI()->cache->get('business' . $key);
    }

    private function setTCache($key, $val)
    {
        \PhalApi\DI()->cache->set('business' . $key, $val, 60 * 60 * 1);
    }

    private function delTCache($key)
    {
        \PhalApi\DI()->cache->delete('business' . $key);
    }

    protected function getCache($key)
    {
        return \PhalApi\DI()->cache->get($key);
    }

    protected function setCache($key, $val, $expire = 60 * 60 * 1)
    {
        \PhalApi\DI()->cache->set($key, $val, $expire);
    }

    protected function delCache($key)
    {
        \PhalApi\DI()->cache->delete($key);
    }


    protected function api_success($data = array())
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
    protected function api_error($code, $msg = '')
    {
        throw new RequestException($msg, $code);
    }


    //------------------------------------------------------------------------------------------------------------
    //用户
    protected $UserDomain;

    protected function _getUserDomain()
    {
        return empty($this->UserDomain) ? new UserDomain() : $this->UserDomain;
    }

    protected $BusinessDomain;

    protected function _getBusinessDomain()
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


}
