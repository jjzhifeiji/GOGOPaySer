<?php

namespace Task\Model\CollectOrder;

use Task\Common\BaseModel;
use Task\Common\ComRedis;

class CollectOrderModel extends BaseModel
{

    public function getOrder($orderNo)
    {
        return $this->getORM()
            ->select('order_no,status,pay_type,user_id,create_time,order_amount,code_id')
            ->where('order_no', $orderNo)
            ->fetchOne();
    }

    public function getCheckOrder()
    {
        return $this->getORM()
            ->where('type', 2)
            ->fetchAll();
    }

    public function createOrder($data)
    {
        return $this->getORM()->insert($data);
    }

    public function timeOutOrder($order)
    {
        $orderLock = 'collect' . $order['id'];
        $isLock = ComRedis::lock($orderLock);
        if (!$isLock) {
            return "error";
        }
        $file = array('id' => $order['id'], 'status' => 1);
        $data = array('status' => 4);
        $this->getORM()->where($file)->update($data);
        ComRedis::unlock($orderLock);
        return null;
    }

    public function getPlatformOrder(array $file)
    {
        return $this->getORM()
            ->select('order_no,status,pay_type,user_id,create_time,order_amount,code_id,business_no')
            ->where($file)
            ->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'collect_order';
    }

}
