<?php

namespace App\Model\Business;

use App\Common\BaseModel;

class BusinessAmountRecordModel extends BaseModel
{


    public function addBusinessLog($data)
    {
        $this->getORM()->insert($data);
    }

    protected function getTableName($id)
    {
        return 'business_amount_record';
    }


}
