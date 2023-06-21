<?php

namespace Task\Common;

use Task\Domain\Business\BusinessDomain;
use Task\Domain\Order\CollectOrderDomain;
use Task\Domain\Order\OutOrderDomain;
use Task\Domain\User\UserDomain;
use PhalApi\Api;

class BaseController extends Api
{

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
    protected $UserDomain;

    protected function _getUserDomain(): UserDomain
    {
        return empty($this->UserDomain) ? new UserDomain() : $this->UserDomain;
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


}
