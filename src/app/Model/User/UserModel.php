<?php

namespace App\Model\User;

use App\Common\BaseModel;
use App\Common\ComRedis;

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

    protected
    function getTableName($id)
    {
        return 'user';
    }

    public function getInfo($userId)
    {
        return $this->getORM()->select('*')->where(array('id' => $userId))->fetchOne();
    }

    public function getUserInfo($userId)
    {
        return $this->getORM()->select('account,account_amount')->where(array('id' => $userId))->fetchOne();
    }

    public function getInfoAccount($account)
    {
        return $this->getORM()->select('*')->where('account', $account)->fetchOne();
    }


}
