<?php

namespace Task\Model\CollectOrder;

use Task\Common\BaseModel;

class OutOrderModel extends BaseModel
{

    public function getOutOrder($order_no)
    {
        return $this->getORM()->where('order_no', $order_no)->fetchOne();
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
