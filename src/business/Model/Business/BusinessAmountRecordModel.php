<?php

namespace Business\Model\Business;

use Business\Common\BaseModel;

class BusinessAmountRecordModel extends BaseModel
{

    public function addBusinessLog($data)
    {
        $this->getORM()->insert($data);
    }

    public function getBusinessRecord($file, $page, $limit)
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

    protected function getTableName($id)
    {
        return 'business_amount_record';
    }


}
