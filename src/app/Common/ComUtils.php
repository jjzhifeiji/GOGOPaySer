<?php

/**
 * Created by PhpStorm.
 * UserModel: lijingzhe
 * Date: 2019/3/1
 * Time: 9:40 PM
 */

namespace App\Common;

use PhalApi\Tool;

class ComUtils
{


    /**
     * 判断手机号
     */
    public static function is_phone($mobile)
    {
        return preg_match("/^1[3456789]\d{9}$/", $mobile);
    }

    public static function findFiles()
    {
        $file_array = array();
        $fileList = scandir(RESOURCE_DIR . RECORD_RESOURCE);

        if (is_array($fileList)) {

            foreach ($fileList as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                if (is_dir(RESOURCE_DIR . RECORD_RESOURCE . '/' . $item)) {

                } else {
                    $file_array[] = $item;
                }
            }

        } else {

        }
        return $file_array;
    }

}
