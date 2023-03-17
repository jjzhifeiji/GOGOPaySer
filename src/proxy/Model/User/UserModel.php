<?php

namespace Proxy\Model\User;

use Proxy\Common\BaseModel;

class UserModel extends BaseModel
{


    public function getUserList(array $file, $like_file, $page, $limit)
    {
        $data = $this->getORM()->select('id,user_name,account,status,filtration_status,type,group_id,group_name,remark')->where($file)->where($like_file)->limit($limit * ($page - 1), $limit)->order('id desc')->fetchAll();
        $total = $this->getORM()->where($file)->where($like_file)->count();
        $res = array(
            "items" => $data,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function getAllUserList()
    {
        return $this->getORM()->select('id,user_name,account,group_id,group_name,filtration_status')->where('status=1')->fetchAll();
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
        return $this->getORM()->select('*')->where('account = ?', $account)->fetchOne();
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
