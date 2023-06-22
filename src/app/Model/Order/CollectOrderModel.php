<?php

namespace App\Model\Order;

use App\Common\BaseModel;

class CollectOrderModel extends BaseModel
{

    public function getOrdering(array $file)
    {
        $data = $this->getORM()
            ->select('id')
            ->where($file)
            ->fetchAll();
        return $data;
    }

    public function getWaitCollectOrderList(array $file)
    {
        $data = $this->getORM()
            ->select('id,order_no,status,type,pay_type,business_name,create_time,order_amount,cost_free')
            ->where($file)
            ->order("id desc")
            ->fetchAll();
        return $data;
    }

    public function getCollectOrderList(array $file, $page, $limit)
    {
        $data = $this->getORM()->where($file)->order('id desc')->limit($limit * ($page - 1), $limit)->fetchAll();
        $total = $this->getORM()->where($file)->count();
        $res = array(
            "data" => $data,
            "total" => $total,
            "page" => $page,
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

    public function getTakeCollectOrder($id)
    {
        $file = array(
            'id' => $id,
            'status' => 1
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    public function takeCollectOrder($file, $data)
    {
        return $this->getORM()->where($file)->update($data);
    }

    public function configCollectOrderList(array $file, array $data)
    {
        return $this->getORM()->where($file)->update($data);
    }

    protected function getTableName($id)
    {
        return 'collect_order';
    }

}
