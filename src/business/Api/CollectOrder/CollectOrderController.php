<?php

namespace Business\Api\CollectOrder;

use Business\Common\BaseController;

/**
 *
 */
class CollectOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'getsCollectOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
        );
    }


    public function getsCollectOrder()
    {
        $user = $this->member_arr;
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $res = $this->_getCollectOrderDomain()->getCollectOrderList($user, $status, $page, $limit);
        return $this->api_success($res);
    }


}
