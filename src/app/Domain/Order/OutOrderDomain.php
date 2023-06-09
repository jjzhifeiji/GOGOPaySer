<?php

namespace App\Domain\Order;

use App\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class OutOrderDomain extends BaseDomain
{


    public function getsOutingOrder($user_id, $type)
    {
        $uu = $this->_getUserModel()->getInfo($user_id);
        if (empty($uu)) {
            return "用户有误";
        }

        $file = array(
            'status' => 1
        );

        if (is_numeric($type) && $type > 0) {
            //$type	1充值 2代付 3商户提现 4用户提现
            switch ($type) {
                case 1:
                    $file['type'] = $type;
                    $file['group_id'] = $uu['group_id'];
                    break;
                case 2:
                    $file['type'] = $type;
                    break;
                case 3:
                    $file['type'] = $type;
                    break;
                case 4:
                    $file['type'] = $type;
                    $file['group_id'] = $uu['group_id'];
                    break;
                default:
                    break;
            }
        }

        return $this->_getOutOrderModel()->getsOutingOrder($file);
    }


    public function getsOutOrder($user_id, $status, $type, $page, $limit)
    {
        $file = array(
            'user_id' => $user_id
        );

        if (is_numeric($type) && $type > 0) {
            $file['type'] = $type;
        }

        if (is_numeric($status) && $status > 0) {
            $file['status'] = $status;
        }

        return $this->_getOutOrderModel()->getsOutOrder($file, $page, $limit);
    }

    public function takeOutOrder($user_id, $id)
    {
        $uu = $this->_getUserModel()->getInfo($user_id);
        if (empty($uu)) {
            return "用户有误";
        }

        $outOrder = $this->_getOutOrderModel()->getOutOrder($id);
        if (empty($outOrder)) {
            return "订单有误";
        }
        if ($outOrder['status'] != 1) {
            return "订单状态有误";
        }

        if ($outOrder['type'] != 2 && $uu['group_id'] != $outOrder['group_id']) {
            return "订单用户有误";
        }
        $file = array(
            'id' => $id,
            'status' => 1
        );
        $data = array(
            'status' => 2,
            'user_id' => $uu['id'],
            'user_name' => $uu['user_name'],
            'user_account' => $uu['account']
        );
        $this->_getOutOrderModel()->upOutOrder($file, $data);
        return "";
    }

    public function readyOutOrder($user_id, $id)
    {
        $uu = $this->_getUserModel()->getInfo($user_id);
        if (empty($uu)) {
            return "用户有误";
        }
        $file = array(
            'id' => $id,
            'status' => 2,
            'user_id' => $uu['id']
        );
        $data = array(
            'status' => 3
        );
        $this->_getOutOrderModel()->upOutOrder($file, $data);

        return "";
    }

    public function getOutOrder($user_id, $id)
    {
        return $this->_getOutOrderModel()->getOutOrder($id);
    }

    public function withdrawal($id, $amount)
    {
        $data = array('id' => $id);
        $user = $this->_getUserModel()->getInfo($data);
        if (empty($user)) {
            return "信息有误";
        }
        $data = array(
            'order_no' => 'u' . date('YmdHis') . rand(1000, 9999),
            'business_id' => $user['id'],
            'business_name' => $user['user_name'],
            'free_amount' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'pay_type' => 1,
            'type' => 4,
            'status' => 6,
            'order_amount' => $amount,
//            'group_id' => $u['id'],
//            'group_name' => $u['user_name'],
//            'group_account' => $u['account'],

            'pay_name' => '用户提现',
            'pay_account' => '用户提现',
            'pay_info' => '',
            'pay_remark' => '备注',

        );

        $this->_getOutOrderModel()->createOutOrder($data);

        $order = $this->_getOutOrderModel()->getOutOrderSn($data['order_no']);

        //todo 商户金额
        $res = $this->_getUserModel()->changeUserAmount($id, $amount, false);
        if (empty($res)) {
            \PhalApi\DI()->logger->error('用户提现下单失败', $id);
            \PhalApi\DI()->logger->error('用户提现下单失败', $data);
            return "提现失败";
        }
        $beforeAmount1 = $res['beforeAmount'];
        $changAmount1 = $amount;
        $afterAmount1 = $res['beforeAmount'] - $amount;

        //用户金额log
        $logData = array(
            'user_id' => $user['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount1,
            'change_amount' => $changAmount1,
            'result_amount' => $afterAmount1,
            'type' => 6,
            'business_id' => 0,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '用户提现',
        );
        $this->_getUserAmountRecordModel()->addUserLog($logData);

    }

    public
    function getWithdrawal($user_id, $page, $limit)
    {
        $file = array(
            'business_id' => $user_id,
            'type' => 4,
        );

        return $this->_getOutOrderModel()->getsOutOrder($file, $page, $limit);
    }

}
