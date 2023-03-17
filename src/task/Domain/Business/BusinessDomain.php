<?php

namespace Task\Domain\Business;

use Task\Common\BaseDomain;
use PhalApi\Tool;

class BusinessDomain extends BaseDomain
{


    public function getBusiness($platform_id)
    {
        return $this->_getBusinessModel()->getBusiness($platform_id);
    }



}
