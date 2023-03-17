<?php

namespace Admin\Api\User;

use Admin\Common\BaseController;

/**
 * 用户数据9000
 */
class UserCollectController extends BaseController
{
    public function getRules()
    {
        return array(
            'getCollectInfoList' => array(
                'page' => array('name' => 'page', 'type' => 'int', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'type' => 'int', 'default' => '20', 'desc' => '数量'),
                'user_id' => array('name' => 'user_id', 'desc' => ''),
                'status' => array('name' => 'status', 'desc' => ''),
                'type' => array('name' => 'type', 'desc' => ''),
            ),

        );
    }

    /**
     * 用户收款信息
     */
    public function getCollectInfoList()
    {
        $page = $this->page;
        $limit = $this->limit;
        $user_id = $this->user_id;
        $status = $this->status;
        $type = $this->type;

        $res = $this->_getCollectInfoDomain()->getCollectInfoList($page, $limit, $user_id, $status, $type);
        return $this->api_success($res);
    }


}
