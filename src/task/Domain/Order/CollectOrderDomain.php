<?php

namespace Task\Domain\Order;

use App\Api\Order\CollectOrderController;
use Task\Common\BaseDomain;
use Task\Common\ComRedis;
use PhalApi\Tool;
use function PhalApi\DI;

class CollectOrderDomain extends BaseDomain
{


    public function createOrder($pay_type, $amount, $platform, $business_no, $callback_url)
    {

        $collect_free = 350;
        if ($pay_type == 1) {
            $collect_free = $platform['collect_bank_free'];
        } else if ($pay_type == 2) {
            $collect_free = $platform['collect_wx_free'];
        } else if ($pay_type == 3) {
            $collect_free = $platform['collect_ali_free'];
        }


        $data = array(
            'order_no' => 'i' . date('YmdHis') . rand(1000, 9999),
            'type' => 1,
            'pay_type' => $pay_type,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'expire_time' => date('Y-m-d H:i:s', strtotime("+15 minute")),
            'business_id' => $platform['id'],
            'business_name' => $platform['name'],
            'free' => $collect_free,
            'order_amount' => $amount,
            'cost_free' => $amount * $collect_free / 10000,
            'entry_amount' => $amount * (10000 - $collect_free) / 10000,
            'business_no' => $business_no,
            'callback_url' => $callback_url
        );

        $orderId = $this->_getCollectOrderModel()->createOrder($data);
        $data['id'] = $orderId;
        DI()->logger->info($platform['name'] . "createOrder:" . $res);

        $this->autoAssign($data);

        return $data['order_no'];
    }

    //分配
    private function autoAssign($order)
    {
        $isAutoAssign = $this->_getSystemModel()->getAutoAssign();

        if ($isAutoAssign['config_value'] == 0) {
            DI()->logger->info('自动分配关闭' . $isAutoAssign);

            //推送所有用户
            $user = $this->_getUserModel()->getNoticeBotUser();
            $msg = '新代收订单' . $order['order_no'] . ',金额:' . $order['order_amount'] . ',请即使接单';
            foreach ($user as $item) {
                $chartId = $item['chat_id'];
                if (!empty($chartId)) {
                    ComRedis::pushTask(json_encode(array('type' => 'BotMsg', 'content' => json_encode(array('chartId' => $chartId)), 'msg' => $msg)));
                }
            }

            return;
        }
        DI()->logger->info('开始自动分配');

        //TODO 获取可用收款信息
        $code = $this->_getUserCollectInfoModel()->getAssignCode($order['pay_type']);

        //TODO 根据人员已售金额排序选取收款信息
        if (sizeof($code) > 0) {

            $user_min_code = array();
            $min = 100;
            foreach ($code as $item) {
                $u_amount = ComRedis::getRCache($order['pay_type'] . 'collect_amount' . $item['user_id']);
                if ($u_amount < $min) {
                    $min = $u_amount;
                    $user_min_code = $item;
                    DI()->logger->info('');
                }
            }
            if (empty($user_min_code)) {
                DI()->logger->info('暂无可分配用户');
                return;
            }
            DI()->logger->info('匹配信息->' . $user_min_code);

            if (empty($user_min_code['user_id']) || empty($order['id'])) {
                DI()->logger->info('暂无法分配' . $user_min_code);
                DI()->logger->info('暂无法分配' . $order);
                return;
            }
            $user = array('id' => $user_min_code['user_id'], 'user_name' => $user_min_code['user_name']);
            //TODO 分配
            $collectDomain = new \App\Domain\Order\CollectOrderDomain();
            $collectDomain->takeCollectOrder($order['id'], $user);

            //推送所有用户
            $user = $this->_getUserModel()->getUserId($user['id']);
            $chartId = $user['chat_id'];
            if (!empty($chartId)) {
                $msg = '代收订单' . $order['order_no'] . '分配成功,金额:' . $order['order_amount'] . ',请注意查收';
                ComRedis::pushTask(json_encode(array('type' => 'BotMsg', 'content' => json_encode(array('chartId' => $chartId)), 'msg' => $msg)));
            }


        } else {
            DI()->logger->info('暂无可分配用户');
        }
    }

