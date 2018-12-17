<?php namespace Nylas\Utilities;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Errors
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
 */
class Helper
{

    // ------------------------------------------------------------------------------

    /**
     * convert assoc array to multi
     *
     * @param array $arr
     * @return array
     */
    public static function arrayToMulti(array $arr) : array
    {
        return self::isAssoc($arr) ? [$arr] : $arr;
    }

    // ------------------------------------------------------------------------------

    /**
     * check if assoc array
     *
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr) : bool
    {
        foreach ($arr as $key => $value)
        {
            if (is_string($key)) { return true; }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * something foo to array
     *
     * @param $foo
     * @return array
     */
    public static function fooToArray($foo) : array
    {
        if ($foo === null) { return []; }

        $temp = is_array($foo) ? $foo : [$foo];

        return array_values(array_unique($temp));
    }

    // ------------------------------------------------------------------------------

    /**
     * pickup element from inside array
     *
     * @param  array  $data
     * @param  string $key
     * @return array
     */
    public static function generateArray(array $data, string $key = 'id') : array
    {
        $temp = [];

        if (!count($data)) { return $temp; }

        foreach ($data as $val)
        {
            if (empty($val[$key])) { continue; }

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
     * @return array
     */
    public static function concatPoolInfos(array $params, array $pools) : array
    {
        $data = [];

        foreach ($params as $index => $id)
        {
            $item = ['id' => $id];

            // check if get errors
            if (isset($pools[$index]['error']))
            {
                $item = array_merge($item, $pools[$index]);
            }

            $data[$id] = $item;
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

}
