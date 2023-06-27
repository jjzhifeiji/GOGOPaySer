<?php

namespace Task\Common;

use Task\Model\Admin\AdminModel;
use Task\Model\Business\BusinessAmountRecordModel;
use Task\Model\Business\BusinessModel;
use Task\Model\Order\CollectOrderModel;
use Task\Model\Order\OutOrderModel;
use Task\Model\Sys\SystemModel;
use Task\Model\User\UserAmountRecordModel;
use Task\Model\User\UserCollectInfoModel;
use Task\Model\User\UserModel;

class BaseDomain
{

    protected $UserModel;

    protected function _getUserModel(): UserModel
    {
        return empty($this->UserModel) ? new UserModel() : $this->UserModel;
    }

    protected $AdminModel;

    protected function _getAdminModel(): AdminModel
    {
        return empty($this->AdminModel) ? new AdminModel() : $this->AdminModel;
    }

    protected $BusinessModel;

    protected function _getBusinessModel(): BusinessModel
    {
        return empty($this->BusinessModel) ? new BusinessModel() : $this->BusinessModel;
    }

    protected $CollectOrderModel;

    protected function _getCollectOrderModel(): CollectOrderModel
    {
        return empty($this->CollectOrderModel) ? new CollectOrderModel() : $this->CollectOrderModel;
    }


    protected $OutOrderModel;

    protected function _getOutOrderModel(): OutOrderModel
    {
        return empty($this->OutOrderModel) ? new OutOrderModel() : $this->OutOrderModel;
    }


    protected $UserCollectInfoModel;

    protected function _getUserCollectInfoModel(): UserCollectInfoModel
    {
        return empty($this->UserCollectInfoModel) ? new UserCollectInfoModel() : $this->UserCollectInfoModel;
    }


    protected $BusinessAmountRecordModel;

    protected function _getBusinessAmountRecordModel()
    {
        return empty($this->BusinessAmountRecordModel) ? new BusinessAmountRecordModel() : $this->BusinessAmountRecordModel;
    }

    protected $UserAmountRecordModel;

    protected function _getUserAmountRecordModel()
    {
        return empty($this->UserAmountRecordModel) ? new UserAmountRecordModel() : $this->UserAmountRecordModel;
    }

    protected $SystemModel;

    protected function _getSystemModel()
    {
        return empty($this->SystemModel) ? new SystemModel() : $this->SystemModel;
    }

}
