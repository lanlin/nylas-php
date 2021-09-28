<?php

namespace Nylas\Utilities;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Utils Helper
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
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
        return self::isAssoc($arr) ? [$arr] : $arr;
    }

    // ------------------------------------------------------------------------------

    /**
     * check if assoc array
     *
     * @param array $arr
     *
     * @return bool
     */
    public static function isAssoc(array $arr): bool
    {
        foreach ($arr as $key => $value)
        {
            if (!\is_int($key))
            {
                return true;
            }
        }

        return false;
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

        $temp = \is_array($foo) ? $foo : [$foo];

        return \array_values(\array_unique($temp));
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
            if (\is_bool($val))
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

        if (!\count($data))
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
                $item = \array_merge($item, $pools[$index]);
            }

            $data[$id] = $item;
        }

        return $data;
    }

    // ------------------------------------------------------------------------------
}
