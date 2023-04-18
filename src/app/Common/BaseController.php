<?php

namespace App\Common;

use App\Domain\Common\CommonDomain;
use App\Domain\Order\CollectOrderDomain;
use App\Domain\Order\OutOrderDomain;
use App\Domain\User\CollectInfoDomain;
use App\Domain\User\UserDomain;
use PhalApi\Api;
use PhalApi\Request\Parser;

class BaseController extends Api
{
    protected $member_arr = array();

    protected function checkRequestMethod()
    {
        $request = \PhalApi\DI()->request->getAll();
        \PhalApi\DI()->logger->debug('$request', $request);
        return parent::checkRequestMethod();
    }

    protected function userCheck()
    {

        $isWhiteList = $this->isServiceWhitelist();

        $token = \PhalApi\DI()->request->get('token');
        if (empty($token)) {
            $token = \PhalApi\DI()->request->getHeader("Token");
        }
        if (empty($token) && !$isWhiteList) {
            \PhalApi\DI()->logger->debug('token', $token);
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
        $user = $this->_getUserDomain()->getUserInfo($user_id);
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
        return \PhalApi\DI()->cache->get('user' . $key);
    }

    private function setTCache($key, $val)
    {
        \PhalApi\DI()->cache->set('user' . $key, $val, 60 * 60 * 1);
    }

    private function delTCache($key)
    {
        \PhalApi\DI()->cache->delete('user' . $key);
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


    //公共产品
    protected $CommonDomain;

    protected function _getCommonDomain()
    {
        return empty($this->CommonDomain) ? new CommonDomain() : $this->CommonDomain;
    }

    protected $CollectOrderDomain;

    protected function _getCollectOrderDomain()
    {
        return empty($this->CollectOrderDomain) ? new CollectOrderDomain() : $this->CollectOrderDomain;
    }

    protected $OutOrderDomain;

    protected function _getOutOrderDomain()
    {
        return empty($this->OutOrderDomain) ? new OutOrderDomain() : $this->OutOrderDomain;
    }

    protected $CollectInfoDomain;

    protected function _getCollectInfoDomain()
    {
        return empty($this->CollectInfoDomain) ? new CollectInfoDomain() : $this->CollectInfoDomain;
    }


}
