<?php

namespace Business\Domain\Business;

use Business\Common\BaseDomain;
use PhalApi\Tool;

class BusinessDomain extends BaseDomain
{


    public function getsBusiness($page, $limit)
    {
        return $this->_getBusinessModel()->getsBusiness($page, $limit);
    }

    public function getBusiness($user_id)
    {
        return $this->_getBusinessModel()->getBusiness(array('id' => $user_id));
    }

    public function getBusinessAccount($account)
    {
        return $this->_getBusinessModel()->getBusiness(array('account' => $account));
    }

    public function getsAmountLog($id, $type, $page, $limit)
    {
        $file = array(
            'business_id' => $id,
        );

        if (is_numeric($type) && $type > 0) {
            $file['type'] = $type;
        }

        return $this->_getBusinessAmountRecordModel()->getBusinessRecord($file, $page, $limit);
    }

    public function setSecret($id, $secret)
    {
        $data = array(
            'google_auth' => $secret,
        );
        return $this->_getBusinessModel()->update($id, $data);
    }

    public function modPwd($id, $pwd)
    {
        $data = array(
            'pwd' => $pwd,
        );
        $this->_getBusinessModel()->update($id, $data);
    }


}
