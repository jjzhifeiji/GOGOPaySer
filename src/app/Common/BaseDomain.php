<?php
/**
 * Created by PhpStorm.
 * IController: lijingzhe
 * Date: 2018/5/9
 * Time: 下午9:19
 */

namespace App\Common;


use App\Model\Business\BusinessAmountRecordModel;
use App\Model\Business\BusinessModel;
use App\Model\Invitation\InvitationModel;
use App\Model\Module\FiltrationAPI;
use App\Model\Order\CollectOrderModel;
use App\Model\Order\OutOrderModel;
use App\Model\Setting\VersionModel;
use App\Model\User\UserAmountRecordModel;
use App\Model\User\UserCollectInfoModel;
use App\Model\User\UserModel;

class BaseDomain
{

    protected function encryptAppKey($params, $private_key)
    {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            $paramsStrExceptSign .= $val;
        }
//        \PhalApi\DI()->logger->debug('推送sign params ->', $params);
//        \PhalApi\DI()->logger->debug('推送sign ->', $paramsStrExceptSign . $private_key);
        $res = md5($paramsStrExceptSign . $private_key);
//        \PhalApi\DI()->logger->debug('推送sign res->', $res);
        return $res;
    }

    protected function getCode()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_code = substr(str_shuffle($characters), 0, 6);
        return $random_code;
    }


    //用户model
    protected $UserModel;

    protected function _getUserModel()
    {
        return empty($this->UserModel) ? new UserModel() : $this->UserModel;
    }

    //版本
    protected $VersionModel;

    protected function _getVersionModel()
    {
        return empty($this->VersionModel) ? new VersionModel() : $this->VersionModel;
    }

    protected $FiltrationAPI;

    protected function _getFiltrationAPI()
    {
        return empty($this->FiltrationAPI) ? new FiltrationAPI() : $this->FiltrationAPI;
    }

    protected $CollectOrderModel;

    protected function _getCollectOrderModel()
    {
        return empty($this->CollectOrderModel) ? new CollectOrderModel() : $this->CollectOrderModel;
    }

    protected $OutOrderModel;

    protected function _getOutOrderModel()
    {
        return empty($this->OutOrderModel) ? new OutOrderModel() : $this->OutOrderModel;
    }

    protected $UserAmountRecordModel;

    protected function _getUserAmountRecordModel()
    {
        return empty($this->UserAmountRecordModel) ? new UserAmountRecordModel() : $this->UserAmountRecordModel;
    }

    protected $BusinessAmountRecordModel;

    protected function _getBusinessAmountRecordModel()
    {
        return empty($this->BusinessAmountRecordModel) ? new BusinessAmountRecordModel() : $this->BusinessAmountRecordModel;
    }


    protected $BusinessModel;

    protected function _getBusinessModel()
    {
        return empty($this->BusinessModel) ? new BusinessModel() : $this->BusinessModel;
    }


    protected $UserCollectInfoModel;

    protected function _getUserCollectInfoModel()
    {
        return empty($this->UserCollectInfoModel) ? new UserCollectInfoModel() : $this->UserCollectInfoModel;
    }

    protected $InvitationModel;

    protected function _getInvitationModel()
    {
        return empty($this->InvitationModel) ? new InvitationModel() : $this->InvitationModel;
    }


}
