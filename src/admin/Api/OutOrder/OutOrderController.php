<?php

namespace Admin\Api\OutOrder;

use Admin\Common\BaseController;

/**
 * admin数据1000
 */
class OutOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'createOutOrder' => array(
                'amount' => array('name' => 'amount', 'require' => true, 'desc' => '金额'),
                'parent_id' => array('name' => 'parent_id', 'require' => true, 'desc' => '指定团队'),
            ),
            'confirmOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true),
                'status' => array('name' => 'status', 'require' => true)
            ),
            'getsOutOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'type' => array('name' => 'type', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'confWithdrawal' => array(
                'id' => array('name' => 'id', 'require' => true),
                'status' => array('name' => 'status', 'min' => 0, 'max' => 1, 'require' => true)
            ),
            'pushOrder' => array(
                'order_id' => array('name' => 'order_id', 'require' => true)
            ),
        );
    }

    /**
     * 创建出款单
     */
    public function createOutOrder()
    {
        $amount = $this->amount;
        $group_id = $this->parent_id;
        $res = $this->_getOutOrderDomain()->createOutOrder($amount, $group_id);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    /**
     *获取出款单
     **/
    public function getsOutOrder()
    {
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $type = $this->type;
        $res = $this->_getOutOrderDomain()->getsOutOrder($status, $type, $page, $limit);
        return $this->api_success($res);
    }


    /**
     * 确认出款单
     */
    public function confirmOutOrder()
    {
        $id = $this->id;
        $status = $this->status;
        if ($status != 4 && $status != 5) {
            return $this->api_error(1000, "yes");
        }
        $res = $this->_getOutOrderDomain()->confirmOutOrder($id, $status);

        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    /**
     * 订单审核
     */
    public function confWithdrawal()
    {
        $id = $this->id;
        $status = $this->status;

        $res = $this->_getOutOrderDomain()->confWithdrawal($id, $status);

        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    public function pushOrder()
    {
        $order_id = $this->order_id;
        $this->_getOutOrderDomain()->pushOrder($order_id);
        return $this->api_success();

    }

}
