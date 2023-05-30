<?php

namespace Admin\Api\CollectOrder;

use Admin\Common\BaseController;

/**
 *
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
            'getsCollectOrder' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
        );
    }


    public function getsCollectOrder()
    {
        $page = $this->page;
        $limit = $this->limit;
        $status = $this->status;
        $res = $this->_getCollectOrderDomain()->getCollectOrderList($status, $page, $limit);
        return $this->api_success($res);
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


}
