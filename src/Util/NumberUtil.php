<?php
namespace Common\Component\Util;

/**
 * Class NumberUtil
 * 数字相关操作工具类  转换一些数字格式
 * @package Dahua\Common\Util
 */
class NumberUtil
{
    /**
     * 四舍五入后保留小数点后至多2位小数 demo
     * 1.20 为1.2
     * 1.234 为 1.23
     * 1.236 为 1.24
     * @param $number
     * @return string
     */
    public static function converDecimal($number)
    {
        return (string)floatval(round($number, 2));
    }

    /**
     * 四舍五入后保留n位小数 demo
     * 1.20 为 1.20
     * 1.245 为 1.25
     * 1 为 1.00
     * @param $number
     * @return string
     */
    public static function converFormatNum($number, $precision)
    {
        return (string)sprintf("%.".$precision."f", round($number, 2));
    }
}
