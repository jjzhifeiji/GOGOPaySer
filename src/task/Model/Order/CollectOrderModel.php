<?php

namespace Task\Model\Order;

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
            ->where('status', 2)
            ->fetchAll();
    }

    public function createOrder($data)
    {
        $this->getORM()->insert($data);
        return $this->getORM()->insert_id();
    }

    public function timeOutOrder($order)
    {
        $file = array('id' => $order['id'], 'status' => 2);
        $data = array('status' => 4);
        return $this->getORM()->where($file)->update($data);
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
