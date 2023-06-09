<?php

namespace Task\Model\Sys;

use Task\Common\BaseModel;

class SystemModel extends BaseModel
{


    protected function getTableName($id)
    {
        return 'sys_config';
    }

    public function getAutoAssign()
    {
        return $this->getORM()->select('config_value')->where('config_key', 'auto_assign')->fetchOne();
    }


}
