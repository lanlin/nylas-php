<?php namespace Nylas\Utilities;

use Nylas\Exceptions\NylasException;
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

    /**
     * optional key
     *
     * @param string                               $reference
     * @param \Respect\Validation\Validatable|NULL $referenceValidator
     * @return \Respect\Validation\Validator
     */
    public static function keyOptional(string $reference, Validatable $referenceValidator = null)
    {
        $rules = static::oneOf(static::nullType(), $referenceValidator);

        return static::key($reference, $rules, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * nylas params
     *
     * @param mixed $input
     * @return bool
     * @throws \Nylas\Exceptions\NylasException
     */
    public function assert($input)
    {
        if (is_array($input))
        {
            foreach ($input as &$val)
            {
                if (is_string($val) && empty($val))
                {
                    $val = null;
                }
            }
        }

        try
        {
            return parent::assert($input);
        }
        catch (\Exception $e)
        {
            throw new NylasException($e->getMessage(), $e->getCode(), $e);
        }
    }

    // ------------------------------------------------------------------------------

}
