<?php

namespace Task\Model\User;

use Task\Common\BaseModel;

class UserCollectInfoModel extends BaseModel
{

    public function getAssignCode($type)
    {
        return $this->getORM()->where(array('status' => 1, 'assign' => 1, 'type' => $type))->fetchAll();
    }

    protected function getTableName($id)
    {
        return 'user_collect_info';
    }

    public function getCode($user_id, $code_id)
    {
        return $this->getORM()
            ->select('*')
            ->where('id', $code_id)
            ->where('user_id', $user_id)
            ->fetchOne();
    }

}
