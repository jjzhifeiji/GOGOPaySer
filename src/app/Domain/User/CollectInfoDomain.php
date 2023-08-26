<?php

namespace App\Domain\User;

use App\Common\BaseDomain;
use PhalApi\Tool;

/**
 */
class CollectInfoDomain extends BaseDomain
{


    public function getCollectInfoList($user_id, $type, $page, $limit)
    {
        $file = array();

        if (is_numeric($user_id)) {
            $file['user_id'] = $user_id;
        }
        if (is_numeric($type) && $type > 0) {
            $file['type'] = $type;
        }

        return $this->_getUserCollectInfoModel()->getCollectInfoList($file, $page, $limit);
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
            'update_time' => date('Y-m-d H:i:s'),
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
            'update_time' => date('Y-m-d H:i:s'),
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

    public function getMyInvitation($user, $id)
    {

        $file = array(
            'id' => $id,
            'user_id' => $user['id'],
            'status' => 1
        );

        $res = $this->_getInvitationModel()->getMyInvitation($file);
        return empty($res) ? array() : $res;

    }

    public function getMyInvitationList($user)
    {
        $file = array(
            'user_id' => $user['id'],
            'status' => 1
        );
        return $this->_getInvitationModel()->getMyInvitationList($file);
    }

    public function setMyInvitation($user, $bank_min_val, $bank_max_val, $wx_min_val, $wx_max_val, $ali_min_val, $ali_max_val, $bank_out_max_val, $bank_out_min_val, $wx_out_max_val, $wx_out_min_val, $ali_out_max_val, $ali_out_min_val)
    {

        $data = array(
            'code' => $this->getCode(),
            'user_id' => $user['id'],
            'user_name' => $user['user_name'],
            'create_time' => date('Y-m-d H:i:s'),
            'invitation_num' => 1,
            'end_time' => date('Y-m-d H:i:s', time() + (24 * 60 * 60)),
            'type' => 1,
            'status' => 1,
            'bank_min_val' => $bank_min_val,
            'bank_max_val' => $bank_max_val,
            'wx_min_val' => $wx_min_val,
            'wx_max_val' => $wx_max_val,
            'ali_min_val' => $ali_min_val,
            'ali_max_val' => $ali_max_val,
            'bank_out_max_val' => $bank_out_max_val,
            'bank_out_min_val' => $bank_out_min_val,
            'wx_out_max_val' => $wx_out_max_val,
            'wx_out_min_val' => $wx_out_min_val,
            'ali_out_max_val' => $ali_out_max_val,
            'ali_out_min_val' => $ali_out_min_val

        );

        $this->_getInvitationModel()->setMyInvitation($data);

        return true;
    }


    public function delMyInvitation($user, $id)
    {

        $file = array(
            'id' => $id,
            'user_id' => $user['id'],
        );

        $this->_getInvitationModel()->delMyInvitation($file);

        return true;
    }

    public function setCollectInfoStatus($id, $user_id, $status)
    {

        $file = array(
            'id' => $id,
            'user_id' => $user_id
        );
        $collectInfo = $this->_getUserCollectInfoModel()->getCollectInfo($file);
        if (empty($collectInfo)) {
            return "无效信息";
        }

        $data = array();

        if ($status == 1) {
            $data['assign'] = 1;
        } else {
            $data['assign'] = 0;
        }


        $this->_getUserCollectInfoModel()->upCollectInfo($file, $data);
        return "";
    }


}
