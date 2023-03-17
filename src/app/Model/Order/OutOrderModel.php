<?php

namespace App\Model\Order;

use App\Common\BaseModel;

class OutOrderModel extends BaseModel
{

    public function getsOutingOrder(array $file)
    {
        $res = $this->getORM()->where($file)->fetchAll();
        return $res;
    }

    public function getsOutOrder(array $file, $page, $limit)
    {
        $data = $this->getORM()->where($file)->order("id desc")->limit($limit * ($page - 1), $limit)->fetchAll();
        $allnum = $this->getORM()->where($file)->count();
        $res = array(
            "data" => $data,
            "total" => $allnum,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function upOutOrder(array $file, array $data)
    {
        return $this->getORM()->where($file)->update($data);
    }

    public function getOutOrder($id)
    {
        return $this->getORM()->where('id', $id)->fetchOne();
    }

    public function getOutOrderSn($order_no)
    {
        return $this->getORM()->where('order_no', $order_no)->fetchOne();
    }

    public function createOutOrder(array $data)
    {
        return $this->getORM()->insert($data);
    }

    protected function getTableName($id)
    {
        return 'out_order';
    }

}
