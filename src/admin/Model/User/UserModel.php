<?php

namespace Admin\Model\User;

use Admin\Common\BaseModel;
use Admin\Common\ComRedis;

class UserModel extends BaseModel
{


    protected function getTableName($id)
    {
        return 'user';
    }

    public function addUser($data)
    {
        return $this->getORM()->insert($data);
    }

    public function getUserAccount($user_account)
    {
        return $this->getORM()->where('account', $user_account)->fetchOne();
    }

    //锁 ,account_sum 变动
    public function changeUserAmount($user_id, $change_sum, $isAdd)
    {
        $lock = 'user' . $user_id;
        $isLock = ComRedis::lock($lock);
        if (!$isLock) {
            return null;
        }

        $file = array('id' => $user_id);

        $beforeUser = $this->getORM()->where($file)->fetchOne();
        if ($isAdd) {
            $afterAmount = $beforeUser['account_amount'] + $change_sum;
        } else {
            $afterAmount = $beforeUser['account_amount'] - $change_sum;
            if ($afterAmount < 0) {
                ComRedis::unlock($lock);
                return null;
            }
        }

        $this->getORM()->where($file)->update(array('account_amount' => $afterAmount));

        $afterUser = $this->getORM()->where($file)->fetchOne();

        ComRedis::unlock($lock);

        return array(
            'id' => $user_id,
            'beforeAmount' => $beforeUser['account_amount'],
            'changAmount' => $change_sum,
            'afterAmount' => $afterUser['account_amount']
        );
    }


    public function getsUserGroup()
    {
        return $this->getORM()->select('id,user_name,account,group_id,group_name')->where('is_top=1')->fetchAll();
    }


    public function getsAllUser()
    {
        return $this->getORM()->select('id,user_name,account,group_id,group_name,is_top')->fetchAll();
    }


    public function getUserList(array $file, $like_file, $page, $limit)
    {

        $data = $this->getORM()
            ->where($file)
            ->where($like_file)
            ->limit($limit * ($page - 1), $limit)
            ->order('id desc')
            ->fetchAll();
        $total = $this->getORM()->where($file)->where($like_file)->count();
        $res = array(
            "data" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function modUserStatus($id, $data)
    {
        $this->getORM()->where('id', $id)->update($data);
    }

    public function getUserId($id)
    {
        return $this->getORM()->where('id', $id)->fetchOne();
    }


}
