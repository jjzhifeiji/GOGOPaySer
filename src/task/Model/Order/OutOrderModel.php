<?php

namespace Task\Model\Order;

use Task\Common\BaseModel;

class OutOrderModel extends BaseModel
{

    public function getOutOrder($order_no)
    {
        return $this->getORM()->where('order_no', $order_no)->fetchOne();
    }

    public function getPlatformOrder(array $file)
    {
        return $this->getORM()
            ->select('order_no,business_no,status,pay_type,type,order_amount')
            ->where($file)
            ->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'out_order';
    }

    public function createOutOrder($data)
    {
        return $this->getORM()->insert($data);
    }

}
