<?php

namespace App\Model\User;

use App\Common\BaseModel;

class UserCollectInfoModel extends BaseModel
{


    public function addCollectInfo($data)
    {
        return $this->getORM()->insert($data);
    }

    public function getCode($user_id, $pay_type)
    {
        return $this->getORM()->select('*')
            ->where('user_id', $user_id)
            ->where('type', $pay_type)
            ->where('status', 1)
            ->fetchOne();
    }

    public function getCollectInfoList(array $file, $page, $limit)
    {
        $data = $this->getORM()->where($file)->where('status', 1)->order("id desc")->limit($limit * ($page - 1), $limit)->fetchAll();
        $allnum = $this->getORM()->where($file)->where('status', 1)->count();
        $res = array(
            "data" => $data,
            "total" => $allnum,
            "page" => $page,
            "limit" => $limit
        );
        return $res;

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
