<?php

namespace Admin\Model\User;

use Admin\Common\BaseModel;

class UserAmountRecordModel extends BaseModel
{


    public function addUserLog($data)
    {
        $this->getORM()->insert($data);
    }

    public function getUserRecord($file, $page, $limit)
    {
        $data = $this->getORM()->where($file) ->order("id desc")->limit($limit * ($page - 1), $limit)->fetchAll();
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
        return 'user_amount_record';
    }


}
