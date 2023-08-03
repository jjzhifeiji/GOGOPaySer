<?php

namespace Task\Api\Comment;

use Task\Common\BaseController;

/**
 */
class CommonController extends BaseController
{
    public function getRules()
    {
        return array(
            'getOrder' => array(
                'orderNo' => array('name' => 'orderNo', 'require' => true)
            ),
        );
    }


    /**
     * 订单超时任务
     */
    public function checkOrder()
    {
        $this->_getCollectOrderDomain()->checkOrder();
        $this->_getCollectOrderDomain()->closeCode();
        return $this->api_success();
    }


    /**
     */
    public function getOrder()
    {
        $orderNo = $this->orderNo;
        $res = $this->_getCollectOrderDomain()->getOrder($orderNo);
        return $this->api_success($res);
    }


}
