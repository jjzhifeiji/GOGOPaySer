<?php

namespace App\Api\Order;

use App\Common\BaseController;
use App\Common\ComRedis;

/**
 * 订单7000
 */
class CollectOrderController extends BaseController
{
    public function getRules()
    {
        return array(
            'getCollectOrderList' => array(
                'status' => array('name' => 'status', 'desc' => ''),
                'page' => array('name' => 'page', 'default' => '1', 'desc' => '页数'),
                'limit' => array('name' => 'limit', 'default' => '20', 'desc' => '数量')
            ),
            'getCollectOrder' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true),
            ),
            'takeCollectOrder' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true),
            ),
            'configCollectOrder' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true),
            ),
            'configCollectPictureOrder' => array(
                'file' => array(
                    'name' => 'file',        // 客户端上传的文件字段
                    'type' => 'file',
                    'require' => true,
                    'max' => 100 * 1024 * 1024,        // 最大允许上传100M = 200 * 1024 * 1024,
                    'ext' => 'jpg, png, jpeg', // 允许的文件扩展名
                    'desc' => '待上传的图片文件',
                ),
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true),
                'remark' => array('name' => 'remark', 'desc' => '')
            ),
        );
    }

    /**
     *
     */
    public function getWaitCollectOrderList()
    {
        $user = $this->member_arr;
        $res = $this->_getCollectOrderDomain()->getWaitCollectOrderList($user);
        return $this->api_success($res);
    }

    /**
     *
     */
    public function getCollectOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;
        $res = $this->_getCollectOrderDomain()->getCollectOrder($user, $id);
        return $this->api_success($res);
    }

    /**
     *
     */
    public function takeCollectOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;
        $isLock = $this->getCache('take' . $id);
        if ($isLock == true) {
            \PhalApi\DI()->logger->error('take' . $id . '<-确认->' . $isLock);
            return $this->api_error(1001, "too late");
        }
        $this->setCache('take' . $id, true, 60);
        $res = $this->_getCollectOrderDomain()->takeCollectOrder($id, $user);
        $this->delCache('take' . $id);

        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    /**
     *  获取我的订单
     */
    public function getCollectOrderList()
    {
        $user = $this->member_arr;
        $status = $this->status;
        $page = $this->page;
        $limit = $this->limit;
        $res = $this->_getCollectOrderDomain()->getCollectOrderList($user, $status, $page, $limit);
        return $this->api_success($res);
    }


    /**
     *  确认订单
     */
    public function configCollectOrder()
    {
        $user = $this->member_arr;
        $id = $this->id;

        $isLock = $this->getCache('config' . $id);
        if ($isLock == true) {
            \PhalApi\DI()->logger->error('config' . $id . '<-确认->' . $isLock);
            return $this->api_error(1001, "too late");
        }
        $this->setCache('config' . $id, true, 60);

        $res = $this->_getCollectOrderDomain()->configCollectOrderList($user, $id);
        $this->delCache('config' . $id);

        if (empty($res)) {
            return $this->api_success();
        } else {
            return $this->api_error(1000, $res);
        }
    }

    /**
     * 确认订单
     */
    public function configCollectPictureOrder()
    {
        $user = $this->member_arr;

        $id = $this->id;
        $remark = $this->remark;
        $tmpName = $this->file['tmp_name'];
        $file_name = $this->file['name'];

        \PhalApi\DI()->logger->info('确认订单-' . $id . '-' . $user['id']);

        if (!is_dir(RESOURCE_DIR . IMAGE_SOURCE_CONFIG)) {
            mkdir(RESOURCE_DIR . IMAGE_SOURCE_CONFIG, 0777);
        }

        $isLock = $this->getCache('config' . $id);
        if ($isLock == true) {
            \PhalApi\DI()->logger->error('config' . $id . '<-确认->' . $isLock);
            return $this->api_error(1001, "too late");
        }
        $this->setCache('config' . $id, true, 60);

        $path = RESOURCE_DIR . IMAGE_SOURCE_CONFIG . $file_name;
        if (move_uploaded_file($tmpName, $path)) {

            $url = HTTP_RESOURCE . IMAGE_SOURCE_CONFIG . $file_name;
            \PhalApi\DI()->logger->info('upLoadAudioFile 上传成功', $path);
            \PhalApi\DI()->logger->info('upLoadAudioFile 上传成功', $url);

            $res = $this->_getCollectOrderDomain()->configCollectOrderList($user, $id, $url);
            $this->delCache('config' . $id);

            if (empty($res)) {
                return $this->api_success();
            } else {
                return $this->api_error(1001, $res);
            }

        } else {
            \PhalApi\DI()->logger->info('upLoadAudioFile 上传失败', $path);
            $this->delCache('config' . $id);

            return $this->api_error(1002, "上传失败");
        }

    }


}
