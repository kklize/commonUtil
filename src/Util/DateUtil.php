<?php
namespace Common\Component\Util;

/**
 * Class DateUtil
 * 日期工具类
 * @package Common\Component\Util
 */
class DateUtil
{
    /**
     * @param string $startDate
     * 开始时间 demo 2019-08-29
     * @param string $endDate
     * 结束时间 demo 2019-09-20
     *
     * @return array
     */
    public static function getDateFromRange(string $startDate, string $endDate):array
    {
        $startdate = date('Y-m-d 00:00:00', strtotime($startDate));
        $enddate = date('Y-m-d 23:59:59', strtotime($endDate));

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        if ($etimestamp < $stimestamp) {
            return [];
        }
        // 计算日期段内有多少天
        $days = (int)ceil(($etimestamp - $stimestamp) / 86400);
        // 保存每天日期
        $date = [];
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }
        $data = [
            'dates' => $date,
            'days' => $days,
        ];
        return $data;
    }
}
