<?php

namespace Business\Domain\Order;

use Business\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class OutOrderDomain extends BaseDomain
{

    /**
     * type 1充值 2代付 3商户提现 4用户提现
     * status  1 "待接单"  2  "待付款"  3  "待确认"  4 "已确认"  5  "已超时"
     */
    public function createOutOrder($platform, $amount)
    {

        $file = array('id' => $platform['id']);
        $u = $this->_getBusinessModel()->getBusiness($file);
        if (empty($u)) {
            return "信息有误";
        }
        if ($amount > $u['business_amount']) {
            return "余额不足";
        }
        $data = array(
            'order_no' => 'tx' . date('YmdHis') . rand(1000, 9999),
            'business_id' => $platform['id'],
            'business_name' => $platform['name'],
            'free_amount' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'pay_type' => 1,
            'type' => 3,
            'status' => 6,
            'order_amount' => $amount,
//            'group_id' => $u['id'],
//            'group_name' => $u['user_name'],
//            'group_account' => $u['account'],

            'pay_name' => '商户提现',
            'pay_account' => '商户提现',
            'pay_info' => '',
            'pay_remark' => '备注',

        );

        //todo 商户金额
        $res = $this->_getBusinessModel()->changeBusinessAmount($platform['id'], $amount, false);
        if (empty($res)) {
            \PhalApi\DI()->logger->error('提现下单失败', $platform);
            \PhalApi\DI()->logger->error('提现下单失败', $data);
            return "提现失败";
        }

        $beforeAmount1 = $res['beforeAmount'];
        $changAmount1 = $amount;
        $afterAmount1 = $res['beforeAmount'] - $amount;

        $this->_getOutOrderModel()->createOutOrder($data);

        $order = $this->_getOutOrderModel()->getOutOrder($data['order_no']);

        if (empty($order)) {
            \PhalApi\DI()->logger->error('提现创建下单失败', $platform);
            \PhalApi\DI()->logger->error('提现创建下单失败', $data);
            return "订单创建失败";
        }

        //商户金额log
        $businessLogData = array(
            'business_id' => $platform['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount1,
            'change_amount' => $changAmount1,
            'result_amount' => $afterAmount1,
            'type' => 5,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '商户提现',
        );
        $this->_getBusinessAmountRecordModel()->addBusinessLog($businessLogData);

        \PhalApi\DI()->logger->info($platform['name'] . "createOutOrder:" . $data);

        return null;
    }


    public function getsOutOrder($user, $status, $type, $start_time, $end_time, $page, $limit)
    {
        $file = array(
            'business_id' => $user['id']
        );

        if (is_numeric($type) && ($type == 2 || $type == 3)) {
            $file['type'] = $type;
        }

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }


        return $this->_getOutOrderModel()->getsOutOrder($file,  $start_time, $end_time, $page, $limit);
    }

}
