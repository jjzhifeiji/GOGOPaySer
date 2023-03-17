<?php

namespace App\Api\Order;

use App\Common\BaseController;

/**
 * 代付订单 3000
 */
class OutOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'getsOutingOrder' => array(
                'type' => array('name' => 'type', 'require' => true, 'type' => 'int', 'desc' => '订单类型')
            ),
            'getsOutOrder' => array(
                'type' => array('name' => 'type', 'desc' => '订单类型'),
                'status' => array('name' => 'status', 'desc' => '订单状态'),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'getOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '订单ID')
            ),
            'takeOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '订单ID')
            ),
            'readyOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '订单ID')
            ),
            'withdrawal' => array(
                'amount' => array('name' => 'amount', 'require' => true, 'type' => 'int', 'desc' => '提现金额')
            ),
            'getWithdrawal' => array(
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
        );
    }

    /**
     * 获取代付订单
     * @desc 获取 待接的代付订单列表
     */
    public function getsOutingOrder()
    {
        $user = $this->member_arr;
        $type = $this->type;
        $res = $this->_getOutOrderDomain()->getsOutingOrder($user['id'], $type);
        return $this->api_success($res);
    }

    /**
     * 获取代付订单
     * @desc 获取代付订单列表
     */
    public function getsOutOrder()
    {
        $user = $this->member_arr;
        $status = $this->status;
        $type = $this->type;
        $page = $this->page;
        $limit = $this->limit;

        $res = $this->_getOutOrderDomain()->getsOutOrder($user['id'], $status, $type, $page, $limit);

        return $this->api_success($res);
    }

    /**
     * 获取代付订单
     * @desc 获取代付订单详情
     */
    public function getOutOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getOutOrderDomain()->getOutOrder($user['id'], $id);

        return $this->api_success($res);
    }

    /**
     * 接单
     * @desc 接单 代付订单
     */
    public function takeOutOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getOutOrderDomain()->takeOutOrder($user['id'], $id);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(3001, $res);
        }
    }


    /**
     * 确认代付订单
     * @desc 代付订单 付款后确认
     */
    public function readyOutOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getOutOrderDomain()->readyOutOrder($user['id'], $id);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(3002, $res);
        }
    }


    /**
     * 提现
     * @desc 用户提现
     */
    public function withdrawal()
    {
        $user = $this->member_arr;
        $amount = $this->amount;

        $res = $this->_getOutOrderDomain()->withdrawal($user['id'], $amount);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(3003, $res);
        }
    }

    /**
     * 提现列表
     * @desc 获取提现列表
     */
    public function getWithdrawal()
    {
        $user = $this->member_arr;

        $page = $this->page;
        $limit = $this->limit;

        $res = $this->_getOutOrderDomain()->getWithdrawal($user['id'], $page, $limit);
        return $this->api_success($res);

    }


}
