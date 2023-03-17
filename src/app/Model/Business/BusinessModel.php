<?php

namespace App\Model\Business;

use App\Common\BaseModel;
use App\Common\ComRedis;

class BusinessModel extends BaseModel
{

    public function getBusiness($id)
    {
        $file = array(
            'id' => $id
        );
        return $this->getORM()->where($file)->fetchOne();
    }

    //锁 ,account_sum 变动
    public function changeBusinessAmount($business_id, $change_sum, bool $isAdd)
    {
        $lock = 'business' . $business_id;
        $isLock = ComRedis::lock($lock);
        if (!$isLock) {
            return null;
        }
        $file = array('id' => $business_id);
        $beforeBusiness = $this->getORM()->where($file)->fetchOne();
        if ($isAdd) {
            $afterAmount = $beforeBusiness['business_amount'] + $change_sum;
        } else {
            $afterAmount = $beforeBusiness['business_amount'] - $change_sum;
            if ($afterAmount < 0) {
                ComRedis::unlock($lock);
                return null;
            }
        }

        $this->getORM()->where($file)->update(array('business_amount' => $afterAmount));

        $afterBusiness = $this->getORM()->where($file)->fetchOne();
        ComRedis::unlock($lock);

        return array(
            'id' => $beforeBusiness['id'],
            'beforeAmount' => $beforeBusiness['business_amount'],
            'changAmount' => $change_sum,
            'afterAmount' => $afterBusiness['business_amount']
        );
    }


    protected function getTableName($id)
    {
        return 'business';
    }

}
