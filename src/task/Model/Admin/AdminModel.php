<?php

namespace Task\Model\Admin;
use Task\Common\BaseModel;

class AdminModel extends BaseModel
{

    protected function getTableName($id)
    {
        return 'admin';
    }

    public function getAdminAccount($account)
    {
        return $this->getORM()->select('*')->where('account = ?', $account)->fetchOne();
    }

}