    public function getOrder($orderNo)
    {
        //order_no,status,pay_type,user_id,order_sum,code_id
        $res = $this->_getCollectOrderModel()->getOrder($orderNo);

        $res['end_time'] = date('Y/m/d H:i:s', strtotime($res['create_time']) + 60 * 5);

        $res['pay_no'] = '';
        $res['pay_name'] = '';
        $res['pay_organ'] = '';
        $res['pay_local'] = '';

        if ($res['status'] == 2) {
            $code = $this->_getUserCollectInfoModel()->getCode($res['user_id'], $res['code_id']);
            if (empty($code)) {
                return null;
            }

            //{"pay_bank": "666666", "pay_name": "测试", "pay_account": "66666666666666666666", "pay_bank_local": "6666666"}
            $pi = json_decode($code['pay_info'], true);

            DI()->logger->info("pay_info:" . $code['pay_info']);
            DI()->logger->info("pay_info:" . $pi);

            $res['pay_no'] = $pi['pay_account'];
            $res['pay_name'] = $pi['pay_name'];
            $res['pay_organ'] = $pi['pay_bank'];
            $res['pay_local'] = $pi['pay_bank_local'];
        }

        return $res;
    }

    public function checkOrder()
    {
        $res = $this->_getCollectOrderModel()->getCheckOrder();
        if (sizeof($res) > 0) {
            DI()->logger->info("进行中订单->" . sizeof($res));

            foreach ($res as $order) {
                $ptime = strtotime($order['create_time']);
                $etime = time() - $ptime;
                DI()->logger->info("结束时间->" . $etime);

                //订单五分钟超时
                if ($etime > 60 * 15) {
                    $r = $this->backOrder($order);
                    DI()->logger->info(":超时订单->" . $order['id'] . ':' . $r);
                }
            }
        }

    }

    private function backOrder($order)
    {

        $order_res = $this->_getCollectOrderModel()->timeOutOrder($order);

        if (is_numeric($order_res) && $order_res > 0) {
        } else {
            return "订单有误" . $order_res;
        }

        //退款
        $res = $this->_getUserModel()->changeUserAmount($order['user_id'], $order['order_amount'], true);

        if (empty($res)) {
            DI()->logger->error("backOrder:" . $order);
            return "用户退款失败";
        }

        //用户金额log
        $logData = array(
            'user_id' => $order['user_id'],
            'create_time' => date('Y-m-d H:i:s'),
            'before_amount' => $res['beforeAmount'],
            'change_amount' => $res['changAmount'],
            'result_amount' => $res['afterAmount'],
            'type' => 7,
            'business_id' => $order['business_id'],
            'order_id' => $order['id'],
            'order_no' => $order['order_no'],
            'remark' => '超时退款',
        );
        $this->_getUserAmountRecordModel()->addUserLog($logData);


        return "成功";
    }

    public function getPlatformOrder($platform_id, $order_no, $business_no)
    {
        $file = array('business_id' => $platform_id);

        if (!empty($order_no)) {
            $file['order_no'] = $order_no;
        } else if (!empty($business_no)) {
            $file['business_no'] = $business_no;
        } else {
            return '订单有误1';
        }
        $order = $this->_getCollectOrderModel()->getPlatformOrder($file);

        if (empty($order)) {
            return '订单有误2';
        }
//        order_no,status,pay_type,user_id,create_time,order_amount,code_id,business_no
//        1待接单2已接单，待收款3已收款，已确认，4已超时，流单
        $status = 0;
        switch ($order['status']) {
            case 3:
                $status = 'SUCCESS';
                break;
            case 4:
                $status = 'FAILED';
                break;
            default:
                $status = 'WAITING';
                break;
        }
        $res = array(
            'order_no' => $order['order_no'],
            'order_amount' => $order['order_amount'],
            'create_time' => $order['create_time'],
            'business_no' => $order['business_no'],
            'status' => $status,
            'currency_code' => 'CNY',
            'pay_type' => $order['pay_type'],
        );

        return $res;
    }

    public function closeCode()
    {
        $this->_getUserCollectInfoModel()->closeCode();
    }


}
