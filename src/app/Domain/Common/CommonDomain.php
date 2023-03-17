<?php

namespace App\Domain\Common;

use App\Common\BaseDomain;
use PhalApi\Tool;

/**
 * 公共数据
 */
class CommonDomain extends BaseDomain
{

    public function checkVersion()
    {
        $file = array(
            'status' => 1,
            'type' => 1,
        );
        return $this->_getVersionModel()->getVersion($file);
    }

    public function getsReasons()
    {
        return $this->_getReasonsModel()->getsReasons();
    }


    public function addRecord($res_id, $mobile, $user_id, $connect_time, $record_path)
    {
        $file = array(
            'res_id' => $res_id,
            'mobile' => $mobile,
            'user_id' => $user_id,
            'create_time' => date('Y-m-d H:i:s'),
            'status' => 1,
            'type' => 1,
            'connect_time' => $connect_time,
            'record_path' => $record_path,
            'remark' => ''
        );

        return $this->_getAudioRecordModel()->addRecord($file);
    }

}
