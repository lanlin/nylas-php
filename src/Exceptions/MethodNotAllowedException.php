<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Method Not Allowed
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class MethodNotAllowedException extends NylasException
{
    protected $code = 405;

    protected $message = 'You tried to access a resource with an invalid method.';
}
