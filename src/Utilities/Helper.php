<?php namespace Nylas\Utilities;


/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Errors
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class Helper
{

    // ------------------------------------------------------------------------------

    /**
     * check if assoc array
     *
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr)
    {
        foreach ($arr as $key => $value)
        {
            if (is_string($key)) { return true; }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * convert assoc array to multi
     *
     * @param array $arr
     * @return array
     */
    public static function arrayToMulti(array $arr)
    {
        return self::isAssoc($arr) ? [$arr] : $arr;
    }

    // ------------------------------------------------------------------------------

}
