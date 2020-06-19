<?php

namespace Nylas\Exceptions;

/**
 * ----------------------------------------------------------------------------------
 * Too Many Requests
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class TooManyRequestsException extends NylasException
{
    protected $code = 429;

    protected $message = 'Slow down! (If you legitimately require this many requests, please contact support.)';
}
