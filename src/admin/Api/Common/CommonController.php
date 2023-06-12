<?php

namespace Admin\Api\Common;

use Admin\Common\BaseController;

/**
 * 公共数据4000
 */
class CommonController extends BaseController
{

    public function getRules()
    {
        return array(

        );
    }


    /**
     * 首页统计数据
     */
    public function getHomeData()
    {
        $admin = $this->member_arr;
        $res = $this->_getCommonDomain()->getHomeData();
        return $this->api_success($res);
    }


}
