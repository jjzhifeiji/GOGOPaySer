<?php
/**
 * Created by PhpStorm.
 * IController: lijingzhe
 * Date: 2018/5/9
 * Time: 下午9:19
 */

namespace Proxy\Common;

use Proxy\Model\User\UserModel;

class BaseDomain
{

    //用户model
    protected $UserModel;

    protected function _getUserModel()
    {
        return empty($this->UserModel) ? new UserModel() : $this->UserModel;
    }

    //管理员用户model
    protected $AdminModel;

    protected function _getAdminModel()
    {
        return empty($this->AdminModel) ? new AdminModel() : $this->AdminModel;
    }

    //导入记录
    protected $ImportResultModel;

    protected function _getImportResultModel()
    {
        return empty($this->ImportResultModel) ? new ImportResultModel() : $this->ImportResultModel;
    }

    //图片导入记录
    protected $ImageResourceModel;

    protected function _getImageResourceModel()
    {
        return empty($this->ImageResourceModel) ? new ImageResourceModel() : $this->ImageResourceModel;
    }

    //图片识别解析
    protected $AnalysisImageAPI;

    protected function _getAnalysisImageAPI()
    {
        return empty($this->AnalysisImageAPI) ? new AnalysisImageAPI() : $this->AnalysisImageAPI;
    }

    //图片解析结果记录
    protected $ImageResultResourceModel;

    protected function _getImageResultResourceModel()
    {
        return empty($this->ImageResultResourceModel) ? new ImageResultResourceModel() : $this->ImageResultResourceModel;
    }

    //资源类型
    protected $ResourceTypeModel;

    protected function _getResourceTypeModel()
    {
        return empty($this->ResourceTypeModel) ? new ResourceTypeModel() : $this->ResourceTypeModel;
    }

    //资源类型组
    protected $ResourceTypeGroupModel;

    protected function _getResourceTypeGroupModel()
    {
        return empty($this->ResourceTypeGroupModel) ? new ResourceTypeGroupModel() : $this->ResourceTypeGroupModel;
    }

    //资源库
    protected $ResourceModel;

    protected function _getResourceModel()
    {
        return empty($this->ResourceModel) ? new ResourceModel() : $this->ResourceModel;
    }

    //资源详情
    protected $ResourceDetailModel;

    protected function _getResourceDetailModel()
    {
        return empty($this->ResourceDetailModel) ? new ResourceDetailModel() : $this->ResourceDetailModel;
    }

    //资源库
    protected $ResourceDelCacheModel;

    protected function _getResourceDelCacheModel()
    {
        return empty($this->ResourceDelCacheModel) ? new ResourceDelCacheModel() : $this->ResourceDelCacheModel;
    }

    //组管理
    protected $GroupModel;

    protected function _getGroupModel()
    {
        return empty($this->GroupModel) ? new GroupModel() : $this->GroupModel;
    }

    //客户
    protected $CustomerModel;

    protected function _getCustomerModel()
    {
        return empty($this->CustomerModel) ? new CustomerModel() : $this->CustomerModel;
    }

    //小结
    protected $ResourceSuborderModel;

    protected function _getResourceSuborderModel()
    {
        return empty($this->ResourceSuborderModel) ? new ResourceSuborderModel() : $this->ResourceSuborderModel;
    }

    //放弃原因
    protected $ReasonsModel;

    protected function _getReasonsModel()
    {
        return empty($this->ReasonsModel) ? new ReasonsModel() : $this->ReasonsModel;
    }

    //合同
    protected $ContractModel;

    protected function _getContractModel()
    {
        return empty($this->ContractModel) ? new ContractModel() : $this->ContractModel;
    }

    //子合同
    protected $ContractSubModel;

    protected function _getContractSubModel()
    {
        return empty($this->ContractSubModel) ? new ContractSubModel() : $this->ContractSubModel;
    }

    //子合同
    protected $SettingModel;

    protected function _getSettingModel()
    {
        return empty($this->SettingModel) ? new SettingModel() : $this->SettingModel;
    }


    //---------------------------API-------------------------------------------------------------------
    //腾讯API
    protected $TencentAPI;

    protected function _getTencentAPI()
    {
        return empty($this->TencentAPI) ? new TencentAPI() : $this->TencentAPI;
    }


}
