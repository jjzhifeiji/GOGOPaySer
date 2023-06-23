<?php

namespace Admin\Api\CollectOrder;

use Admin\Common\BaseController;

/**
 * 收款数据3000
 */
class CollectOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'testOutOrder' => array(
                'amount' => array('name' => 'amount', 'desc' => ''),
            ),
            'testCollectOrder' => array(
                'amount' => array('name' => 'amount', 'desc' => ''),
            ),
            'getCollectOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '账号'),
            ),
            'getsCollectOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量'),
                'order_no' => array('name' => 'order_no', 'desc' => '订单编号'),
                'business_no' => array('name' => 'business_no', 'desc' => '商户编号'),
                'amount' => array('name' => 'amount', 'desc' => '订单金额'),
                'order_fee' => array('name' => 'order_fee', 'desc' => '手续费'),
                'user_name' => array('name' => 'user_name', 'desc' => '用户'),
                'business_name' => array('name' => 'business_name', 'desc' => '商户'),
                'pay_type' => array('name' => 'pay_type', 'desc' => '支付类型'),
            ),
            'pushOrder' => array(
                'order_id' => array('name' => 'order_id', 'require' => true, 'desc' => '')
            ),
            'repairCollectOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '')
            ),
        );
    }

    public function getCollectOrder()
    {
        $id = $this->id;
        $res = $this->_getCollectOrderDomain()->getCollectOrder($id);
        return $this->api_success($res);
    }

    public function getsCollectOrder()
    {
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $order_no = $this->order_no;
        $business_no = $this->business_no;
        $amount = $this->amount;
        $order_fee = $this->order_fee;
        $user_name = $this->user_name;
        $business_name = $this->business_name;
        $pay_type = $this->pay_type;


        $res = $this->_getCollectOrderDomain()->getCollectOrderList($status, $page, $limit, $order_no, $business_no, $amount, $order_fee, $user_name, $business_name, $pay_type);
        return $this->api_success($res);
    }


    /**
     *  补单
     * @desc 确认已超时的代收订单
     */
    public function repairCollectOrder()
    {
        $id = $this->id;

        $isLock = $this->getCache('config' . $id);
        if ($isLock == true) {
            \PhalApi\DI()->logger->error('config' . $id . '<-确认->' . $isLock);
            return $this->api_error(2003, "too late");
        }
        $this->setCache('config' . $id, true, 60);

        $res = $this->_getCollectOrderDomain()->repairCollectOrder($id);
        $this->delCache('config' . $id);

        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(2004, $res);
        }
    }


    public function testCollectOrder()
    {
        $amount = $this->amount;
        $curl = new \PhalApi\CUrl();
        $url = 'http://api.tmpay777.com/Task/Api_ApiController.createCollectionOrder?amount=' . $amount . '&sign=2&callback_url=2&platform_id=GP12339876fo';
        $rs = $curl->get($url, 3000);

        $res = json_decode($rs, true);
        return $this->api_success();
    }


    public function testOutOrder()
    {
        $amount = $this->amount;
        $curl = new \PhalApi\CUrl();
        $url = 'http://api.tmpay777.com/Task/Api_ApiController.createPayOrder?' . $amount
            . '&sign=2&pay_type=1&platform_id=GP12339876fo&type=1&currency_code=CNY&card_no=79122387643388213131&name=xaxx&organ=xaxx&address=7912&callback_url=7912';

        $rs = $curl->get($url, 3000);

        $res = json_decode($rs, true);
        return $this->api_success();
    }

    public function pushOrder()
    {

        $order_id = $this->order_id;
        $this->_getCollectOrderDomain()->pushOrder($order_id);
        return $this->api_success();

    }


}
