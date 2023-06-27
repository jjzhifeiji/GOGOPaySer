<?php

namespace Proxy\Model\Sys;

use Proxy\Common\BaseModel;

class SystemModel extends BaseModel
{


    protected function getTableName($id)
    {
        return 'sys_config';
    }

    public function getAutoAssign()
    {
        return $this->getORM()->where('config_key', 'auto_assign')->fetchOne();
    }


}
