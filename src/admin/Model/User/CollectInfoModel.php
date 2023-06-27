<?php

namespace Admin\Model\User;

use Admin\Common\BaseModel;

class CollectInfoModel extends BaseModel
{

    public function getCollectInfo($code_id)
    {
        $file = array(
            'id' => $code_id
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'user_collect_info';
    }

    public function getCollectInfoList(array $file, $page, $limit)
    {

        $data = $this->getORM()
            ->where($file)
            ->limit($limit * ($page - 1), $limit)
            ->order('id desc')
            ->fetchAll();
        $total = $this->getORM()->where($file)->count();
        $res = array(
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function modCollectInfoStatus($id, $data)
    {
        $this->getORM()->where('id', $id)->update($data);
    }


}
