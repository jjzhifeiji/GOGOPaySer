<?php

namespace Admin\Model\Order;

use Admin\Common\BaseModel;

class OutOrderModel extends BaseModel
{

    public function createOutOrder(array $data)
    {
        return $this->getORM()->insert($data);
    }

    public function confirmOutOrder(array $file, array $data)
    {
        return $this->getORM()->where($file)->update($data);
    }

    public function getconfirmOutOrder($id)
    {
        $file = array(
            'id' => $id,
            'status' => 3
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    public function getOutOrder($id)
    {
        $file = array(
            'id' => $id
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    public function getsOutOrder(array $file, $page, $limit)
    {
        $data = $this->getORM()->where($file)->limit($limit * ($page - 1), $limit)->order('id desc')->fetchAll();
        $allnum = $this->getORM()->where($file)->count();
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
