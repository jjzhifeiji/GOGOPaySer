<?php

namespace Admin\Domain\Common;

use Admin\Common\BaseDomain;
use PhalApi\Tool;

class CommonDomain extends BaseDomain
{

    function getHomeData()
    {

//        userAlINumber://楼单豆人数userAllAmount:/账户余额userGrabAmount:1/ 佣金杀额userShareAmount:谁广佣金latformAlINumber:".I1A装platformCol: 代爱
//        1/代行platformPayment:/今日下发platformOut:00all present:
//tableData:[checkAmount:
//payalipercent: 0
//paypercent:0.
//paywxpercent:0
//hour:{ //每小时统计图表
//        allamount:dateTime:
//        days:{// 日统计图表
//            dayswxAmount:daysaliAmount:daysyunAmount:daysbankAmount:dateTime:
//            weeks:部统计国表
//weekswxAmount:weeksaliAmount:weeksyunAmount:weeksbankAmount:dateTime:
//months: / 月统计图表
//monthwxAmount:monthaliAmount:monthyunAmount:monthbankAmount:dateTime:


        $checkAmount['payalipercent'] = 123;
        $checkAmount['paypercent'] = 43240;
        $checkAmount['paywxpercent'] = 63450;

        $hour['allamount'] = array();
        $hour['dateTime'] = array();

        $days['dayswxAmount'] = array();
        $days['daysaliAmount'] = array();
        $days['daysyunAmount'] = array();
        $days['daysbankAmount'] = array();
        $days['dateTime'] = array();

        $weeks['weekswxAmount'] = array();
        $weeks['weeksaliAmount'] = array();
        $weeks['weeksyunAmount'] = array();
        $weeks['weeksbankAmount'] = array();
        $weeks['dateTime'] = array();

        $months['monthwxAmount'] = array();
        $months['monthaliAmount'] = array();
        $months['monthyunAmount'] = array();
        $months['monthbankAmount'] = array();
        $months['dateTime'] = array();

        $res = array(
            'userAllNumber' => 123,
            'userAllAmount' => 321,
            'userGrabAmount' => 345,
            'userShareAmount' => 456,
            'latformAllNumber' => 567,
            'platformPayment' => 678,
            'platformOut' => 789,
            'all_present' => 96784,
            'tableData' => array(),
            'checkAmount' => $checkAmount,
            'hour' => $hour,
            'days' => $days,
            'weeks' => $weeks,
            'months' => $months,
            'platformCol' => ''
        );


        return $res;
    }


}
