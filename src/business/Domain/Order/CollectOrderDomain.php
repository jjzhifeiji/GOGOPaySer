<?php

namespace Business\Domain\Order;

use Business\Common\ComRedis;
use Business\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectOrderDomain extends BaseDomain
{


    public function getCollectOrderList($user, $status, $start_time, $end_time, $page, $limit)
    {
        $file = array(
            'business_id' => $user['id']
        );

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }

        return $this->_getCollectOrderModel()->getCollectOrderList($file, $start_time, $end_time,  $page, $limit);
    }


}
