<?php

namespace Admin\Model\Order;

use Admin\Common\BaseModel;

class CollectOrderModel extends BaseModel
{

    public function getCollectOrderList(array $file, $offset, $limit)
    {
        $data = $this->getORM()->where($file)->limit($limit * ($offset - 1), $limit)->order('id desc')->fetchAll();
        $allnum = $this->getORM()->where($file)->count();
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

    public function upCollectOrder(array $file, array $data)
    {
        return $this->getORM()->where($file)->update($data);
    }

    protected function getTableName($id)
    {
        return 'collect_order';
    }

}
