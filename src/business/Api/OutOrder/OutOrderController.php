<?php

namespace Business\Api\OutOrder;

use Business\Common\BaseController;

/**
 * 商户代付
 */
class OutOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'createOutOrder' => array(
                'amount' => array('name' => 'amount', 'require' => true, 'desc' => '金额')
            ),
            'getsOutOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'type' => array('name' => 'type', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量'),
                'start_time' => array('name' => 'start_time', 'desc' => ''),
                'end_time' => array('name' => 'end_time', 'desc' => '')
            ),
        );
    }

    /**
     * 商户提现
     * @desc 商户提现
     */
    public function createOutOrder()
    {
        $user = $this->member_arr;
        $amount = $this->amount;
        $res = $this->_getOutOrderDomain()->createOutOrder($user, $amount);
        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    /**
     *获取出款单
     * @desc 获取出款单
     */
    public function getsOutOrder()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $type = $this->type;
        $start_time = $this->start_time;
        $end_time = $this->end_time;
        $res = $this->_getOutOrderDomain()->getsOutOrder($user, $status, $type, $start_time, $end_time, $page, $limit);
        return $this->api_success($res);
    }

}
