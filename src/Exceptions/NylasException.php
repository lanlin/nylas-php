<?php

namespace Nylas\Exceptions;

use Exception;

/**
 * ----------------------------------------------------------------------------------
 * NylasException
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class NylasException extends Exception
{
    protected $code = 999;

    protected $message = 'some issue found when calling nylas.';
}
