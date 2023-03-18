<?php

namespace Admin\Model\Admin;

use Admin\Common\BaseModel;

class AdminModel extends BaseModel
{

    protected function getTableName($id)
    {
        return 'admin';
    }

    public function getAdminAccount($account)
    {
        return $this->getORM()->select('*')->where('account', $account)->where('status', 1)->fetchOne();
    }

    public function getAdminId($id)
    {
        return $this->getORM()->where('id', $id)->fetchOne();
    }

}
