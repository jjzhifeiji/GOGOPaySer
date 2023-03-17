<?php

namespace Proxy\Common;

use Proxy\Domain\User\UserDomain;
use PhalApi\Api;

class BaseController extends Api
{
    protected $member_arr = array();


    protected function userCheck()
    {
        $isWhiteList = $this->isServiceWhitelist();
//        if ($isWhiteList) {
//            return;
//        }
        $token = \PhalApi\DI()->request->get('token');
        if (empty($token)) {
            $token = \PhalApi\DI()->request->getHeader("Token");
        }
        if (empty($token) && !$isWhiteList) {
            \PhalApi\DI()->logger->debug('token', $token);
            $this->api_error(502, 'token_null');
        }
        $this->member_arr = \PhalApi\DI()->cache->get($token);
        if ((empty($this->member_arr)) && !$isWhiteList) {
            $this->api_error(501, 'token_error');
        } else {
            \PhalApi\DI()->cache->set($token, $this->member_arr, 60 * 60 * 1);
        }
    }

    protected function upUserCheck($user_id)
    {
        $user = $this->_getUserDomain()->getUserInfo($user_id);
        \PhalApi\DI()->cache->delete($user['token']);
        \PhalApi\DI()->cache->set($user['token'], $user, 60 * 60 * 1);
    }

    //用户ID获取token
    protected function getToken($user_id)
    {
        $admin = $this->_getAdminDomain()->getAdminId($user_id);
        \PhalApi\DI()->cache->delete($admin['token']);

        $token = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 32));

        $adminModel = new AdminModel();
        $adminModel->upToken($user_id, $token);
        $admin['token'] = $token;
        \PhalApi\DI()->cache->set($token, $admin, 60 * 60 * 1);
        return $token;
    }

    //用户ID获取token
    protected function removeToken($user)
    {
        \PhalApi\DI()->cache->delete($user['token']);
        $adminModel = new AdminModel();
        $adminModel->upToken($user['id'], "");
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

    //管理员用户
    protected $AdminDomain;

    protected function _getAdminDomain()
    {
        return empty($this->AdminDomain) ? new AdminDomain() : $this->AdminDomain;
    }

    //资源类型
    protected $ResourceTypeDomain;

    protected function _getResourceTypeDomain()
    {
        return empty($this->ResourceTypeDomain) ? new  ResourceTypeDomain() : $this->ResourceTypeDomain;
    }

    //资源
    protected $ResourceDomain;

    protected function _getResourceDomain()
    {
        return empty($this->ResourceDomain) ? new  ResourceDomain() : $this->ResourceDomain;
    }


    //客户管理
    protected $CustomerDomain;

    protected function _getCustomerDomain()
    {
        return empty($this->CustomerDomain) ? new  CustomerDomain() : $this->CustomerDomain;
    }


    //报表
    protected $ReportDomain;

    protected function _getReportDomain()
    {
        return empty($this->ReportDomain) ? new  ReportDomain() : $this->ReportDomain;
    }


    //导入记录
    protected $ImportResultDomain;

    protected function _getImportResultDomain()
    {
        return empty($this->ImportResultDomain) ? new  ImportResultDomain() : $this->ImportResultDomain;
    }

    //公共
    protected $CommonDomain;

    protected function _getCommonDomain()
    {
        return empty($this->CommonDomain) ? new  CommonDomain() : $this->CommonDomain;
    }

    //合同
    protected $ContractDomain;

    protected function _getContractDomain()
    {
        return empty($this->ContractDomain) ? new  ContractDomain() : $this->ContractDomain;
    }


}
