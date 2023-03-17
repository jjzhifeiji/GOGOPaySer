<?php

namespace Admin\Api\Business;

use Admin\Common\BaseController;

/**
 *
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
                'collect_free' => array('name' => 'collect_free', 'require' => true, 'desc' => ''),
                'out_free' => array('name' => 'out_free', 'require' => true, 'desc' => ''),
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
        $collect_free = $this->collect_free;
        $out_free = $this->out_free;
        $this->_getBusinessDomain()->addBusiness($name,$account, $collect_free, $out_free);
        return $this->api_success();
    }


}
