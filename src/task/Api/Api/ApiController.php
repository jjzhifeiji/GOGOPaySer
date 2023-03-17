<?php

namespace Task\Api\Api;

use Task\Common\BaseController;
use function PhalApi\DI;

/**
 *
 */
class ApiController extends BaseController
{

    public function getRules()
    {
        return array(
            'createCollectionOrder' => array(
                'pay_type' => array('name' => 'pay_type', 'require' => true, 'desc' => '支付类型 1、银行卡 2、微信 3、支付宝 4、USDT', 'type' => 'int', 'min' => 1, 'max' => 4, 'default' => 1),
                'amount' => array('name' => 'amount', 'require' => true, 'desc' => '金额'),
                'currency_code' => array('name' => 'currency_code', 'require' => true, 'desc' => '币种简码'),
                'type' => array('name' => 'type', 'require' => true, 'desc' => 'type', 'default' => 1),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'trade_no' => array('name' => 'business_no', 'require' => false, 'desc' => '三方订单号'),
                'callback_url' => array('name' => 'notice_url', 'require' => false, 'desc' => '回调地址'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),
            'createPayOrder' => array(
                'pay_type' => array('name' => 'pay_type', 'require' => true, 'desc' => '支付类型 1、银行卡 2、微信 3、支付宝 4、USDT', 'type' => 'int', 'min' => 1, 'max' => 4, 'default' => 1),
                'business_no' => array('name' => 'business_no', 'desc' => '商户订单'),
                'amount' => array('name' => 'amount', 'require' => true, 'desc' => '金额'),
                'number' => array('name' => 'number', 'require' => true, 'desc' => '号码'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '名字'),
                'organ' => array('name' => 'organ', 'require' => true, 'desc' => '组织'),
                'address' => array('name' => 'address', 'require' => true, 'desc' => '地址'),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'callback_url' => array('name' => 'callback_url', 'require' => false, 'desc' => '商户ID'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),

        );
    }


    public function createCollectionOrder()
    {

        $pay_type = $this->pay_type;
        $amount = $this->amount;
        $platform_id = $this->platform_id;
        $business_no = $this->trade_no;
        $callback_url = $this->callback_url;
        $sign = $this->sign;
        //TODO 验证签名


        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(10001, '商户ID有误');
        }
        if ($amount < 500 || $amount > 100000) {
            return $this->api_error(10002, '金额有误');
        }

        $res = $this->_getCollectOrderDomain()->createOrder($pay_type, $amount, $platform, $business_no, $callback_url);

        $res = array(
            'order_no' => $res,
            'show_page' => 'http://120.48.10.211:9002/show_code.html?' . $res
//            'show_page' => 'http://show.oubao1688.com/show_code.html?' . $res
        );
        return $this->api_success($res);
    }


    public function createPayOrder()
    {
        $pay_type = $this->pay_type;
        $business_no = $this->business_no;
        $amount = $this->amount;
        $number = $this->number;
        $name = $this->name;
        $organ = $this->organ;
        $address = $this->address;
        $platform_id = $this->platform_id;
        $callback_url = $this->callback_url;
        $sign = $this->sign;
        //TODO 验证签名

        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(20001, '商户ID有误');
        }

        $cost_free = $amount * $platform['out_free'] / 10000;
        if (($amount + $cost_free) > $platform['business_amount']) {
            return $this->api_error(20002, '金额不足');
        }

        if ($amount > 10000) {
            return $this->api_error(20003, '金额有误');
        }

        if (!$this->checkPayInfo($pay_type, $number, $name, $organ, $address)) {
            return $this->api_error(20004, '收款信息有误');
        }

        $r = $this->_getOutOrderDomain()->createOutOrder($pay_type, $amount, $platform, $business_no, $callback_url, $number, $name, $organ, $address);

        if (empty($r['order_no'])) {
            return $this->api_error(20005, $r);
        }
        $res = array(
            'order_no' => $r['order_no']
        );
        return $this->api_success($res);
    }

    //检查付款账户信息
    private function checkPayInfo($pay_type, $number, $name, $organ, $address): bool
    {
        $res = false;
        switch ($pay_type) {
            case 1:
                if (strlen($number) > 16 && !empty($name) && !empty($organ)) {
                    $res = true;
                } else {
                    DI()->logger->info("收款信息有误" . sizeof($number) . "number" . $number . "name" . $name . " $organ" . $organ);
                }
                break;
            case  2:
                if (sizeof($number) > 5 && !empty($name)) {
                    $res = true;
                }
                break;
            case  3:
                if (sizeof($number) > 6 && !empty($name)) {
                    $res = true;
                }
                break;
            case  4:
                if (sizeof($number) > 20 && !empty($name) && !empty($organ)) {
                    $res = true;
                }
                break;
        }

        return $res;
    }

}