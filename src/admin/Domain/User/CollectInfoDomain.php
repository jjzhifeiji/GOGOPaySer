<?php

namespace Admin\Domain\User;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectInfoDomain extends BaseDomain
{

    public function modUserStatus($id, $status)
    {
        $user = $this->_getUserModel()->getUserId($id);
        if (empty($user) || $status == $user['status'] || !is_numeric($status)) {
            return '用户有误';
        }
        $data = array(
            'status' => $status
        );
        $this->_getUserModel()->modUserStatus($id, $data);
    }

    public function getCollectInfoList($page, $limit, $user_id, $status, $type)
    {
        $file = array();

        if (is_numeric($type)) {
            $file['type'] = $type;
        }

        if (is_numeric($status)) {
            $file['status'] = $status;
        }

        if (is_numeric($user_id)) {
            $file['user_id'] = $user_id;
        }

        return $this->_getCollectInfoModel()->getCollectInfoList($file, $page, $limit);
    }


}
