<?php

namespace Business\Model\Business;

use Business\Common\ComRedis;
use Business\Common\BaseModel;

class BusinessModel extends BaseModel
{

    protected function getTableName($id)
    {
        return 'business';
    }

    public function getsBusiness($page, $limit)
    {
        $data = $this->getORM()->where('status', 1)->limit($limit * ($page - 1), $limit)->fetchAll();
        $allnum = $this->getORM()->where('status', 1)->count();
        $res = array(
            "data" => $data,
            "total" => $allnum,
            "page" => $page,
            "limit" => $limit
        );
        return $res;
    }

    public function getBusiness($data)
    {
        return $this->getORM()->where($data)->fetchOne();
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
}
