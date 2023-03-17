<?php

namespace Task\Model\User;

use Task\Common\BaseModel;
use Task\Common\ComRedis;

class UserModel extends BaseModel
{



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

    public function getAllUserList()
    {
        return $this->getORM()->select('id,user_name,user_account,group_id,group_name,filtration_status')->where('status=1')->fetchAll();
    }

    public function delUser($id)
    {
        return $this->getORM()->where('id', $id)->update(array('status' => 0));
    }

    public function getUserId($id)
    {
        return $this->getORM()->select('*')->where('id', $id)->fetchOne();
    }

    public function upUser($id, array $data)
    {
        return $this->getORM()->where('id', $id)->update($data);
    }

    public function getUserName($user_name)
    {
        return $this->getORM()->select('id')->where('user_name', $user_name)->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'user';
    }

    public function getUsers($userIds)
    {
        return $this->getORM()->select('*')->where('id', $userIds)->fetchAll();
    }

    public function getInfo($userId)
    {
        return $this->getORM()->select('*')->where('id', $userId)->fetchOne();
    }

    public function getInfoAccount($account)
    {
        return $this->getORM()->select('*')->where('user_account = ?', $account)->fetchOne();
    }

    public function upToken($user_id, $token)
    {
        $this->getORM()->where('id', $user_id)->update(array('token' => $token));
    }

    public function getsUser($file, $offset, $limit)
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

    public function getsFileUser($file)
    {
        $res = $this->getORM()->where($file)->order('id desc')->fetchAll();
        return $res;
    }

    public function upUserPwd($id, $encryptPassword)
    {
        return $this->getORM()->where('id', $id)->update(array('pwd' => $encryptPassword));
    }

}
