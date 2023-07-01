<?php

namespace Business\Api\CollectOrder;

use Business\Common\BaseController;

/**
 * 商户代收
 */
class CollectOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'getsCollectOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量'),
                'start_time' => array('name' => 'start_time', 'desc' => ''),
                'end_time' => array('name' => 'end_time', 'desc' => '')
            ),
        );
    }


    /***
     * 代收列表
     * @desc 代收列表
     */
    public function getsCollectOrder()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $start_time = $this->start_time;
        $end_time = $this->end_time;
        $res = $this->_getCollectOrderDomain()->getCollectOrderList($user, $status, $start_time, $end_time, $page, $limit);
        return $this->api_success($res);
    }


}
