<?php

namespace App\Api\Order;

use App\Common\BaseController;

/**
 * 8000
 */
class OutOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'getsOutingOrder' => array(
                'type' => array('name' => 'type', 'require' => true, 'type' => 'int', 'desc' => '')
            ),
            'getsOutOrder' => array(
                'type' => array('name' => 'type', 'desc' => ''),
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'getOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '')
            ),
            'takeOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '')
            ),
            'readyOutOrder' => array(
                'id' => array('name' => 'id', 'require' => true, 'type' => 'int', 'desc' => '')
            ),
            'withdrawal' => array(
                'amount' => array('name' => 'amount', 'require' => true, 'type' => 'int', 'desc' => '')
            ),
            'getWithdrawal' => array(
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
        );
    }

    /**
     *
     */
    public function getsOutingOrder()
    {
        $user = $this->member_arr;
        $type = $this->type;
        $res = $this->_getOutOrderDomain()->getsOutingOrder($user['id'], $type);
        return $this->api_success($res);
    }

    /**
     *
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
     */
    public function takeOutOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getOutOrderDomain()->takeOutOrder($user['id'], $id);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(8001, $res);
        }
    }


    /**
     * 确认出款
     */
    public function readyOutOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getOutOrderDomain()->readyOutOrder($user['id'], $id);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(8001, $res);
        }
    }


    /**
     * 确认出款
     */
    public function withdrawal()
    {
        $user = $this->member_arr;
        $amount = $this->amount;

        $res = $this->_getOutOrderDomain()->withdrawal($user['id'], $amount);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(8001, $res);
        }
    }

    public function getWithdrawal()
    {
        $user = $this->member_arr;

        $page = $this->page;
        $limit = $this->limit;

        $res = $this->_getOutOrderDomain()->getWithdrawal($user['id'], $page, $limit);
        return $this->api_success($res);

    }


}
