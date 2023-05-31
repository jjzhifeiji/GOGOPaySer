<?php

namespace Admin\Model\Module;


use Admin\Common\BaseDomain;

class FiltrationAPI extends BaseDomain
{

    /**
     * @param $mobile
     * {
     * "code": 200,
     * "message": "处理成功",
     * "data": true
     * }
     */
    public function phoneInquire($mobile)
    {

        // 先实例
        $curl = new \PhalApi\CUrl();
        $url = 'http://api.filtration.benpaodewanzi2022.xyz/index.php/Resource_ResourceController.checkMobileInfo?mobile=' . $mobile . '&model=2';
        $rs = $curl->get($url, 3000);

        $res = json_decode($rs, true);
        if ($res['ret'] == 200) {
            \PhalApi\DI()->logger->info('过滤1 ->', $res['data']);
            \PhalApi\DI()->logger->info('过滤2 ->', $res['data']['result']);
            if ($res['data']['result'] == 1) return 1; else return 0;
        } else {
            \PhalApi\DI()->logger->error('过滤3 ->', $rs);
            \PhalApi\DI()->logger->error('过滤4 ->', $res);
            \PhalApi\DI()->logger->error('API 错误 ' . $mobile . ' -> ', $rs);
            return 2;
        }
    }

    /**
     */
    public function pushUrl($url, $data)
    {
        $curl = new \PhalApi\CUrl();
        $rs = $curl->post($url, $data, 5000);
        $res = json_decode($rs, true);
        \PhalApi\DI()->logger->debug('推送 ->', $rs);
        \PhalApi\DI()->logger->debug('推送 ->', $res);
    }


}
