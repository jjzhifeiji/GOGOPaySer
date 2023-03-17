<?php

namespace Admin\Model\Business;

use Admin\Common\BaseModel;

class BusinessModel extends BaseModel
{

    protected function getTableName($id)
    {
        return 'business';
    }

    public function getsBusiness($page, $limit)
    {
        $data = $this->getORM()->where('status', 1)->limit($limit * ($page - 1), $limit)->fetchAll();
        $allnum = $this->getORM()->where('status', 1)->count();
        $res = array(
            "data" => $data,
            "total" => $allnum,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function addBusiness($data)
    {
        return $this->getORM()->insert($data);
    }

}
