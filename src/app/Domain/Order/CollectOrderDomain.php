<?php

namespace App\Domain\Order;

use App\Common\ComRedis;
use App\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectOrderDomain extends BaseDomain
{


    public function getWaitCollectOrderList($user)
    {
        $file = array(
            'status' => 1
        );
        return $this->_getCollectOrderModel()->getWaitCollectOrderList($file);
    }

    public function getCollectOrder(array $user, $id)
    {
        return $this->_getCollectOrderModel()->getCollectOrder($id);
    }

    public function getCollectOrderList($user, $status, $page, $limit)
    {
        $file = array(
            'user_id' => $user['id']
        );

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }

        return $this->_getCollectOrderModel()->getCollectOrderList($file, $page, $limit);
    }

    public function takeCollectOrder($id, $user)
    {

        //查订单
        $order = $this->_getCollectOrderModel()->getTakeCollectOrder($id);

        $code = $this->_getUserCollectInfoModel()->getCode($user['id'], $order['pay_type']);
        if (empty($code)) {
            return "code error";
        }

        $orderLock = 'collect' . $id;
        $isLock = ComRedis::lock($orderLock);
        if (!$isLock) {
            return "too hot";
        }

        if (empty($order)) {
            ComRedis::unlock($orderLock);
            return "too late";
        }

        $u = $this->_getUserModel()->getUserInfo($user['id']);
        if ($u['account_amount'] < $order['order_amount']) {
            ComRedis::unlock($orderLock);
            return "余额不足";
        }


        //扣款
        $res = $this->_getUserModel()->changeUserAmount($user['id'], $order['order_amount'], false);

        if (empty($res)) {
            ComRedis::unlock($orderLock);
            return "扣款失败";
        }

        //用户金额log
        $logData = array(
            'user_id' => $user['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $res['beforeAmount'],
            'change_amount' => $res['changAmount'],
            'result_amount' => $res['afterAmount'],
            'type' => 1,
            'business_id' => $order['business_id'],
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '接单扣款',
        );
        $this->_getUserAmountRecordModel()->addUserLog($logData);

        //更新订单
        $file = array('id' => $order['id'], 'status' => 1);
        $data = array('status' => 2, 'user_id' => $user['id'], 'code_id' => $code['id']);
        $this->_getCollectOrderModel()->takeCollectOrder($file, $data);

        ComRedis::unlock($orderLock);

        return null;
    }

    public function configCollectOrderList($user, $id, $url = '')
    {
        $user = $this->_getUserModel()->getInfo($user['id']);
        $order = $this->_getCollectOrderModel()->getCollectOrder($id);

        if ($order['status'] !== 2) {
            return '订单有误';
        }

        if ($order['user_id'] !== $user['id']) {
            return '用户有误';
        }


        //todo 商户金额
        $res = $this->_getBusinessModel()->changeBusinessAmount($order['business_id'], $order['entry_amount'], true);
        if (empty($res)) {
            \PhalApi\DI()->logger->error('确认失败', $user);
            \PhalApi\DI()->logger->error('确认失败', $order);
            return "确认失败";
        }

        $beforeAmount1 = $res['beforeAmount'];
        $changAmount1 = $order['order_amount'];
        $afterAmount1 = $res['beforeAmount'] + $order['order_amount'];
        $beforeAmount2 = $afterAmount1;
        $changAmount2 = $order['cost_free'];
        $afterAmount2 = $afterAmount1 - $order['cost_free'];

        //商户金额log
        $businessLogData = array(
            'business_id' => $user['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount1,
            'change_amount' => $changAmount1,
            'result_amount' => $afterAmount1,
            'type' => 1,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '收款入账',
        );
        $this->_getBusinessAmountRecordModel()->addBusinessLog($businessLogData);
        //商户金额log
        $businessLogData = array(
            'business_id' => $user['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount2,
            'change_amount' => $changAmount2,
            'result_amount' => $afterAmount2,
            'type' => 2,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '手续费支出',
        );
        $this->_getBusinessAmountRecordModel()->addBusinessLog($businessLogData);

        //todo 订单

        //更新订单
        $file = array('id' => $order['id'], 'status' => 2, 'user_id' => $user['id']);
        $data = array('status' => 3, 'conf_img' => $url);
        $this->_getCollectOrderModel()->configCollectOrderList($file, $data);


        //todo 用户金额
        //佣金

        $userChangAmount = $order['order_amount'] * $user['collect_free'] / 10000;
        $res = $this->_getUserModel()->changeUserAmount($user['id'], $userChangAmount, true);

        if (empty($res)) {
            \PhalApi\DI()->logger->error('返佣失败', $user);
            \PhalApi\DI()->logger->error('返佣失败', $order);
            return "返佣失败";
        }

        //用户金额log
        $logData = array(
            'user_id' => $user['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $res['beforeAmount'],
            'change_amount' => $res['changAmount'],
            'result_amount' => $res['afterAmount'],
            'type' => 2,
            'business_id' => $order['business_id'],
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '佣金',
        );
        $this->_getUserAmountRecordModel()->addUserLog($logData);


        //todo 推送消息
        $r_order = $this->_getCollectOrderModel()->getCollectOrder($id);
        $business = $this->_getBusinessModel()->getBusiness($r_order['business_id']);

        if (empty($r_order) || empty($r_order['callback_url']) || empty($business)) {
            \PhalApi\DI()->logger->debug('回调异常 ->', $r_order);
            return null;
        }

        $b_status = 0;
        if (3 == $r_order['status'])
            $b_status = 1;
        $data = array('order_no' => $r_order['order_no'], 'business_no' => $r_order['business_no'], 'status' => $b_status, 'amount' => $r_order['order_amount']);

        $sign = $this->encryptAppKey($data, $business['private_key']);
        $data['sign'] = $sign;
        $this->_getFiltrationAPI()->pushUrl($r_order['callback_url'], $data);

        return null;
    }


}
