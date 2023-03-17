<?php

namespace Business\Api\OutOrder;

use Business\Common\BaseController;

/**
 * admin数据1000
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
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
        );
    }

    /**
     * 商户提现
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
     **/
    public function getsOutOrder()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $type = $this->type;
        $res = $this->_getOutOrderDomain()->getsOutOrder($user, $status, $type, $page, $limit);
        return $this->api_success($res);
    }

}
