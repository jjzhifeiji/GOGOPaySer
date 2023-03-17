<?php
/**
 * Created by PhpStorm.
 * IController: lijingzhe
 * Date: 2018/5/9
 * Time: 下午9:19
 */

namespace Business\Common;

use Business\Model\Business\BusinessAmountRecordModel;
use Business\Model\Business\BusinessModel;
use Business\Model\Order\CollectOrderModel;
use Business\Model\Order\OutOrderModel;
use Business\Model\User\UserModel;

class BaseDomain
{

    //用户model
    protected $UserModel;

    protected function _getUserModel()
    {
        return empty($this->UserModel) ? new UserModel() : $this->UserModel;
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

    protected $BusinessModel;

    protected function _getBusinessModel(): BusinessModel
    {
        return empty($this->BusinessModel) ? new BusinessModel() : $this->BusinessModel;
    }

    protected $BusinessAmountRecordModel;

    protected function _getBusinessAmountRecordModel()
    {
        return empty($this->BusinessAmountRecordModel) ? new BusinessAmountRecordModel() : $this->BusinessAmountRecordModel;
    }
}
