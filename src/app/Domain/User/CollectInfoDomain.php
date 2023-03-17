<?php

namespace App\Domain\User;

use App\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectInfoDomain extends BaseDomain
{


    public function getCollectInfoList($user_id)
    {
        $file = array();

        if (is_numeric($user_id)) {
            $file['user_id'] = $user_id;
        }

        return $this->_getUserCollectInfoModel()->getCollectInfoList($file);
    }


    public function addImageCollectInfo($user, $type, $name, $amount, $remark)
    {

        $pay_info = '{"pay_bank": "", "pay_name": ' . $name . ', "pay_account": "", "pay_bank_local": ""}';
        \PhalApi\DI()->logger->debug('$pay_info img', $pay_info);

        $pay_name = $name;
        $pay_account = '';

        $data = array(
            'user_id' => $user['id'],
            'user_name' => $user['user_name'],
            'user_account' => $user['account'],
            'type' => (int)$type,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'amount' => (double)$amount,
            'pay_info' => $pay_info,
            'pay_name' => $pay_name,
            'pay_account' => $pay_account,
            'remark' => $remark,
        );

        $this->_getUserCollectInfoModel()->addCollectInfo($data);
        return true;
    }

    public function addCollectInfo($user, $type, $amount, $pay_info)
    {

        $p = json_decode($pay_info, true);
        \PhalApi\DI()->logger->debug('$pay_info', json_last_error());
        \PhalApi\DI()->logger->debug('$pay_info', $pay_info);
        \PhalApi\DI()->logger->debug('$pay_info', $p);

        $pay_name = $p['pay_name'];
        $pay_account = $p['pay_account'];

        $data = array(
            'user_id' => $user['id'],
            'user_name' => $user['user_name'],
            'user_account' => $user['account'],
            'type' => (int)$type,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'amount' => (double)$amount,
            'pay_info' => $pay_info,
            'pay_name' => $pay_name,
            'pay_account' => $pay_account,
            'remark' => '',
        );

        $this->_getUserCollectInfoModel()->addCollectInfo($data);
        return true;
    }

    public function delCollectInfo($user, $id)
    {
        $file = array(
            'id' => $id,
            'user_id' => $user['id'],
            'status' => 1,
        );
        $data = array(
            'status' => 0,
        );

        $this->_getUserCollectInfoModel()->delCollectInfo($file, $data);
        return true;
    }

    public function getCollectInfo($user, $id)
    {
        $file = array(
            'id' => $id,
            'user_id' => $user['id'],
            'status' => 1,
        );

        return $this->_getUserCollectInfoModel()->getCollectInfo($file);
    }


}
