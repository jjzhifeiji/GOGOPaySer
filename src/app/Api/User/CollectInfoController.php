<?php

namespace App\Api\User;


use App\Common\BaseController;

/**
 * 用户数据2000
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
                'type' => array('name' => 'type', 'require' => true, 'desc' => ''),
                'amount' => array('name' => 'amount', 'require' => true, 'default' => 0, 'desc' => ''),
                'pay_info' => array('name' => 'pay_info', 'require' => true, 'desc' => ''),
            ),
            'delCollectInfo' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => ''),
            ),
            'getCollectInfo' => array(
                'id' => array('name' => 'id', 'require' => true, 'desc' => ''),
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
     */
    public function getCollectInfoList()
    {
        $user = $this->member_arr;
        $res = $this->_getCollectInfoDomain()->getCollectInfoList($user['id']);
        return $this->api_success($res);
    }

    /**
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
            return $this->api_error(2003, $res);
        }
    }

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
            return $this->api_error("1010", "上传失败");
        }


    }

    /**
     */
    public function delCollectInfo()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getCollectInfoDomain()->delCollectInfo($user, $id);
        if ($res == true) {
            return $this->api_success();
        } else {
            return $this->api_error(2004, $res);
        }
    }

    /**
     */
    public function getCollectInfo()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $res = $this->_getCollectInfoDomain()->getCollectInfo($user, $id);
        return $this->api_success($res);

    }

}
