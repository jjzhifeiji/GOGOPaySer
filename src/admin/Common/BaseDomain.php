<?php

namespace Admin\Common;

use Admin\Model\Admin\AdminModel;
use Admin\Model\Business\BusinessAmountRecordModel;
use Admin\Model\Business\BusinessModel;
use Admin\Model\Order\CollectOrderModel;
use Admin\Model\Order\OutOrderModel;
use Admin\Model\User\CollectInfoModel;
use Admin\Model\User\UserAmountRecordModel;
use Admin\Model\User\UserModel;

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

    protected $UserAmountRecordModel;

    protected function _getUserAmountRecordModel(): UserAmountRecordModel
    {
        return empty($this->UserAmountRecordModel) ? new UserAmountRecordModel() : $this->UserAmountRecordModel;
    }

    protected $CollectInfoModel;

    protected function _getCollectInfoModel(): CollectInfoModel
    {
        return empty($this->CollectInfoModel) ? new CollectInfoModel() : $this->CollectInfoModel;
    }


    protected $BusinessAmountRecordModel;

    protected function _getBusinessAmountRecordModel()
    {
        return empty($this->BusinessAmountRecordModel) ? new BusinessAmountRecordModel() : $this->BusinessAmountRecordModel;
    }
}
