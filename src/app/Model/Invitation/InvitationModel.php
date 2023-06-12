<?php

namespace App\Model\Invitation;

use App\Common\BaseModel;

class InvitationModel extends BaseModel
{


    public function getMyInvitation(array $file)
    {
        return $this->getORM()->where($file)->fetchOne();
    }

    public function getMyInvitationList(array $file)
    {
        return $this->getORM()->where($file)->fetchAll();
    }

    public function setMyInvitation(array $data)
    {
        return $this->getORM()->insert($data);
    }

    public function delMyInvitation(array $file)
    {
        return $this->getORM()->where($file)->update(array('status' => 0));
    }


    protected function getTableName($id)
    {
        return 'invitation';
    }

}
