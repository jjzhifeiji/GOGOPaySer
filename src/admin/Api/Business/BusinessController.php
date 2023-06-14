<?php

namespace Admin\Api\Business;

use Admin\Common\BaseController;

/**
 * 商户数据2000
 */
class BusinessController extends BaseController
{

    public function getRules()
    {
        return array(
            'getsBusiness' => array(
                'page' => array('name' => 'page', 'require' => false, 'default' => '1', 'desc' => ''),
                'limit' => array('name' => 'limit', 'require' => false, 'default' => '20', 'desc' => ''),
            ),
            'addBusiness' => array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => ''),
                'account' => array('name' => 'account', 'require' => true, 'desc' => ''),
                'collect_wx_free' => array('name' => 'collect_wx_free', 'require' => true, 'desc' => ''),
                'collect_ali_free' => array('name' => 'collect_ali_free', 'require' => true, 'desc' => ''),
                'collect_bank_free' => array('name' => 'collect_bank_free', 'require' => true, 'desc' => ''),
            ),

        );
    }


    public function getsBusiness()
    {
        $page = $this->page;
        $limit = $this->limit;
        $res = $this->_getBusinessDomain()->getsBusiness($page, $limit);
        return $this->api_success($res);
    }


    public function addBusiness()
    {
        $name = $this->name;
        $account = $this->account;
        $collect_wx_free = $this->collect_wx_free;
        $collect_ali_free = $this->collect_ali_free;
        $collect_bank_free = $this->collect_bank_free;
        $this->_getBusinessDomain()->addBusiness($name, $account, $collect_bank_free, $collect_ali_free, $collect_wx_free);
        return $this->api_success();
    }


}
