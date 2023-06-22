<?php

namespace Task\Api\Api;

use http\Params;
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
//                'type' => array('name' => 'type', 'require' => true, 'desc' => 'type1 代收 2充值 。。。', 'default' => 1),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'business_no' => array('name' => 'business_no', 'require' => false, 'desc' => '三方订单号'),
                'callback_url' => array('name' => 'callback_url', 'require' => true, 'desc' => '回调地址'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),
            'getCollectionOrder' => array(
                'order_no' => array('name' => 'order_no', 'require' => false, 'desc' => '订单号'),
                'business_no' => array('name' => 'business_no', 'require' => false, 'desc' => '三方订单号'),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),
            'createPayOrder' => array(
                'pay_type' => array('name' => 'pay_type', 'require' => true, 'desc' => '支付类型 1、银行卡 2、微信 3、支付宝 4、USDT', 'type' => 'int', 'min' => 1, 'max' => 4, 'default' => 1),
                'business_no' => array('name' => 'business_no', 'desc' => '商户订单'),
                'currency_code' => array('name' => 'currency_code', 'require' => true, 'desc' => '币种简码'),
                'amount' => array('name' => 'amount', 'require' => true, 'desc' => '金额'),
                'card_no' => array('name' => 'card_no', 'require' => true, 'desc' => '号码'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '名字'),
                'type' => array('name' => 'type', 'require' => true, 'default' => 1, 'desc' => '付款类型 1、网银 2、快转数 3、TRC20'),
                'organ' => array('name' => 'organ', 'require' => true, 'desc' => '组织'),
                'address' => array('name' => 'address', 'require' => true, 'desc' => '地址'),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'callback_url' => array('name' => 'callback_url', 'require' => false, 'desc' => '商户ID'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),
            'getPayOrder' => array(
                'order_no' => array('name' => 'order_no', 'require' => false, 'desc' => '订单号'),
                'business_no' => array('name' => 'business_no', 'require' => false, 'desc' => '商户订单'),
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),
            'getAmount' => array(
                'platform_id' => array('name' => 'platform_id', 'require' => true, 'desc' => '商户ID'),
                'sign' => array('name' => 'sign', 'require' => true, 'desc' => '签名'),
            ),

        );
    }


    public function createCollectionOrder()
    {

        $pay_type = $this->pay_type;
        $amount = $this->amount;
        $platform_id = $this->platform_id;
        $business_no = $this->business_no;
        $currency_code = $this->currency_code;
        $callback_url = $this->callback_url;
        $sign = $this->sign;

        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(10001, '商户ID有误');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($platform['remote_ip'], $ip) !== false) {
            DI()->logger->info("异常 createCollectionOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "异常 createCollectionOrder:" . $platform['name']);
            DI()->logger->info("异常 createCollectionOrder:" . $_SERVER);
//            return $this->api_error(10001, '来源异常');
        } else {
            DI()->logger->info("正常 createCollectionOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "正常 createCollectionOrder:" . $platform['name']);
            DI()->logger->info("正常 createCollectionOrder:" . $_SERVER);
        }

        if ($amount < 100 || $amount > 5000) {
            return $this->api_error(10002, '金额有误');
        }

        if ($currency_code != 'CNY') {
            return $this->api_error(10003, '币种有误');
        }

        //TODO 验证签名
        $filter = new \PhalApi\Filter\SimpleMD5Filter();
        try {
            $filter->check();
        } catch (\PhalApi\Exception $e) {
            DI()->logger->error("签名有误" . $sign);
            return $this->api_error(10004, '签名有误');
        }

        $res = $this->_getCollectOrderDomain()->createOrder($pay_type, $amount, $platform, $business_no, $callback_url);

        $res = array(
            'order_no' => $res,
//            'show_page' => 'http://120.48.10.211:9002/show_code.html?' . $res
//            http://show.tmpay777.com/#/render?i202306181246439181
            'show_page' => HTTP_SHOW . '/#/render?' . $res
        );
        return $this->api_success($res);
    }


    public function getCollectionOrder()
    {
        $platform_id = $this->platform_id;
        $order_no = $this->order_no;
        $business_no = $this->business_no;
        $sign = $this->sign;

        if (empty($order_no) && empty($business_no))
            return $this->api_error(10002, '订单号有误');

        //TODO 验证签名
        $filter = new \PhalApi\Filter\SimpleMD5Filter();
        try {
            $filter->check();
        } catch (\PhalApi\Exception $e) {
            DI()->logger->error("签名有误" . $sign);
//            return $this->api_error(10004, '签名有误');
        }

        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(10001, '商户ID有误');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($platform['remote_ip'], $ip) !== false) {
            DI()->logger->info("异常 getCollectionOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "异常 getCollectionOrder:" . $platform['name']);
            DI()->logger->info("异常 getCollectionOrder:" . $_SERVER);
//            return $this->api_error(10001, '来源异常');
        } else {
            DI()->logger->info("正常 getCollectionOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "正常 getCollectionOrder:" . $platform['name']);
            DI()->logger->info("正常 getCollectionOrder:" . $_SERVER);
        }


        $order = $this->_getCollectOrderDomain()->getPlatformOrder($platform['id'], $order_no, $business_no);

        return $this->api_success($order);

    }

    public function createPayOrder()
    {
        $pay_type = $this->pay_type;
        $business_no = $this->business_no;
        $currency_code = $this->currency_code;
        $amount = $this->amount;
        $card_no = $this->card_no;
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
        $ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($platform['remote_ip'], $ip) !== false) {
            DI()->logger->info("异常 createPayOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "异常 createPayOrder:" . $platform['name']);
            DI()->logger->info("异常 createOrder:" . $_SERVER);
//            return $this->api_error(10001, '来源异常');
        } else {
            DI()->logger->info("正常 createPayOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "正常 createPayOrder:" . $platform['name']);
            DI()->logger->info("正常 createPayOrder:" . $_SERVER);
        }

        //TODO 验证签名
        $filter = new \PhalApi\Filter\SimpleMD5Filter();
        try {
            $filter->check();
        } catch (\PhalApi\Exception $e) {
            DI()->logger->error("签名有误" . $sign);
            return $this->api_error(20002, '签名有误');
        }

        if ($pay_type == 1) {
            $out_free = $platform['bank_out_free'];
        } else if ($pay_type == 2) {
            $out_free = $platform['wx_out_free'];
        } else if ($pay_type == 3) {
            $out_free = $platform['ali_out_free'];
        } else {
            $out_free = 100;
        }
        $cost_free = $amount * $out_free / 10000;
        if (($amount + $cost_free) > $platform['business_amount']) {
            return $this->api_error(20003, '金额不足');
        }

        if ($amount > 10000) {
            return $this->api_error(20004, '金额有误');
        }

        if (!$this->checkPayInfo($pay_type, $card_no, $name, $organ, $address)) {
            return $this->api_error(20005, '收款信息有误');
        }

        $r = $this->_getOutOrderDomain()->createOutOrder($pay_type, $amount, $platform, $business_no, $callback_url, $card_no, $name, $organ, $address);

        if (empty($r['order_no'])) {
            return $this->api_error(20006, $r);
        }
        $res = array(
            'order_no' => $r['order_no']
        );
        return $this->api_success($res);
    }

    public function getPayOrder()
    {
        $order_no = $this->order_no;
        $business_no = $this->business_no;
        $platform_id = $this->platform_id;
        $sign = $this->sign;

        if (empty($order_no) && empty($business_no))
            return $this->api_error(10002, '订单号有误');

        //TODO 验证签名
        $filter = new \PhalApi\Filter\SimpleMD5Filter();
        try {
            $filter->check();
        } catch (\PhalApi\Exception $e) {
            DI()->logger->error("签名有误" . $sign);
//            return $this->api_error(10004, '签名有误');
        }

        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(20001, '商户ID有误');
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($platform['remote_ip'], $ip) !== false) {
            DI()->logger->info("异常 getPayOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "异常 getPayOrder:" . $platform['name']);
            DI()->logger->info("异常 getPayOrder:" . $_SERVER);
//            return $this->api_error(10001, '来源异常');
        } else {
            DI()->logger->info("正常 getPayOrder:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "正常 getPayOrder:" . $platform['name']);
            DI()->logger->info("正常 getPayOrder:" . $_SERVER);
        }

        $order = $this->_getOutOrderDomain()->getPlatformOrder($platform['id'], $order_no, $business_no);
        return $this->api_success($order);

    }

    public function getAmount()
    {
        $platform_id = $this->platform_id;
        $sign = $this->sign;

        //TODO 验证签名
        $filter = new \PhalApi\Filter\SimpleMD5Filter();
        try {
            $filter->check();
        } catch (\PhalApi\Exception $e) {
            DI()->logger->error("签名有误" . $sign);
//            return $this->api_error(10004, '签名有误');
        }

        $platform = $this->_getBusinessDomain()->getBusiness($platform_id);
        if (empty($platform)) {
            return $this->api_error(20001, '商户ID有误');
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        if (strpos($platform['remote_ip'], $ip) !== false) {
            DI()->logger->info("异常 getAmount:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "异常 getAmount:" . $platform['name']);
            DI()->logger->info("异常 getAmount:" . $_SERVER);
//            return $this->api_error(10001, '来源异常');
        } else {
            DI()->logger->info("正常 getAmount:" . $ip);
            DI()->logger->info($platform['remote_ip'] . "正常 getAmount:" . $platform['name']);
            DI()->logger->info("正常 getAmount:" . $_SERVER);
        }


        return $this->api_success(array('name' => $platform['name'], 'amount' => $platform['business_amount']));


    }

    //检查付款账户信息
    private function checkPayInfo($pay_type, $number, $name, $organ, $address): bool
    {
        $res = false;
        switch ($pay_type) {
            case 1:
                if (strlen($number) > 12 && !empty($name) && !empty($organ)) {
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
