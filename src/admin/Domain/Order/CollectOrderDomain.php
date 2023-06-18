<?php

namespace Admin\Domain\Order;

use Admin\Common\ComRedis;
use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectOrderDomain extends BaseDomain
{


    public function getCollectOrder($id)
    {
        $order = $this->_getCollectOrderModel()->getCollectOrder($id);

        if ($order['code_id'] > 0) {
            $pay_info = $this->_getCollectInfoModel()->getCollectInfo($order['code_id']);
            $order['pay_info'] = $pay_info['pay_info'];
        }

        return $order;
    }

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
        $business = $this->_getBusinessModel()->getsBusinessId($order['business_id']);

        if (empty($order) || empty($order['callback_url']) || empty($business)) {
            \PhalApi\DI()->logger->debug('回调异常 ->', $order);
            return;
        }

        //todo 推送消息
        $b_status = 0;
        if (3 == $order['status'])
            $b_status = 1;
        $data = array('order_no' => $order['order_no'], 'business_no' => $order['business_no'], 'status' => $b_status, 'amount' => $order['order_amount']);

        $sign = $this->encryptAppKey($data, $business['private_key']);
        $data['sign'] = $sign;
        $this->_getFiltrationAPI()->pushUrl($order['callback_url'], $data);


    }


}
