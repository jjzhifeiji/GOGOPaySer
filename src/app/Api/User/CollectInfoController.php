<?php

namespace App\Api\User;


use App\Common\BaseController;

/**
 * 收款信息 4000
 */
class CollectInfoController extends BaseController
{
    public function getRules()
    {
        return array(
            'getCollectInfoList' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'addCollectInfo' => array(
                'type' => array('name' => 'type', 'require' => true, 'desc' => '类型，1、银行卡 2、微信 3、支付宝 '),
                'amount' => array('name' => 'amount', 'require' => true, 'default' => 0, 'desc' => '金额'),
                'pay_info' => array('name' => 'pay_info', 'require' => true, 'desc' => '收款信息'),
            ),
            'delCollectInfo' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '收款信息ID'),
            ),
            'getCollectInfo' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => '收款信息ID'),
            ),
            'upPictures' => array(
                'file' => array(
                    'name' => 'file',        // 客户端上传的文件字段
                    'type' => 'file',
                    'require' => true,
                    'max' => 4 * 1024 * 1024,        // 最大允许上传2M = 2 * 1024 * 1024,
                    'ext' => 'jpg,png,jpeg', // 允许的文件扩展名
                    'desc' => '待上传的图片文件',
                )
            ),

        );
    }

    /**
     * 获取收款信息列表
     * @desc 获取收款信息列表
     */
    public function getCollectInfoList()
    {
        $user = $this->member_arr;
        $res = $this->_getCollectInfoDomain()->getCollectInfoList($user['id']);
        return $this->api_success($res);
    }

    /**
     *  添加收款信息
     * @desc 添加收款信息
     */
    public function addCollectInfo()
    {
        $user = $this->member_arr;
        $type = $this->type;
        $amount = $this->amount;
        $pay_info = $this->pay_info;

        $res = $this->_getCollectInfoDomain()->addCollectInfo($user, $type, $amount, $pay_info);
        if ($res == true) {
            return $this->api_success();
        } else {
            return $this->api_error(4001, $res);
        }
    }


    /**
     *  上传图片
     * @desc 上传图片
     */
    public function upPictures()
    {
        $user = $this->member_arr;
        $tmpName = $this->file['tmp_name'];

        $file_name = md5($this->file['name'] . $_SERVER['REQUEST_TIME']);
        $ext = strrchr($this->file['name'], '.');
        $source_file = $file_name . $ext;

        $res_path = IMAGE_SOURCE_COLLECT . $source_file;

        if (!is_dir(RESOURCE_DIR . IMAGE_SOURCE_COLLECT)) {
            mkdir(RESOURCE_DIR . IMAGE_SOURCE_COLLECT, 0777);
        }

        $path = RESOURCE_DIR . $res_path;
        if (move_uploaded_file($tmpName, $path)) {
            \PhalApi\DI()->logger->info('path', $path);
            $res = HTTP_RESOURCE . $res_path;
            \PhalApi\DI()->logger->info('url', $res);
            return $this->api_success($res);
        } else {
            \PhalApi\DI()->logger->info('tmpName', $tmpName);
            \PhalApi\DI()->logger->info('path', $path);
            return $this->api_error("4002", "上传失败");
        }


    }

    /**
     * 删除收款信息
     * @desc 删除收款信息
     */
    public function delCollectInfo()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getCollectInfoDomain()->delCollectInfo($user, $id);
        if ($res == true) {
            return $this->api_success();
        } else {
            return $this->api_error(4003, $res);
        }
    }

    /**
     * 获取收款信息
     * @desc 获取收款信息详情
     */
    public function getCollectInfo()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getCollectInfoDomain()->getCollectInfo($user, $id);
        return $this->api_success($res);

    }

}
