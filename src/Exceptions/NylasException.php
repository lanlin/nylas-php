<?php namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * NylasException
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class NylasException extends \Exception
{
    protected $code = 999;

    protected $message = 'some issue found when calling nylas.';
}