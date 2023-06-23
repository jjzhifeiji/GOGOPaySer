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

    public function getCollectOrderList($status, $page, $limit, $order_no, $business_no, $amount, $order_fee, $user_name, $business_name, $pay_type)
    {
        $file = array();

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }
        if (!empty($order_no)) {
            $file['order_no'] = $order_no;
        }
        if (!empty($business_no)) {
            $file['business_no'] = $business_no;
        }
        if (!empty($amount)) {
            $file['order_amount'] = $amount;
        }
        if (!empty($order_fee)) {
            $file['cost_free'] = $order_fee;
        }
        if (!empty($user_name)) {
            $file['user_name'] = $user_name;
        }
        if (!empty($business_name)) {
            $file['business_name'] = $business_name;
        }
        if (!empty($pay_type)) {
            $file['pay_type'] = $pay_type;
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

    public function repairCollectOrder($id)
    {

        $order = $this->_getCollectOrderModel()->getCollectOrder($id);

        if ($order['status'] != 4) {
            return '订单有误,无法补单';
        }

        //更新订单
        $file = array('id' => $order['id'], 'status' => 4);
        $data = array('status' => 2);
        $res = $this->_getCollectOrderModel()->upCollectOrder($file, $data);

        if ($res > 0) {
            $user = $this->_getUserModel()->getUserId($order['user_id']);
            $collect = new \App\Domain\Order\CollectOrderDomain();
            return $collect->repairCollectOrder($user, $id, '');
        } else {
            return '补单失败';
        }
    }

}
