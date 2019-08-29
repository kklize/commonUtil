<?php
namespace Common\Component\Util;

/**
 * Class ArrUtil
 * 数组相关操作公共工具方法
 * @package common
 */
class ArrUtil
{
    /**
     * 二维数组根据某个元素去重
     * @return array
     */
    public static function arrayUnsetTt($arr, $key)
    {
        $res = [];
        foreach ($arr as $value) {
            if (isset($res[$value[$key]])) {
                unset($value[$key]);
            } else {
                $res[$value[$key]] = $value;
            }
        }
        rsort($res);
        return $res;
    }

    /**
     * 二维数组 根据某个key值进行排序
     * @return array
     */
    public static function arraySort($arr, $key, $sortType = SORT_ASC)
    {
        $descArr = array_column($arr, $key);
        array_multisort($descArr, $sortType, $arr);
        return $arr;
    }

    /**
     * 二维数组根据某个key值相同 归类
     * @return array
     */
    public static function arrayGroupSame($arr, $key)
    {
        $newArr = [];
        foreach ($arr as $k => $v) {
            $newArr[$v[$key]][] = $v;
        }
        return $newArr;
    }

    /**
     * 二维数组 根据某些key 进行数值归类 ,默认去重
     * @param $arr
     * @param array $key_arr
     * @param bool $boolean
     * @return array
     * demo
     * old arr
     * $old_arr = [
     * [
     * 'user_id' => 1,
     * "name"=>'test',
     * "te" => "123"
     * ],
     * [
     * 'user_id' => 1,
     * "name"=>'test111'
     * ],[
     * 'user_id' => 2,
     * "name"=>'test111'
     * ],
     * ];
     *
     * new arr
     * $new_arr = [
     * "user_id" => [1,2],
     * "name" => ["test","test111"],
     * "te" => ["123"]
     * ];
     *
     */
    public static function arrayUnColumn($arr, $keyArr = [], $boolean = true)
    {
        $newArr = [];
        foreach ($keyArr as $v) {
            if ($boolean === true) {
                $newArr[$v] = array_values(array_unique(array_column($arr, $v)));
            } else {
                $newArr[$v] = array_values(array_column($arr, $v));
            }
        }
        return $newArr;
    }
}
