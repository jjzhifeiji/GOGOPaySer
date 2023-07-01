<?php

namespace Business\Model\Order;

use Business\Common\BaseModel;

class CollectOrderModel extends BaseModel
{

    public function getCollectOrderList(array $file, $start_time, $end_time, $offset, $limit)
    {
        $order = $this->getORM();
        if (!empty($start_time)) {
            $order->where('start_time > ' . $start_time);
        }
        if (!empty($end_time)) {
            $order->where('end_time < ' . $end_time);
        }

        $data = $order->where($file)->limit($limit * ($offset - 1), $limit)->fetchAll();
        $allnum = $order->where($file)->count();
        $res = array(
            "data" => $data,
            "allnum" => $allnum,
            "offset" => $offset,
            "limit" => $limit
        );
        return $res;
    }

    public function getCollectOrder($id)
    {
        $file = array(
            'id' => $id
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'collect_order';
    }

}
