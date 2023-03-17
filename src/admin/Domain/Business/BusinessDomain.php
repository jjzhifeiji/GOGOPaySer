<?php

namespace Admin\Domain\Business;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

class BusinessDomain extends BaseDomain
{


    public function getsBusiness($page, $limit)
    {
        return $this->_getBusinessModel()->getsBusiness($page, $limit);
    }


    public function addBusiness($name, $account, $collect_free, $out_free)
    {
        $private_key = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 32));
        $platform_sn = strtoupper(substr(sha1(uniqid(NULL, TRUE)) . sha1(uniqid(NULL, TRUE)), 0, 10));

        $date = array(
            'platform_sn' => 'GP' . $platform_sn,
            'name' => $name,
            'account' => $account,
            'pwd' => 123456,
            'status' => 1,
            'private_key' => $private_key,
            'business_amount' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'collect_free' => $collect_free,
            'out_free' => $out_free,
            'whitelist' => ""
        );
        $this->_getBusinessModel()->addBusiness($date);
    }


}
