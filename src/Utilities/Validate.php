<?php namespace Nylas\Utilities;

use Respect\Validation\Validator;
use Respect\Validation\Validatable;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul API List
 * ----------------------------------------------------------------------------------
 *
 * @method static Validator keySet(Validatable ...$rule)
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Validate extends Validator
{

    // ------------------------------------------------------------------------------

    /**
     * timestamp
     *
     * @return \Respect\Validation\Validator
     */
    public static function timestampType()
    {
        return static::intType()->min(strtotime('1971-1-1'));
    }

    // ------------------------------------------------------------------------------

}
