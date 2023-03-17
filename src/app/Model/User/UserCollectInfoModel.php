<?php

namespace App\Model\User;

use App\Common\BaseModel;

class UserCollectInfoModel extends BaseModel
{


    public function addCollectInfo($data)
    {
        return $this->getORM()->insert($data);
    }

    public function getCode($user_id)
    {
        return $this->getORM()->select('*')
            ->where('user_id', $user_id)
            ->where('status', 1)
            ->fetchOne();
    }

    public function getCollectInfoList(array $file)
    {
        return $this->getORM()->select('*')
            ->where($file)
            ->where('status', 1)
            ->order("id desc")
            ->fetchAll();
    }

    public function delCollectInfo($file, $data)
    {
        $this->getORM()->where($file)->update($data);
    }

    public function getCollectInfo(array $file)
    {
        return $this->getORM()->select('*')
            ->where($file)
            ->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'user_collect_info';
    }


}
