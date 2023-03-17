<?php

namespace App\Model\Setting;

use App\Common\BaseModel;

class VersionModel extends BaseModel
{

    public function getVersion($file)
    {
        return $this->getORM()->where($file)->fetchOne();
    }

    protected function getTableName($id)
    {
        return 'version';
    }


}
