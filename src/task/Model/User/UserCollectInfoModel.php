<?php

namespace Task\Model\User;

use Task\Common\BaseModel;

class UserCollectInfoModel extends BaseModel
{

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
