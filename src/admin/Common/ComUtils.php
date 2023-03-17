<?php

/**
 * Created by PhpStorm.
 * UserModel: lijingzhe
 * Date: 2019/3/1
 * Time: 9:40 PM
 */

namespace Admin\Common;

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

    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * @param unknown $idcard_base
     * @return boolean|string
     */
    public static function idcard_verify_number($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 将15位身份证升级到18位
     */
    function idcard_15to18($idcard)
    {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . idcard_verify_number($idcard);
        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     * @param unknown $idcard
     * @return boolean
     */
    function idcard_checksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    function compress($file)
    {
        $image = imagecreatefrompng($file);
        $img_info = getimagesize($file);
        $com_image = imagecreatetruecolor($img_info[0], $img_info[1]);
        imagecopyresampled($com_image, $image, 0, 0, 0, 0, $img_info[0], $img_info[1], $img_info[0], $img_info[1]);
        return $com_image;
    }

    /**
     * desription 压缩图片
     * @param string $imgsrc 图片路径
     * @param string $imgdst 压缩后保存路径
     */
    public static function compressedImage($imgsrc, $imgdst)
    {
        list($width, $height, $type) = getimagesize($imgsrc);

        $new_width = $width;//压缩后的图片宽
        $new_height = $height;//压缩后的图片高

        if ($width >= 600) {
            $per = 600 / $width;//计算比例
            $new_width = $width * $per;
            $new_height = $height * $per;
        }

        $quality = 40;
        switch ($type) {
            case 1:
                $giftype = check_gifcartoon($imgsrc);
                if ($giftype) {
                    header('Content-Type:image/gif');
                    $image_wp = imagecreatetruecolor($new_width, $new_height);
                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    //90代表的是质量、压缩图片容量大小
                    imagejpeg($image_wp, $imgdst, $quality);
                    imagedestroy($image_wp);
                    imagedestroy($image);
                }
                break;
            case 2:
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, $quality);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
            case 3:
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, $quality);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
        }
    }

}
