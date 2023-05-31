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

    public function pushOrder($order_id)
    {
        $order = $this->_getCollectOrderModel()->getCollectOrder($order_id);

        if (empty($order)||empty($order['callback_url'])) {
            \PhalApi\DI()->logger->debug('回调异常 ->', $order);
            return;
        }

        //todo 推送消息
        $b_status = 0;
        if (3 == $order['status'])
            $b_status = 1;
        $data = array('order_no' => $order['order_no'], 'business_no' => $order['business_no'], 'status' => $b_status, 'amount' => $order['order_amount']);
        $this->_getFiltrationAPI()->pushUrl($order['callback_url'], $data);


    }


}
