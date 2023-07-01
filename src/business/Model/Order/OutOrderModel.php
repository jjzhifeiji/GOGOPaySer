<?php

namespace Business\Model\Order;

use Business\Common\BaseModel;

class OutOrderModel extends BaseModel
{

    public function createOutOrder(array $data)
    {
        return $this->getORM()->insert($data);
    }

    public function getOutOrder($order_no)
    {
        return $this->getORM()->where('order_no', $order_no)->fetchOne();
    }

    public function getsOutOrder(array $file, $start_time, $end_time, $page, $limit)
    {
        $order = $this->getORM();
        if (!empty($start_time)) {
            $order->where('start_time > ' . $start_time);
        }
        if (!empty($end_time)) {
            $order->where('end_time < ' . $end_time);
        }

        $data = $order->where($file)
            ->limit($limit * ($page - 1), $limit)->fetchAll();
        $allnum = $order
            ->where($file)
            ->count();
        $res = array(
            "data" => $data,
            "allnum" => $allnum,
            "offset" => $page,
            "limit" => $limit
        );
        return $res;
    }

    protected function getTableName($id)
    {
        return 'out_order';
    }

}
