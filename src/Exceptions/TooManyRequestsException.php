<?php

declare(strict_types = 1);

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Too Many Requests
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class TooManyRequestsException extends NylasException
{
    protected $code = 429;

    protected $message = 'Slow down! (If you legitimately require this many requests, please contact support.)';
}
