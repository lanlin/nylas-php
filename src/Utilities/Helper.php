<?php

declare(strict_types = 1);

namespace Nylas\Utilities;

use function end;
use function key;
use function count;
use function reset;
use function is_int;
use function is_bool;
use function is_array;
use function array_unique;
use function array_values;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Utils Helper
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Helper
{
    // ------------------------------------------------------------------------------

    /**
     * convert assoc array to multi
     *
     * @param array $arr
     *
     * @return array
     */
    public static function arrayToMulti(array $arr): array
    {
        if (count($arr) === 0)
        {
            return $arr;
        }

        return self::isAssoc($arr) ? [$arr] : $arr;
    }

    // ------------------------------------------------------------------------------

    /**
     * something foo to array
     *
     * @param mixed $foo
     *
     * @return array
     */
    public static function fooToArray(mixed $foo): array
    {
        if (!$foo)
        {
            return [];
        }

        $temp = is_array($foo) ? $foo : [$foo];

        return array_values(array_unique($temp));
    }

    // ------------------------------------------------------------------------------

    /**
     * convert boolean to string value
     *
     * @param array $data
     *
     * @return array
     */
    public static function boolToString(array $data): array
    {
        foreach ($data as $key => $val)
        {
            if (is_bool($val))
            {
                $data[$key] = $val ? 'true' : 'false';
            }
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * pickup element from inside array
     *
     * @param array  $data
     * @param string $key
     *
     * @return array
     */
    public static function generateArray(array $data, string $key = 'id'): array
    {
        $temp = [];

        if (!count($data))
        {
            return $temp;
        }

        foreach ($data as $val)
        {
            if (empty($val[$key]))
            {
                continue;
            }

            $temp[] = $val[$key];
        }

        return $temp;
    }

    // ------------------------------------------------------------------------------

    /**
     * concat pool return infos
     *
     * @param array $params
     * @param array $pools
     *
     * @return array
     */
    public static function concatPoolInfos(array $params, array $pools): array
    {
        $data = [];

        foreach ($params as $index => $id)
        {
            $item = ['id' => $id];

            // merge with pool data
            if (isset($pools[$index]))
            {
                $item = self::loopMerge($item, $pools[$index]);
            }

            $data[$id] = $item;
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * for replace the \array_merge($arrA, $arrB) when loop & merge array
     *
     * @param array $arrA
     * @param array $arrB
     *
     * @return array
     */
    public static function loopMerge(array $arrA, array $arrB): array
    {
        if (count($arrA) === 0)
        {
            return $arrB;
        }

        if (count($arrB) === 0)
        {
            return $arrA;
        }

        // for associative array
        if (self::isAssoc($arrA))
        {
            return $arrB + $arrA;
        }

        // for sequential array
        foreach ($arrB as $val)
        {
            $arrA[] = $val;
        }

        return $arrA;
    }

    // ------------------------------------------------------------------------------

    /**
     * check if an assoc array
     *
     * @param array $arr
     *
     * @return bool
     */
    public static function isAssoc(array $arr): bool
    {
        if (count($arr) === 0)
        {
            return false;
        }

        if (!is_int(key($arr)))
        {
            return true;
        }

        end($arr);

        if (!is_int(key($arr)))
        {
            return true;
        }

        reset($arr);

        foreach ($arr as $key => $val)
        {
            if (!is_int($key))
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------
}
