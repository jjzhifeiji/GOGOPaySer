<?php

namespace Task\Domain\Order;

use Task\Common\BaseDomain;
use PhalApi\Tool;
use function PhalApi\DI;

class OutOrderDomain extends BaseDomain
{


    public function createOutOrder($pay_type, $amount, $platform, $business_no, $callback_url, $number, $name, $organ, $address)
    {

        $cost_free = $amount * $platform['out_free'] / 10000;

        $myObj = '';
        $myObj->pay_account = $number;
        $myObj->pay_name = $name;
        $myObj->pay_bank = $organ;
        $myObj->pay_bank_local = $address;

        $pay_info = json_encode($myObj);

        $data = array(
            'order_no' => 'ob' . date('YmdHis') . rand(1000, 9999),
            'business_id' => $platform['id'],
            'business_name' => $platform['name'],
            'business_no' => $business_no,
            'free_amount' => $cost_free,
            'create_time' => date('Y-m-d H:i:s'),
            'pay_type' => $pay_type,
            'type' => 2,
            'status' => 1,
            'order_amount' => $amount,
            'callback_url' => $callback_url,

            'pay_name' => $name,
            'pay_account' => $number,
            'pay_info' => $pay_info,
            'pay_remark' => '',

        );
        $this->_getOutOrderModel()->createOutOrder($data);

        $order = $this->_getOutOrderModel()->getOutOrder($data['order_no']);

        //todo 商户金额
        $res = $this->_getBusinessModel()->changeBusinessAmount($platform['id'], $amount + $cost_free, false);
        if (empty($res)) {
            \PhalApi\DI()->logger->error('出款下单失败', $platform);
            \PhalApi\DI()->logger->error('出款下单失败', $data);
            return "出款失败";
        }
        $beforeAmount1 = $res['beforeAmount'];
        $changAmount1 = $amount;
        $afterAmount1 = $res['beforeAmount'] - $amount;
        $beforeAmount2 = $afterAmount1;
        $changAmount2 = $cost_free;
        $afterAmount2 = $afterAmount1 - $cost_free;

        //商户金额log
        $businessLogData = array(
            'business_id' => $platform['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount1,
            'change_amount' => $changAmount1,
            'result_amount' => $afterAmount1,
            'type' => 3,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '代付出账',
        );
        $this->_getBusinessAmountRecordModel()->addBusinessLog($businessLogData);
        //商户金额log
        $businessLogData = array(
            'business_id' => $platform['id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $beforeAmount2,
            'change_amount' => $changAmount2,
            'result_amount' => $afterAmount2,
            'type' => 4,
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '代付手续费支出',
        );
        $this->_getBusinessAmountRecordModel()->addBusinessLog($businessLogData);


        DI()->logger->info($platform['name'] . "createOutOrder:" . $res);

        return $data;
    }

    public function getPlatformOrder($platform_id, $order_no, $business_no)
    {

        $file = array(
            'business_id' => $platform_id,
            'type' => 2
        );

        if (!empty($order_no)) {
            $file['order_no'] = $order_no;
        } else if (!empty($business_no)) {
            $file['business_no'] = $business_no;
        } else {
            return '订单有误1';
        }
        $order = $this->_getOutOrderModel()->getPlatformOrder($file);

        if (empty($order)) {
            return '订单有误2';
        }

//order_no,business_no,status,pay_type,type,order_amount
//         1 "待接单"  2  "待付款"  3  "待确认"  4 "已确认"  5  "已超时" 6  "待审核" 7  "审核拒绝"

        $status = 'FAILED';
        switch ($order['status']) {
            case 4:
                $status = 'SUCCESS';
                break;
            case 5:
            case 7:
                $status = 'FAILED';
                break;
            default:
                $status = 'WAITING';
                break;
        }

        $res = array(
            'order_no' => $order['order_no'],
            'order_amount' => $order['order_amount'],
            'create_time' => $order['create_time'],
            'business_no' => $order['business_no'],
            'status' => $status,
            'currency_code' => 'CNY',
            'pay_type' => $order['pay_type'],
        );

        return $res;

    }

    private function autoAccept($res)
    {
    }


}
