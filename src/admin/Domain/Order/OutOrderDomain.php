<?php

namespace Admin\Domain\Order;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class OutOrderDomain extends BaseDomain
{


    /**
     * type 1充值 2代付 3商户提现 4用户提现
     * status  1 "待接单"  2  "待付款"  3  "待确认"  4 "已确认"  5  "已超时"
     */
    public function createOutOrder($amount, $group_id)
    {
        $u = $this->_getUserModel()->getUserId($group_id);
        if (empty($u)) {
            return "组信息有误";
        }
        $data = array(
            'order_no' => 'o' . date('YmdHis') . rand(1000, 9999),
            'business_id' => 0,
            'free_amount' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'pay_type' => 1,
            'type' => 1,
            'status' => 1,
            'order_amount' => $amount,
            'group_id' => $u['id'],
            'group_name' => $u['user_name'],
            'group_account' => $u['account'],

            'pay_name' => '充值订单',
            'pay_account' => '充值订单',
            'pay_info' => '',
            'pay_remark' => '备注',

        );

        $this->_getOutOrderModel()->createOutOrder($data);

        return null;
    }

    public function confirmOutOrder($id, $status)
    {

        $file = array('id' => $id, 'status' => 3);
        $data = array('status' => $status);

        if ($status == 4) {
            //todo 用户金额变动

            $ouOrder = $this->_getOutOrderModel()->getconfirmOutOrder($id);
            if (empty($ouOrder)) {
                return "订单有误";
            }
            $userChangAmount = $ouOrder['order_amount'];
            $res = $this->_getUserModel()->changeUserAmount($ouOrder['user_id'], $userChangAmount, true);

            if (empty($res)) {
                \PhalApi\DI()->logger->error('充值订单失败', $ouOrder);
                return "充值订单失败";
            }

            //用户金额log
            $logData = array(
                'user_id' => $ouOrder['user_id'],
                'create_time' => date('Y-m-d H:i:s'),
                'before_amount' => $res['beforeAmount'],
                'change_amount' => $res['changAmount'],
                'result_amount' => $res['afterAmount'],
                'type' => 3,
                'business_id' => 0,
                'order_id' => $ouOrder['id'],
                'order_no' => $ouOrder['order_no'],
                'remark' => '充值',
            );
            $this->_getUserAmountRecordModel()->addUserLog($logData);
        }

        $this->_getOutOrderModel()->confirmOutOrder($file, $data);

        $this->pushOrder($id);
        return null;

    }

    public function confWithdrawal($id, $status)
    {

        $file = array('id' => $id, 'status' => 6);

        if ($status == 0) {
            //todo 用户金额变动

            $ouOrder = $this->_getOutOrderModel()->getconfirmOutOrder($id);
            if (empty($ouOrder)) {
                return "订单有误";
            }
            $userChangAmount = $ouOrder['order_amount'];

//            3商户提现 4用户提现
            if ($ouOrder['type'] == 3) {
                $res = $this->_getUserModel()->changeUserAmount($ouOrder['user_id'], $userChangAmount, false);
                if (empty($res)) {
                    \PhalApi\DI()->logger->error('退款失败', $ouOrder);
                    return "退款失败";
                }

                //用户金额log
                $logData = array(
                    'user_id' => $ouOrder['user_id'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'before_amount' => $res['beforeAmount'],
                    'change_amount' => $res['changAmount'],
                    'result_amount' => $res['afterAmount'],
                    'type' => 4,
                    'business_id' => 0,
                    'order_id' => $ouOrder['id'],
                    'order_no' => $ouOrder['order_no'],
                    'remark' => '商户提现退款',
                );
                $this->_getUserAmountRecordModel()->addUserLog($logData);
            } else if ($ouOrder['type'] == 4) {
                $res = $this->_getBusinessAmountRecordModel()->changeBusinessAmount($ouOrder['user_id'], $userChangAmount, false);
                if (empty($res)) {
                    \PhalApi\DI()->logger->error('退款失败', $ouOrder);
                    return "退款失败";
                }

                //用户金额log
                $logData = array(
                    'user_id' => $ouOrder['user_id'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'before_amount' => $res['beforeAmount'],
                    'change_amount' => $res['changAmount'],
                    'result_amount' => $res['afterAmount'],
                    'type' => 5,
                    'business_id' => 0,
                    'order_id' => $ouOrder['id'],
                    'order_no' => $ouOrder['order_no'],
                    'remark' => '用户提现退款',
                );
                $this->_getUserAmountRecordModel()->addUserLog($logData);
            } else {
            }

            $data = array('status' => 7);

        } else {
            $data = array('status' => 1);

        }

        $this->_getOutOrderModel()->confirmOutOrder($file, $data);

        return null;

    }

    public function getsOutOrder($status, $type, $page, $limit, $order_no, $business_no, $amount, $order_fee, $user_name, $business_name, $pay_type)
    {
        $file = array();

        if (is_numeric($type) && $type > 0) {
            $file['type'] = $type;
        }
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
            $file['free_amount'] = $order_fee;
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

        return $this->_getOutOrderModel()->getsOutOrder($file, $page, $limit);
    }

    public function pushOrder($order_id)
    {
        $order = $this->_getOutOrderModel()->getOutOrder($order_id);
        $business = $this->_getBusinessModel()->getsBusinessId($order['business_id']);

        if (empty($order) || empty($order['callback_url']) || empty($business)) {
            \PhalApi\DI()->logger->debug('代收回调异常 ->', $order);
            return;
        }

        //todo 推送消息
        $b_status = 0;
        if (4 == $order['status'])
            $b_status = 1;
        $data = array('order_no' => $order['order_no'], 'business_no' => $order['business_no'], 'status' => $b_status, 'amount' => $order['order_amount']);

        $sign = $this->encryptAppKey($data, $business['private_key']);
        $data['sign'] = $sign;
        $this->_getFiltrationAPI()->pushUrl($order['callback_url'], $data);

    }

}
