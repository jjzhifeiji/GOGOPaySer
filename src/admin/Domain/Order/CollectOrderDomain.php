<?php

namespace Admin\Domain\Order;

use Admin\Common\ComRedis;
use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectOrderDomain extends BaseDomain
{


    public function getCollectOrderList($status, $page, $limit)
    {
        $file = array();

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }

        return $this->_getCollectOrderModel()->getCollectOrderList($file, $page, $limit);
    }


}
