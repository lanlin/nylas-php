<?php namespace Nylas\Utilities;

use Respect\Validation\Validator;
use Respect\Validation\Validatable;
use Nylas\Exceptions\NylasException;
use Respect\Validation\Exceptions\NestedValidationException;

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
        return self::intType()->min(strtotime('1971-1-1'));
    }

    // ------------------------------------------------------------------------------

    /**
     * optional key
     *
     * @param string                          $reference
     * @param \Respect\Validation\Validatable $referenceValidator
     * @return \Respect\Validation\Validator
     */
    public static function keyOptional(string $reference, Validatable $referenceValidator)
    {
        $rules = self::oneOf(self::nullType(), $referenceValidator);

        return self::key($reference, $rules, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * nylas doing validate
     *
     * @param Validatable $validatable
     * @param mixed $input
     * @return bool
     * @throws \Nylas\Exceptions\NylasException
     */
    public static function doValidate(Validatable $validatable, $input)
    {
        try
        {
            return $validatable->assert($input);
        }
        catch (NestedValidationException $e)
        {
            throw new NylasException($e->getFullMessage());
        }
    }

    // ------------------------------------------------------------------------------

}
