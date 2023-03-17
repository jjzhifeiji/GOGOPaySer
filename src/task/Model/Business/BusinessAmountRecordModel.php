<?php

namespace Task\Model\Business;

use Task\Common\BaseModel;

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
